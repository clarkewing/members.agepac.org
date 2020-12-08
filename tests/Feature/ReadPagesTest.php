<?php

namespace Tests\Feature;

use App\Models\Page;
use Tests\TestCase;

class ReadPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testGuestsCanViewUnrestrictedPages()
    {
        $this->get(route('pages.show', Page::factory()->create(['restricted' => false])))
            ->assertOk();
    }

    /** @test */
    public function testGuestsCannotViewRestrictedPages()
    {
        $this->get(route('pages.show', Page::factory()->create(['restricted' => true])))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUsersCanViewRestrictedPages()
    {
        $this->signIn();

        $this->get(route('pages.show', Page::factory()->create(['restricted' => true])))
            ->assertOk();
    }

    /** @test */
    public function testGuestsCannotViewUnpublishedPages()
    {
        $this->get(route('pages.show', Page::factory()->create(['published_at' => null])))
            ->assertForbidden();

        $this->get(route('pages.show', Page::factory()->create(['published_at' => now()->addYear()])))
            ->assertForbidden();
    }

    /** @test */
    public function testRegularUsersCannotViewUnpublishedPages()
    {
        $this->signIn();

        $this->get(route('pages.show', Page::factory()->create(['published_at' => null])))
            ->assertForbidden();

        $this->get(route('pages.show', Page::factory()->create(['published_at' => now()->addYear()])))
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanViewUnpublishedPages()
    {
        $this->signInWithPermission('pages.viewUnpublished');

        $this->get(route('pages.show', Page::factory()->create(['published_at' => null])))
            ->assertOk();

        $this->get(route('pages.show', Page::factory()->create(['published_at' => now()->addYear()])))
            ->assertOk();
    }

    /** @test */
    public function testCanViewPage()
    {
        $page = Page::factory()->create();

        $this->get(route('pages.show', $page))
            ->assertSee($page->title)
            ->assertSee($page->body, false);
    }
}
