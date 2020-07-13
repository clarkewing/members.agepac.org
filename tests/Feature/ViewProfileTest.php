<?php

namespace Tests\Feature;

use App\Course;
use App\Occupation;
use App\Profile;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ViewProfileTest extends TestCase
{
    protected $profile;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->profile = create(Profile::class);
    }

    /** @test */
    public function testGuestsCannotSeeProfiles()
    {
        Auth::logout();

        $this->getProfile()
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUserHasAProfile()
    {
        $this->getProfile()
            ->assertSee($this->profile->name);
    }

    /** @test */
    public function testProfileDisplaysReputation()
    {
        $this->getProfile()
            ->assertSee($this->profile->reputation . ' XP');
    }

    /** @test */
    public function testProfileDisplaysClass()
    {
        $this->getProfile()
            ->assertSee($this->profile->class);
    }

    /** @test */
    public function testProfileDisplaysEmail()
    {
        $this->getProfile()
            ->assertSee($this->profile->email);
    }

    /** @test */
    public function testProfileHidesPhoneNumberIfNone()
    {
        $this->getProfile(create(User::class, ['phone' => null]))
            ->assertDontSee('Téléphone :');
    }

    /** @test */
    public function testProfileDisplaysPhoneNumber()
    {
        $this->getProfile()
            ->assertSee('Téléphone :')
            ->assertSee($this->profile->phone->formatInternational());
    }

    /** @test */
    public function testProfileHidesCurrentOccupationIfNone()
    {
        $this->getProfile()
            ->assertDontSee('Emploi :');
    }

    /** @test */
    public function testProfileDisplaysCurrentOccupation()
    {
        $occupation = create(Occupation::class, [
            'user_id' => $this->profile->id,
            'end_date' => null,
        ]);

        $this->getProfile()
            ->assertSee('Emploi :')
            ->assertSeeText("{$occupation->title} chez {$occupation->company->name}");
    }

    /** @test */
    public function testProfileHidesLocationIfUnknown()
    {
        $this->profile->location()->delete();

        $this->getProfile()
            ->assertDontSee('profile.location');
    }

    /** @test */
    public function testProfileDisplaysLocation()
    {
        $this->getProfile()
            ->assertSee('profile.location')
            ->assertSee(json_encode(['location' => $this->profile->location]));
    }

    /** @test */
    public function testProfileHidesFlightHoursIfUnknown()
    {
        $this->getProfile(create(User::class, ['flight_hours' => null]))
            ->assertDontSee('profile.flight-hours');
    }

    /** @test */
    public function testProfileDisplaysFlightHours()
    {
        $this->getProfile(create(User::class, ['flight_hours' => 150]))
            ->assertSee('profile.flight-hours')
            ->assertSee(json_encode(['flight_hours' => 150]));
    }

    /** @test */
    public function testProfileHidesBioIfEmpty()
    {
        $this->getProfile(create(User::class, ['bio' => null]))
            ->assertDontSee('profile.bio');
    }

    /** @test */
    public function testProfileDisplaysBio()
    {
        $this->getProfile()
            ->assertSee('profile.bio')
            ->assertSee(json_encode(['bio' => $this->profile->bio]));
    }

    /** @test */
    public function testProfileHidesExperienceIfEmpty()
    {
        $this->getProfile()
            ->assertDontSee('profile.experience');
    }

    /** @test */
    public function testProfileDisplaysExperience()
    {
        $this->profile->experience()->save(make(Occupation::class));

        $this->getProfile()
            ->assertSee('profile.experience')
            ->assertSee(json_encode(['experience' => $this->profile->experience]));
    }

    /** @test */
    public function testProfileHidesEducationIfEmpty()
    {
        $this->getProfile()
            ->assertDontSee('profile.education');
    }

    /** @test */
    public function testProfileDisplaysEducation()
    {
        $this->profile->education()->save(make(Course::class));

        $this->getProfile()
            ->assertSee('profile.education')
            ->assertSee(json_encode(['education' => $this->profile->education]));
    }

    /** @test */
    public function testProfileDisplaysAllThreadsCreatedByAssociatedUser()
    {
        $this->signIn($this->profile);

        $thread = create(Thread::class, ['user_id' => $this->profile->id]);

        $this->getProfile()
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /**
     * Get the user's profile.
     *
     * @param  \App\User|null  $user
     * @return \Illuminate\Testing\TestResponse
     */
    protected function getProfile(User $user = null): \Illuminate\Testing\TestResponse
    {
        return $this->get(route('profiles.show', $user ?? $this->profile));
    }
}
