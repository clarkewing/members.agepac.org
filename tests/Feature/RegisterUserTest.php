<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    /** @test */
    public function testGuestCanRegisterAnAccount() {
        $this->createAccount()
            ->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->assertCount(1, User::all());

        tap(User::first(), function($user) {
            $this->assertEquals('John Doe', $user->name);
            $this->assertEquals('john@example.com', $user->email);
            $this->assertTrue(Hash::check('HugoWasHere', $user->password));
        });
    }

    /** @test */
    public function testConfirmationEmailSentUponRegistration()
    {
        $this->createAccount();

        Notification::assertSentTo(
            [Auth::user()], VerifyEmail::class
        );
    }

    /** @test */
    public function testNewUserCanVerifyTheirEmail()
    {
        $this->createAccount();

        $user = Auth::user();
        $this->flushSession();

        $this->assertNull($user->email_verified_at);

        $this->get((new VerifyEmail)->toMail($user)->actionUrl)
            ->assertRedirect(route('home'));

        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function testFirstNameIsRequired()
    {
        $this->createAccount(['first_name' => ''])
            ->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function testFirstNameCannotExceed255Characters()
    {
        $this->createAccount(['first_name' => str_repeat('a', 256)])
            ->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function testLastNameIsRequired()
    {
        $this->createAccount(['last_name' => ''])
            ->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function testLastNameCannotExceed255Characters()
    {
        $this->createAccount(['last_name' => str_repeat('a', 256)])
            ->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function testEmailIsRequired()
    {
        $this->createAccount(['email' => ''])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testEmailCannotExceed255Characters()
    {
        $this->createAccount(['email' => str_repeat('a', 256) . '@example.com'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testEmailMustBeValid()
    {
        $this->createAccount(['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testEmailMustBeUnique()
    {
        create(User::class, ['email' => 'john@example.com']);

        $this->createAccount(['email' => 'john@example.com'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testPasswordIsRequired()
    {
        $this->createAccount(['password' => ''])
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function testPasswordMustBeLongerThan8Characters()
    {
        $this->createAccount(['password' => 'foobar'])
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function testPasswordMustBeConfirmed()
    {
        $this->createAccount(['password_confirmation' => 'oopswrong'])
            ->assertSessionHasErrors('password');
    }

    /**
     * Submits a post request to create an account.
     *
     * @param  array $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function createAccount(array $overrides = [])
    {
        $this->withExceptionHandling();

        return $this->post(route('register'), array_merge([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'HugoWasHere',
            'password_confirmation' => 'HugoWasHere',
        ], $overrides));
    }
}
