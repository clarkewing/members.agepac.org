<?php

namespace Tests\Feature;

use App\Page;
use Tests\TestCase;

class ReadPagesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testGuestsCanViewUnrestrictedPages()
    {
        $this->get(route('pages.show', create(Page::class, ['restricted' => false])))
            ->assertOk();
    }

    /** @test */
    public function testGuestsCannotViewRestrictedPages()
    {
        $this->get(route('pages.show', create(Page::class, ['restricted' => true])))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUsersCanViewRestrictedPages()
    {
        $this->signIn();

        $this->get(route('pages.show', create(Page::class, ['restricted' => true])))
            ->assertOk();
    }

    /** @test */
    public function testGuestsCannotViewUnpublishedPages()
    {
        $this->get(route('pages.show', create(Page::class, ['published_at' => null])))
            ->assertForbidden();

        $this->get(route('pages.show', create(Page::class, ['published_at' => now()->addYear()])))
            ->assertForbidden();
    }

    /** @test */
    public function testRegularUsersCannotViewUnpublishedPages()
    {
        $this->signIn();

        $this->get(route('pages.show', create(Page::class, ['published_at' => null])))
            ->assertForbidden();

        $this->get(route('pages.show', create(Page::class, ['published_at' => now()->addYear()])))
            ->assertForbidden();
    }

    /** @test */
    public function testAdminsCanViewUnpublishedPages()
    {
        $this->signInAdmin();

        $this->get(route('pages.show', create(Page::class, ['published_at' => null])))
            ->assertOk();

        $this->get(route('pages.show', create(Page::class, ['published_at' => now()->addYear()])))
            ->assertOk();
    }

    /** @test */
    public function testCanViewPage()
    {
        $page = create(Page::class);

        $this->get(route('pages.show', $page))
            ->assertSee($page->title)
            ->assertSee($page->body, false);
    }
}
