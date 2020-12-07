<?php

namespace Tests\Feature;

use App\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateCompanyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testGuestCannotStoreCompany()
    {
        Auth::logout();

        $this->storeCompany()
            ->assertUnauthorized();
    }

    /** @test */
    public function testNameIsRequired()
    {
        $this->storeCompany(['name' => null])
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function testNameMustBeString()
    {
        $this->storeCompany(['name' => 12345])
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function testNameCannotBeLongerThan255Characters()
    {
        $this->storeCompany(['name' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function testTypeCodeIsRequired()
    {
        $this->storeCompany(['type_code' => null])
            ->assertJsonValidationErrors('type_code');
    }

    /** @test */
    public function testTypeCodeMustExist()
    {
        $this->storeCompany(['type_code' => 999])
            ->assertJsonValidationErrors('type_code');
    }

    /** @test */
    public function testWebsiteCanBeNull()
    {
        $this->storeCompany(['website' => null])
            ->assertJsonMissingValidationErrors('website');
    }

    /** @test */
    public function testWebsiteMustBeValidUrl()
    {
        $this->storeCompany(['website' => 'foobar'])
            ->assertJsonValidationErrors('website');
    }

    /** @test */
    public function testWebsiteCannotBeLongerThan255Characters()
    {
        $this->storeCompany(['website' => 'foo.com/'.str_repeat('-', 256 - 8)])
            ->assertJsonValidationErrors('website');
    }

    /** @test */
    public function testDescriptionIsRequired()
    {
        $this->storeCompany(['description' => null])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testDescriptionMustBeString()
    {
        $this->storeCompany(['description' => 12345])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testDescriptionCannotBeLongerThan65535Characters()
    {
        $this->storeCompany(['description' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testOperationsCanBeNull()
    {
        $this->storeCompany(['operations' => null])
            ->assertJsonMissingValidationErrors('operations');
    }

    /** @test */
    public function testOperationsMustBeString()
    {
        $this->storeCompany(['operations' => 12345])
            ->assertJsonValidationErrors('operations');
    }

    /** @test */
    public function testOperationsCannotBeLongerThan65535Characters()
    {
        $this->storeCompany(['operations' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('operations');
    }

    /** @test */
    public function testConditionsCanBeNull()
    {
        $this->storeCompany(['conditions' => null])
            ->assertJsonMissingValidationErrors('conditions');
    }

    /** @test */
    public function testConditionsMustBeString()
    {
        $this->storeCompany(['conditions' => 12345])
            ->assertJsonValidationErrors('conditions');
    }

    /** @test */
    public function testConditionsCannotBeLongerThan65535Characters()
    {
        $this->storeCompany(['conditions' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('conditions');
    }

    /** @test */
    public function testRemarksCanBeNull()
    {
        $this->storeCompany(['remarks' => null])
            ->assertJsonMissingValidationErrors('remarks');
    }

    /** @test */
    public function testRemarksMustBeString()
    {
        $this->storeCompany(['remarks' => 12345])
            ->assertJsonValidationErrors('remarks');
    }

    /** @test */
    public function testRemarksCannotBeLongerThan65535Characters()
    {
        $this->storeCompany(['remarks' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('remarks');
    }

    /** @test */
    public function testCanStoreCompany()
    {
        $data = make(Company::class)->setAppends([])->attributesToArray();

        $this->storeCompany($data)
            ->assertJsonMissingValidationErrors()
            ->assertCreated()
            ->assertJson($data);

        $this->assertDatabaseHas('companies', $data);
    }

    /** @test */
    public function testNonJsonRequestReturnsRedirect()
    {
        $this->post(
            route('companies.store'), $company = make(Company::class)->toArray()
        )->assertRedirect(route('companies.show', Str::slug($company['name'])));
    }

    /**
     * Send a request to store the company.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storeCompany(array $overrides = [])
    {
        return $this->postJson(
            route('companies.store'), array_merge(
                make(Company::class)->toArray(),
                $overrides
            )
        );
    }
}
