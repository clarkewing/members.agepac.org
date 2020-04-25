<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{
    /**
     * @test
     */
    public function testOnlyMembersCanAddAvatars()
    {
        $this->withExceptionHandling();

        $this->postJson('/api/users/1/avatar')
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function testAValidAvatarMustBeProvided()
    {
        $this->withExceptionHandling()->signIn();

        $this->postJson('/api/users/' . Auth::id() . '/avatar', [
            'avatar' => 'not-an-image',
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function testAUserMayAddAnAvatarToTheirProfile()
    {
        $this->signIn();

        Storage::fake('public');

        $this->postJson('/api/users/' . Auth::id() . '/avatar', [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertEquals(Storage::url('avatars/' . $file->hashName()), Auth::user()->avatar_path);

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
}
