<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{
    /** @test */
    public function testOnlyMembersCanAddAvatars()
    {
        $this->withExceptionHandling();

        $this->postJson(route('api.users.avatar.store', 1))
            ->assertStatus(401);
    }

    /** @test */
    public function testAValidAvatarMustBeProvided()
    {
        $this->withExceptionHandling()->signIn();

        $this->postJson(route('api.users.avatar.store', Auth::user()), [
            'avatar' => 'not-an-image',
        ])->assertStatus(422);
    }

    /** @test */
    public function testAUserMayAddAnAvatarToTheirProfile()
    {
        $this->signIn();

        Storage::fake('public');

        $this->postJson(route('api.users.avatar.store', Auth::user()), [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertEquals(Storage::url('avatars/' . $file->hashName()), Auth::user()->avatar_path);

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }

    /** @test */
    public function testAddingANewAvatarDeletesTheOldOneFromDisk()
    {
        $this->signIn();

        Storage::fake('public');

        $this->postJson(route('api.users.avatar.store', Auth::user()), [
            'avatar' => $old_file = UploadedFile::fake()->image('old_avatar.jpg'),
        ]);

        Storage::disk('public')->assertExists('avatars/' . $old_file->hashName());

        $this->postJson(route('api.users.avatar.store', Auth::user()), [
            'avatar' => $new_file = UploadedFile::fake()->image('new_avatar.jpg'),
        ]);

        Storage::disk('public')->assertMissing('avatars/' . $old_file->hashName());

        Storage::disk('public')->assertExists('avatars/' . $new_file->hashName());
    }
}
