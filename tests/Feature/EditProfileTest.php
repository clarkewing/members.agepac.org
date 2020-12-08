<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    /**
     * @var App\Profile
     */
    public $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profile = Profile::factory()->create();

        $this->withExceptionHandling()->signIn($this->profile);
    }

    /** @test */
    public function testGuestsCannotUpdateProfile()
    {
        Auth::logout();

        $this->updateProfile([], Profile::factory()->create())
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function testUsersCanOnlyUpdateTheirOwnProfile()
    {
        $otherProfile = Profile::factory()->create();

        $this->updateProfile([], $otherProfile)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function testFlightHoursMustBeInteger()
    {
        $this->updateProfile(['flight_hours' => 'foo'])
            ->assertJsonValidationErrors('flight_hours');
    }

    /** @test */
    public function testFlightHoursMustBeEqualOrGreaterThanZero()
    {
        $this->updateProfile(['flight_hours' => -1])
            ->assertJsonValidationErrors('flight_hours');
    }

    /** @test */
    public function testFlightHoursMustBeLessThan16777215()
    {
        $this->updateProfile(['flight_hours' => 16777216])
            ->assertJsonValidationErrors('flight_hours');
    }

    /** @test */
    public function testFlightHoursCanBeNull()
    {
        $this->updateProfile(['flight_hours' => null])
            ->assertOk()
            ->assertJson(['flight_hours' => null]);

        $this->assertNull($this->profile->fresh()->flight_hours);
    }

    /** @test */
    public function testCanUpdateFlightHours()
    {
        $this->updateProfile(['flight_hours' => 150])
            ->assertOk()
            ->assertJson(['flight_hours' => 150]);

        $this->assertEquals(150, $this->profile->fresh()->flight_hours);

        $this->updateProfile(['flight_hours' => 450])
            ->assertOk()
            ->assertJson(['flight_hours' => 450]);

        $this->assertEquals(450, $this->profile->fresh()->flight_hours);
    }

    /** @test */
    public function testLocationMustBeValid()
    {
        $this->updateProfile(['location' => 'foobar'])
            ->assertJsonValidationErrors('location');

        $this->updateProfile(['location' => []])
            ->assertJsonValidationErrors('location');
    }

    /** @test */
    public function testLocationCanBeNull()
    {
        $this->assertNotNull($this->profile->location);

        $this->updateProfile(['location' => null])
            ->assertOk()
            ->assertJsonMissing(['location']);

        $this->assertNull($this->profile->fresh()->location);
    }

    /** @test */
    public function testCanUpdateLocation()
    {
        $location = [
            'type' => 'city',
            'name' => 'Paris, France',
            'street_line_1' => null,
            'street_line_2' => null,
            'municipality' => 'Paris',
            'administrative_area' => 'Île-de-France',
            'sub_administrative_area' => 'France',
            'postal_code' => '75000',
            'country' => 'France',
            'country_code' => 'FR',
        ];

        $this->updateProfile(['location' => $location])
            ->assertOk()
            ->assertJson(['location' => $location]);

        foreach ($location as $field => $value) {
            $this->assertEquals($value, $this->profile->fresh()->location->$field);
        }
    }

    /** @test */
    public function testTagsMustBeArray()
    {
        $this->updateProfile(['mentorship_tags' => 'a string tag'])
            ->assertJsonValidationErrors('mentorship_tags');
    }

    /** @test */
    public function testCanSetProfileTags()
    {
        $this->updateProfile(['mentorship_tags' => ['first tag', 'second tag']])
            ->assertJsonMissingValidationErrors('mentorship_tags');

        $this->assertEquals(
            ['first tag', 'second tag'],
            $this->profile->fresh()->mentorship_tags->pluck('name')->toArray()
        );
    }

    /** @test */
    public function testCanRemoveAllProfileTags()
    {
        $this->profile->attachTag('existing tag');

        $this->assertCount(1, $this->profile->tags);

        $this->updateProfile(['mentorship_tags' => ''])
            ->assertJsonMissingValidationErrors('mentorship_tags');

        $this->assertEmpty($this->profile->fresh()->mentorship_tags);
    }

    /** @test */
    public function testBioMustBeString()
    {
        $this->updateProfile(['bio' => 12345])
            ->assertJsonValidationErrors('bio');

        $this->updateProfile(['bio' => ['foo' => 'bar']])
            ->assertJsonValidationErrors('bio');
    }

    /** @test */
    public function testBioMustNotBeLongerThan65535Characters()
    {
        $this->updateProfile(['bio' => str_repeat('a', 65536)])
            ->assertJsonValidationErrors('bio');
    }

    /** @test */
    public function testBioCanBeNull()
    {
        $this->updateProfile(['bio' => null])
            ->assertOk()->assertJson(['bio' => null]);

        $this->assertNull($this->profile->fresh()->bio);
    }

    /** @test */
    public function testCanUpdateBio()
    {
        $this->updateProfile(['bio' => 'This is an awesome bio.'])
            ->assertOk()
            ->assertJson(['bio' => 'This is an awesome bio.']);

        $this->assertEquals('This is an awesome bio.', $this->profile->fresh()->bio);
    }

    /**
     * Send a request to update the profile.
     *
     * @param  array  $data
     * @param  \App\User|string  $profile
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateProfile(array $data = [], $profile = null)
    {
        return $this->patchJson(
            route('profiles.update', ['profile' => $profile ?? $this->profile]),
            $data
        );
    }
}
