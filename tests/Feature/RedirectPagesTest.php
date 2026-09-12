<?php

namespace Tests\Feature;

use App\Models\Page;
use Tests\TestCase;

class RedirectPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        config(['handoff.target_host' => 'http://squawk.agepac.org.test']);
    }

    /** @test */
    public function testUnrestrictedPagesRedirectToThePublicSite()
    {
        $page = Page::factory()->create(['restricted' => false]);

        $this->get(route('pages.show', $page))
            ->assertStatus(302)
            ->assertRedirect(config('app.public_site_url')."/pages/{$page->path}");
    }

    /** @test */
    public function testMembersAreHandedOffToRestrictedPagesOnTheMembersApp()
    {
        $this->signIn();

        $page = Page::factory()->create(['restricted' => true]);

        $location = $this->get(route('pages.show', $page))
            ->assertStatus(302)
            ->headers->get('Location');

        $this->assertStringStartsWith('http://squawk.agepac.org.test/handoff?', $location);
        $this->assertStringContainsString('target='.urlencode("/pages/{$page->path}"), $location);
        $this->assertStringContainsString('signature=', $location);
    }

    /** @test */
    public function testGuestsMustLogInBeforeBeingHandedOffToRestrictedPages()
    {
        $this->get(route('pages.show', Page::factory()->create(['restricted' => true])))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUnsubscribedMembersAreHandedOffForTheNewAppToAuthorize()
    {
        $this->signInUnsubscribed();

        $this->get(route('pages.show', Page::factory()->create(['restricted' => true])))
            ->assertStatus(302)
            ->assertRedirectContains('/handoff');
    }

    /** @test */
    public function testSlashedPathsRedirectIntact()
    {
        $page = Page::factory()->create(['restricted' => false, 'path' => 'guides/orientation']);

        $this->get(route('pages.show', $page))
            ->assertRedirect(config('app.public_site_url').'/pages/guides/orientation');
    }

    /** @test */
    public function testUnpublishedPagesRedirectForTheNewAppToHandle()
    {
        $this->get(route('pages.show', Page::factory()->create(['published_at' => null])))
            ->assertStatus(302);

        $this->get(route('pages.show', Page::factory()->create(['published_at' => now()->addYear()])))
            ->assertStatus(302);
    }

    /** @test */
    public function testUnknownPagesAreNotFound()
    {
        $this->get('/pages/missing')
            ->assertNotFound();
    }

    /** @test */
    public function testTrashedPagesAreNotFound()
    {
        $page = tap(Page::factory()->create())->delete();

        $this->get(route('pages.show', $page))
            ->assertNotFound();
    }
}
