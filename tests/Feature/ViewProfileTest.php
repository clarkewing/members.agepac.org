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
        $this->user->location()->save(make(Location::class));

        $this->getProfile()
            ->assertSee(json_encode(['location' => $this->user->location]));
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
            ->assertSee(json_encode(['flight_hours' => 150]));
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
            ->assertSee(json_encode(['bio' => $this->user->bio]));
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
//        $occupation = create(Occupation::class, ['user_id' => $this->user->id]);
        $this->user->experience()->save(make(Occupation::class));

        $this->getProfile()
            ->assertSee(json_encode(['experience' => $this->user->experience]));
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
        $this->user->education()->save(make(Course::class));

        $this->getProfile()
            ->assertSee(json_encode(['education' => $this->user->education]));
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
