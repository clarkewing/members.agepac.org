<?php

namespace Tests\Feature\Nova;

use App\Models\User;
use Illuminate\Support\Arr;
use LimeDeck\NovaCashierOverview\Http\Controllers\StripeSubscriptionsController;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageUsersTest extends TestCase
{
    use NovaTestRequests;

    public function permissionProvider()
    {
        return [
            'view' => ['view'],
            'edit' => ['edit'],
            'delete' => ['delete'],
        ];
    }

    /** @test */
    public function testUnauthorizedUsersCannotIndexUsers()
    {
        $this->signIn();

        $this->indexResource('users')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewUsers()
    {
        $user = User::factory()->create();

        $this->signIn();

        $this->showResource('users', $user->id)
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditUsers()
    {
        $user = User::factory()->create();

        $this->signIn();

        $this->updateUser(['first_name' => 'This name cannot exist'], $user)
            ->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'first_name' => 'This name cannot exist',
        ]);
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteUsers()
    {
        $user = User::factory()->create();

        $this->signIn();

        $this->deleteResource('users', $user->id);
        // Nova doesn't return 403 on unauthorized delete request, so we don't check the status.
        // Beware: with a random user, it'll return a 403 because of the viewAll authorization.
        // But with any other permission than delete, it would return a 200.

        $this->assertDatabaseCount('users', 2); // Ensure it wasn't deleted.
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanListUsers($permission)
    {
        $this->signInWithPermission("users.$permission");

        $this->indexResource('users')
            ->assertOk();
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanViewUsers($permission)
    {
        $user = User::factory()->create();

        $this->signInWithPermission("users.$permission");

        $this->showResource('users', $user->id)
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanEditUsers()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(
            $user = User::factory()->make()
                ->makeVisible('email')
                ->makeHidden(['name'])
                ->toArray()
        )->assertOk();

        $this->assertDatabaseHas('users', $user);
    }

    /** @test */
    public function testAuthorizedUsersCanDeleteUsers()
    {
        $user = User::factory()->create();

        $this->signInWithPermission('users.delete');

        $this->deleteResource('users', $user->id)
            ->assertOk();

        $this->assertDatabaseCount('users', 1); // The currently signed in user
    }

    /** @test */
    public function testCreatingUsersIsForbidden()
    {
        $this->signIn();

        $this->storeResource('users', User::factory()->raw())
            ->assertForbidden();

        $this->assertDatabaseCount('users', 1); // The currently signed in user
    }

    /** @test */
    public function testFirstNameIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['first_name' => null])
            ->assertJsonValidationErrors('first_name');
    }

    /** @test */
    public function testFirstNameCannotExceed255Characters()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['first_name' => str_repeat('a', 256)])
            ->assertJsonValidationErrors('first_name');
    }

    /** @test */
    public function testLastNameIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['last_name' => null])
            ->assertJsonValidationErrors('last_name');
    }

    /** @test */
    public function testLastNameCannotExceed255Characters()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['last_name' => str_repeat('a', 256)])
            ->assertJsonValidationErrors('last_name');
    }

    /** @test */
    public function testGenderIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['gender' => null])
            ->assertJsonValidationErrors('gender');
    }

    /** @test */
    public function testGenderMustBeRegisteredInConfig()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['gender' => 'Z'])
            ->assertJsonValidationErrors('gender');
    }

    /** @test */
    public function testBirthdateIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['birthdate' => null])
            ->assertJsonValidationErrors('birthdate');
    }

    /** @test */
    public function testBirthdateMustBeAValidDate()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['birthdate' => 'not-a-date'])
            ->assertJsonValidationErrors('birthdate');

        $this->updateUser(['birthdate' => '1990-02-31'])
            ->assertJsonValidationErrors('birthdate');
    }

    /** @test */
    public function testBirthdateMustBeOfIso8601Format()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['birthdate' => '22/09/1994'])
            ->assertJsonValidationErrors('birthdate');
    }

    /** @test */
    public function testUserMustBeOlderThanThirteen() // Plenty of margin there...
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['birthdate' => now()->subYears(10)->toDateString()])
            ->assertJsonValidationErrors('birthdate');
    }

    /** @test */
    public function testClassCourseIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['class_course' => null])
            ->assertJsonValidationErrors('class_course');
    }

    /** @test */
    public function testClassCourseMustBeRegisteredInConfig()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['class_course' => 'Foobar'])
            ->assertJsonValidationErrors('class_course');
    }

    /** @test */
    public function testClassYearIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['class_year' => null])
            ->assertJsonValidationErrors('class_year');
    }

    /** @test */
    public function testClassYearMustBeAFourDigitYear()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['class_year' => 'Not a year'])
            ->assertJsonValidationErrors('class_year');

        $this->updateUser(['class_year' => '15'])
            ->assertJsonValidationErrors('class_year');
    }

    /** @test */
    public function testUsernameIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['username' => null])
            ->assertJsonValidationErrors('username');
    }

    /** @test */
    public function testUsernameMustHaveAppropriateFormat()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['username' => "Weird'UserN4me"])
            ->assertJsonValidationErrors('username');

        $this->updateUser(['username' => 'some.thing.wrong'])
            ->assertJsonValidationErrors('username');
    }

    /** @test */
    public function testUsernameCannotExceed255Characters()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['username' => str_repeat('a', 256)])
            ->assertJsonValidationErrors('username');
    }

    /** @test */
    public function testEmailIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['email' => null])
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function testEmailMustBeValid()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['email' => 'not-an-email'])
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function testEmailCannotExceed255Characters()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['email' => str_repeat('a', 256).'@example.com'])
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function testEmailMustBeUnique()
    {
        $this->signInWithPermission('users.edit');

        User::factory()->create(['email' => 'john@example.com']);

        $this->updateUser(['email' => 'john@example.com'])
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function testPhoneIsRequired()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['phone' => null])
            ->assertJsonValidationErrors('phone');
    }

    /** @test */
    public function testPhoneMustValid()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(['phone' => 'n0t_4_ph0n3_numb3r'])
            ->assertJsonValidationErrors('phone');
    }

    /** @test */
    public function testUnauthorizedUsersCannotManageSubscription()
    {
        $this->signInWithPermission('users.view');

        $user = User::factory()->create();

        $fields = $this->showResource('users', $user->id)->json('resource.fields');

        $this->assertNull(Arr::first($fields, function ($field) {
            return $field['component'] === 'subscription';
        }));

        $this->get(action([StripeSubscriptionsController::class, 'show'], ['id' => 'foobar']))
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanManageSubscription()
    {
        $this->signInWithPermission(['users.view', 'subscriptions.manage']);

        $user = User::factory()->create();

        $fields = $this->showResource('users', $user->id)->json('resource.fields');

        $this->assertNotNull(Arr::first($fields, function ($field) {
            return $field['component'] === 'subscription';
        }));

        $this->get(action([StripeSubscriptionsController::class, 'show'], ['id' => 'foobar']))
            ->assertOk();
    }

    /**
     * Submits a request to update an existing user invitation.
     *
     * @param  array  $data
     * @param  \App\Models\User|null  $user
     * @return \Illuminate\Testing\TestResponse
     */
    public function updateUser(array $data = [], User $user = null)
    {
        $user = $user ?? User::factory()->create();

        return $this->updateResource(
            'users', $user->id,
            array_merge($user->makeVisible('email')->toArray(), $data)
        );
    }
}
