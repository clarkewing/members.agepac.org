<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class FeminineStrTest extends TestCase
{
    /** @test */
    public function testReturnsMasculineFormWhenUnauthenticated()
    {
        $this->assertEquals('grand', Str::feminine('grand'));
    }

    /** @test */
    public function testReturnsFeminineFormWhenAuthenticatedAsFemale()
    {
        $this->signInGender('F');

        $this->assertEquals('grande', Str::feminine('grand'));
    }

    /** @test */
    public function testReturnsMasculineFormWhenAuthenticatedAsAnythingElse()
    {
        $this->signInGender('M');

        $this->assertEquals('grand', Str::feminine('grand'));

        $this->signInGender('O');

        $this->assertEquals('grand', Str::feminine('grand'));

        $this->signInGender('U');

        $this->assertEquals('grand', Str::feminine('grand'));
    }

    /** @test */
    public function testCanAcceptMoreComplexFeminineForms()
    {
        $this->signInGender('F');

        $this->assertEquals('belle', Str::feminine('beau', 'belle'));
    }

    protected function signInGender(string $gender): void
    {
        $this->signIn(User::factory()->create(['gender' => $gender]));
    }
}
