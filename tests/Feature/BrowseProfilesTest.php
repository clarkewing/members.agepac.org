<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BrowseProfilesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testGuestCannotSearchProfiles()
    {
        Auth::logout();

        $this->getJson(route('profiles.index'))
            ->assertUnauthorized();
    }

    /** @test */
    public function testUserCanGetIndexOfProfiles()
    {
        $profiles = Profile::factory()->count(10)->create();

        $this->get(route('profiles.index'))
            ->assertViewIs('profiles.index');

        $this->getJson(route('profiles.index'))
            ->assertJson(['data' => $profiles->only('id')->all()]);
    }

    /**
     * @test
     * @group external-api
     * @group algolia-api
     */
    public function testUserCanSearchProfiles()
    {
        if (! config('scout.algolia.id')) {
            $this->markTestSkipped('Algolia is not configured.');
        }

        config(['scout.driver' => 'algolia']);

        $search = 'Didier';

        Profile::factory()->count(2)->create();
        Profile::factory()->create(['first_name' => $search, 'last_name' => 'Labyt']);
        Profile::factory()->create(['first_name' => $search, 'last_name' => 'Raoult']);

        $maxTime = now()->addSeconds(20);

        do {
            sleep(.25);

            $results = $this->getJson(route('profiles.index', ['query' => $search]))->json()['data'];
        } while (empty($results) && now()->lessThan($maxTime));

        $this->assertCount(2, $results);

        // Clean up index.
        Profile::latest()->take(4)->unsearchable();
    }

    /**
     * Show the requested profile.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Testing\TestResponse
     */
    protected function showProfile(Profile $profile = null)
    {
        return $this->getJson(route(
            'profiles.show',
            $profile ?? Profile::factory()->create()
        ));
    }
}
