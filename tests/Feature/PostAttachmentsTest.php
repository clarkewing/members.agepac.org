<?php

namespace Tests\Feature;

use App\Attachment;
use App\Post;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostAttachmentsTest extends TestCase
{
    /**
     * @var \App\Post
     */
    protected $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        Storage::fake('public');

        $this->post = create(Post::class);
    }

    /** @test */
    public function testGuestsCannotUploadAnAttachment()
    {
        $this->uploadAttachment()->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertCount(0, $this->post->attachments);

        $this->assertEmpty(Storage::disk('public')->allFiles('attachments'));
    }

    /** @test */
    public function testUnverifiedUsersCannotUploadAnAttachment()
    {
        $this->signIn(factory(User::class)->states('unverified_email')->create());

        $this->uploadAttachment()->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(0, $this->post->attachments);

        $this->assertEmpty(Storage::disk('public')->allFiles('attachments'));
    }

    /** @test */
    public function testAuthenticatedUserCanUploadAnAttachment()
    {
        $this->signIn();

        ($response = $this->uploadAttachment())->assertCreated();

        $this->assertEquals(1, Attachment::count());

        Storage::disk('public')->assertExists(($attachment = Attachment::first())->path);

        $this->assertEquals($attachment->id, $response->json('id'));
        $this->assertEquals($attachment->path, $response->json('path'));
    }

    /** @test */
    public function testPreviouslyUploadedAttachmentsAreAssociatedWithStoredPost()
    {
        // We need a thread to post to.
        $thread = create(Thread::class);

        // The user uploads an  attachment while typing up a post.
        ($attachmentResponse = $this->signIn()->uploadAttachment([
            'file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]))->assertCreated();

        $this->assertEquals(1, Attachment::where('post_id', null)->count());

        // The user submits the post.
        $postResponse = $this->postJson(route('posts.store', [$thread->channel, $thread]), [
            'body' => '<figure data-trix-attachment="'
                      .htmlentities(json_encode([
                          'contentType' => 'application/pdf',
                          'filename' => 'document.pdf',
                          'filesize' => 1000,
                          'id' => $attachmentResponse->json('id'),
                          'href' => '/storage/'.$attachmentResponse->json('path'),
                          'url' => '/storage/'.$attachmentResponse->json('path'),
                      ]))
                      .'" class="attachment attachment--file attachment--pdf"></figure>',
        ]);

        $this->assertEquals(0, Attachment::where('post_id', null)->count());
        $this->assertCount(1, Post::find($postResponse->json('id'))->attachments);
    }

    /** @test */
    public function testPostCreatorCanAddAnAttachmentToTheirPost()
    {
        $this->signIn($this->post->owner);
        $this->assertCount(0, $this->post->attachments);

        $response = $this->uploadAttachment();

        $this->patchJson(route('posts.update', $this->post), [
            'body' => '<figure data-trix-attachment="'
                      .htmlentities(json_encode([
                          'contentType' => 'application/pdf',
                          'filename' => 'document.pdf',
                          'filesize' => 1000,
                          'id' => $response->json('id'),
                          'href' => '/storage/'.$response->json('path'),
                          'url' => '/storage/'.$response->json('path'),
                      ]))
                      .'" class="attachment attachment--file attachment--pdf"></figure>',
        ]);

        $this->assertCount(1, $this->post->fresh()->attachments);
    }

    /** @test */
    public function testPostCreatorCanDeleteAnAttachmentFromTheirPost()
    {
        $this->signIn();
        $post = factory(Post::class)->state('with_attachment')->create(['user_id' => Auth::id()]);
        $this->assertCount(1, $attachments = $post->attachments);

        $this->patchJson(route('posts.update', $post), [
            'body' => 'Poof! No more attachment.',
        ])->assertOk();

        $this->assertCount(0, $post->fresh()->attachments);

        Storage::disk('public')->assertMissing($attachments->first()->path);
    }

    /** @test */
    public function testWhenPostIsDeletedAllAssociatedAttachmentsAreDeleted()
    {
        $post = factory(Post::class)->state('with_attachment')->create();
        $attachment = $post->attachments->first();

        $post->delete();

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);

        Storage::disk('public')->assertMissing($attachment->path);
    }

    /** @test */
    public function testAnUploadedAttachmentThatHasNotBeenReconciledIsDeletedAfterTwoHours()
    {
        $this->signIn()->uploadAttachment();

        $this->assertEquals(1, Attachment::count());

        // Time travel 2 hours and 1 minute into the future.
        Carbon::setTestNow(now()->addHours(2)->addMinute());

        $this->artisan('attachment:cleanup');

        $this->assertEquals(0, Attachment::count());
    }

    /**
     * Create a post request to upload an attachment.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    protected function uploadAttachment(array $overrides = [])
    {
        $data = array_merge([
            'file' => $file = UploadedFile::fake()->image('foo.jpg'),
        ], $overrides);

        return $this->postJson(
            route('attachments.store'),
            $data
        );
    }
}
