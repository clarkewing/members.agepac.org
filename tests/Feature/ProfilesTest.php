<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    /** @test */
    public function testUserHasAProfile()
    {
        $user = create(User::class);

        $this->getJson(route('profiles.show', $user))
            ->assertJson(['profileUser' => [
                'name' => $user->name,
            ]]);
    }

    /** @test */
    public function testProfilesDisplayAllThreadsCreatedByAssociatedUser()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);

        $this->get(route('profiles.show', Auth::user()))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
