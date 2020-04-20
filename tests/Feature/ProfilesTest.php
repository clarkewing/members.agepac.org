<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfilesTest extends TestCase
{
    /**
     * @test
     */
    public function testUserHasAProfile()
    {
        $user = create(User::class);

        $this->get('/profiles/' . $user->name)
            ->assertSee($user->name);
    }

    /**
     * @test
     */
    public function testProfilesDisplayAllThreadsCreatedByAssociatedUser()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);

        $this->get('/profiles/' . Auth::user()->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
