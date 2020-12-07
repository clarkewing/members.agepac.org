<?php

namespace Tests\Feature;

use App\Company;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EditCompanyTest extends TestCase
{
    /**
     * @var \App\Company
     */
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->company = create(Company::class);
    }

    /** @test */
    public function testGuestCannotUpdateCompany()
    {
        Auth::logout();

        $this->updateCompany()
            ->assertUnauthorized();
    }

    /** @test */
    public function testNameIsRequiredIfSet()
    {
        $this->updateCompany(['name' => null])
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function testNameMustBeString()
    {
        $this->updateCompany(['name' => 12345])
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function testNameCannotBeLongerThan255Characters()
    {
        $this->updateCompany(['name' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function testTypeCodeIsRequiredIfSet()
    {
        $this->updateCompany(['type_code' => null])
            ->assertJsonValidationErrors('type_code');
    }

    /** @test */
    public function testTypeCodeMustExist()
    {
        $this->updateCompany(['type_code' => 999])
            ->assertJsonValidationErrors('type_code');
    }

    /** @test */
    public function testWebsiteCanBeNull()
    {
        $this->updateCompany(['website' => null])
            ->assertJsonMissingValidationErrors('website');
    }

    /** @test */
    public function testWebsiteMustBeValidUrl()
    {
        $this->updateCompany(['website' => 'foobar'])
            ->assertJsonValidationErrors('website');
    }

    /** @test */
    public function testWebsiteCannotBeLongerThan255Characters()
    {
        $this->updateCompany(['website' => 'foo.com/'.str_repeat('-', 256 - 8)])
            ->assertJsonValidationErrors('website');
    }

    /** @test */
    public function testDescriptionIsRequiredIfSet()
    {
        $this->updateCompany(['description' => null])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testDescriptionMustBeString()
    {
        $this->updateCompany(['description' => 12345])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testDescriptionCannotBeLongerThan65535Characters()
    {
        $this->updateCompany(['description' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testOperationsCanBeNull()
    {
        $this->updateCompany(['operations' => null])
            ->assertJsonMissingValidationErrors('operations');
    }

    /** @test */
    public function testOperationsMustBeString()
    {
        $this->updateCompany(['operations' => 12345])
            ->assertJsonValidationErrors('operations');
    }

    /** @test */
    public function testOperationsCannotBeLongerThan65535Characters()
    {
        $this->updateCompany(['operations' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('operations');
    }

    /** @test */
    public function testConditionsCanBeNull()
    {
        $this->updateCompany(['conditions' => null])
            ->assertJsonMissingValidationErrors('conditions');
    }

    /** @test */
    public function testConditionsMustBeString()
    {
        $this->updateCompany(['conditions' => 12345])
            ->assertJsonValidationErrors('conditions');
    }

    /** @test */
    public function testConditionsCannotBeLongerThan65535Characters()
    {
        $this->updateCompany(['conditions' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('conditions');
    }

    /** @test */
    public function testRemarksCanBeNull()
    {
        $this->updateCompany(['remarks' => null])
            ->assertJsonMissingValidationErrors('remarks');
    }

    /** @test */
    public function testRemarksMustBeString()
    {
        $this->updateCompany(['remarks' => 12345])
            ->assertJsonValidationErrors('remarks');
    }

    /** @test */
    public function testRemarksCannotBeLongerThan65535Characters()
    {
        $this->updateCompany(['remarks' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('remarks');
    }

    /** @test */
    public function testCanUpdateCompany()
    {
        $data = make(Company::class)->toArray();

        $this->updateCompany($data)
            ->assertJsonMissingValidationErrors()
            ->assertOk()
            ->assertJson($data);
    }

    /**
     * Send a request to update the company.
     *
     * @param  array  $data
     * @param  null  $company
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateCompany(array $data = [], $company = null)
    {
        return $this->patchJson(
            route(
                'companies.update',
                $company ?? $this->company
            ),
            $data
        );
    }
}
