<?php

namespace Tests\Feature\Nova;

use App\Company;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageCompaniesTest extends TestCase
{
    use NovaTestRequests;

    /** @test */
    public function testUnauthorizedUsersCannotIndexCompanies()
    {
        $this->signIn();

        $this->indexResource('companies')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewACompany()
    {
        $company = create(Company::class);

        $this->signIn();

        $this->showResource('companies', $company->id)
            ->assertForbidden();
    }

    /** @test */
    public function testCreatingACompanyOnNovaIsForbidden()
    {
        $this->signIn();

        $this->storeResource('companies', make(Company::class, ['name' => 'FakeBus'])->toArray())
            ->assertForbidden();

        $this->assertDatabaseMissing('companies', ['name' => 'FakeBus']);
    }

    /** @test */
    public function testEditingACompanyOnNovaIsForbidden()
    {
        $company = create(Company::class, ['name' => 'Acme Inc.']);

        $this->signInWithPermission('companies.delete');

        $this->updateResource('companies', $company->id, ['name' => 'Turd Express'])
            ->assertForbidden();

        $this->assertEquals('Acme Inc.', $company->fresh()->name);
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteACompany()
    {
        $company = create(Company::class);

        $this->signIn();

        $this->deleteResource('companies', $company->id)
            ->assertForbidden();

        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }

    /** @test */
    public function testAuthorizedUsersCanIndexCompanies()
    {
        $this->signInWithPermission('companies.delete');

        $this->indexResource('companies')
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanViewACompany()
    {
        $company = create(Company::class);

        $this->signInWithPermission('companies.delete');

        $this->showResource('companies', $company->id)
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanDeleteACompany()
    {
        $company = create(Company::class);

        $this->signInWithPermission('companies.delete');

        $this->deleteResource('companies', $company->id)
            ->assertOk();

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}
