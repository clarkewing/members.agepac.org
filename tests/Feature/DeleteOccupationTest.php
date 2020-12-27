<?php

namespace Tests\Feature;

use App\Models\Occupation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteOccupationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signInUnsubscribed();
    }

    /** @test */
    public function testGuestCannotDeleteOccupation()
    {
        Auth::logout();

        $this->deleteOccupation(1)
            ->assertUnauthorized();
    }

    /** @test */
    public function testOnlyAuthorizedUserCanDeleteOccupation()
    {
        $occupation = Occupation::factory()->create();

        $this->deleteOccupation($occupation->id)
            ->assertForbidden();

        $this->signIn(User::find($occupation->user_id));

        $this->deleteOccupation($occupation->id)
            ->assertSuccessful();
    }

    /** @test */
    public function testOccupationMustExist()
    {
        $this->deleteOccupation(999)
            ->assertNotFound();
    }

    /** @test */
    public function testOccupationOwnerCanDeleteIt()
    {
        $occupation = Occupation::factory()->create(['user_id' => Auth::id()]);

        $this->assertDatabaseHas('occupations', ['id' => $occupation->id]);

        $this->deleteOccupation($occupation)
            ->assertNoContent();

        $this->assertDatabaseMissing('occupations', ['id' => $occupation->id]);
    }

    /**
     * Send a request to delete the occupation.
     *
     * @param  \App\Models\Occupation|int  $occupation
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deleteOccupation($occupation)
    {
        return $this->deleteJson(route(
            'occupations.destroy',
            $occupation
        ));
    }
}
