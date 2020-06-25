<?php

namespace Tests\Feature;

use App\Course;
use App\Location;
use App\Occupation;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ViewProfileTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->user = create(User::class);
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
            ->assertSee($this->user->name);
    }

    /** @test */
    public function testProfileDisplaysReputation()
    {
        $this->getProfile()
            ->assertSee($this->user->reputation . ' XP');
    }

    /** @test */
    public function testProfileDisplaysClass()
    {
        $this->getProfile()
            ->assertSee($this->user->class);
    }

    /** @test */
    public function testProfileDisplaysEmail()
    {
        $this->getProfile()
            ->assertSee($this->user->email);
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
            ->assertSee($this->user->phone->formatInternational());
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
            'user_id' => $this->user->id,
            'end_date' => null,
        ]);

        $this->getProfile()
            ->assertSee('Emploi :')
            ->assertSeeText("{$occupation->title} chez {$occupation->company}");
    }

    /** @test */
    public function testProfileHidesLocationIfUnknown()
    {
        $this->getProfile()
            ->assertDontSee('Lieu :');
    }

    /** @test */
    public function testProfileDisplaysLocation()
    {
        $location = create(Location::class, [
            'locatable_id' => $this->user->id,
            'locatable_type' => get_class($this->user),
        ]);

        $this->getProfile()
            ->assertSee('Lieu :')
            ->assertSee("{$location->municipality}, {$location->country}");
    }

    /** @test */
    public function testProfileHidesFlightHoursIfUnknown()
    {
        $this->getProfile(create(User::class, ['flight_hours' => null]))
            ->assertDontSee('heures de vol');
    }

    /** @test */
    public function testProfileDisplaysFlightHours()
    {
        $this->getProfile(create(User::class, ['flight_hours' => 150]))
            ->assertSee('150 heures de vol');
    }

    /** @test */
    public function testProfileHidesBioIfEmpty()
    {
        $this->getProfile(create(User::class, ['bio' => null]))
            ->assertDontSee('Biographie');
    }

    /** @test */
    public function testProfileDisplaysBio()
    {
        $this->getProfile()
            ->assertSee('Biographie')
            ->assertSee($this->user->bio);
    }

    /** @test */
    public function testProfileHidesExperienceIfEmpty()
    {
        $this->getProfile()
            ->assertDontSee('Expérience Professionelle');
    }

    /** @test */
    public function testProfileDisplaysExperience()
    {
        $occupation = create(Occupation::class, ['user_id' => $this->user->id]);

        $this->getProfile()
            ->assertSee('Expérience Professionelle')
            ->assertSee($occupation->title)
            ->assertSee($occupation->status())
            ->assertSee($occupation->company)
            ->assertSee("{$occupation->location->municipality}, {$occupation->location->country}")
            ->assertSee($occupation->start_date->isoFormat('LL'))
            ->assertSee(optional($occupation->end_date)->isoFormat('LL'))
            ->assertSee($occupation->description);
    }

    /** @test */
    public function testProfileHidesEducationIfEmpty()
    {
        $this->getProfile()
            ->assertDontSee('Éducation');
    }

    /** @test */
    public function testProfileDisplaysEducation()
    {
        $course = create(Course::class, ['user_id' => $this->user->id]);

        $this->getProfile()
            ->assertSee('Éducation')
            ->assertSee($course->title)
            ->assertSee($course->school)
            ->assertSee("{$course->location->municipality}, {$course->location->country}")
            ->assertSee($course->start_date->isoFormat('LL'))
            ->assertSee(optional($course->end_date)->isoFormat('LL'))
            ->assertSee($course->description);
    }

    /** @test */
    public function testProfileDisplaysAllThreadsCreatedByAssociatedUser()
    {
        $this->signIn($this->user);

        $thread = create(Thread::class, ['user_id' => $this->user->id]);

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
        return $this->get(route('profiles.show', $user ?? $this->user));
    }
}
