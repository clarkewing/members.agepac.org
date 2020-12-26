<?php

namespace Tests\Feature\Nova;

use App\Models\MentorshipTag;
use App\Models\Profile;
use Tests\NovaTestRequests;
use Tests\TestCase;

class MergeMentorshipTagsTest extends TestCase
{
    use NovaTestRequests, MergesModels;

    protected function setUp(): void
    {
        parent::setUp();

        MentorshipTag::create(['name' => 'Foo']);
        MentorshipTag::create(['name' => 'Bar']);
    }

    /** @test */
    public function testMentorshipTagsResourceHasMergeAction()
    {
        $this->signInWithPermission('mentorship_tags.merge');

        $this->listResourceActions('mentorship-tags')
            ->assertJsonFragment(['name' => 'Merge']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotMergeMentorshipTags()
    {
        $this->signInWithPermission('mentorship_tags.delete');

        $this->performMerge('mentorship-tags', [1, 2])
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanMergeMentorshipTags()
    {
        $this->signInWithPermission('mentorship_tags.merge');

        $this->performMerge('mentorship-tags', [1, 2])
            ->assertJsonMissing(['danger' => 'Sorry! You are not authorized to perform this action.'])
            ->assertSuccessful();
    }

    /** @test */
    public function testTwoMentorshipTagsAssociatedToTheSameProfileCanBeMerged()
    {
        $this->signInWithPermission('mentorship_tags.merge');

        $profile = Profile::factory()->create();
        $profile->attachTags(MentorshipTag::all());

        $this->performMerge('mentorship-tags', [1, 2])
            ->assertJson(['message' => 'Models merged successfully.'])
            ->assertSuccessful();
    }
}
