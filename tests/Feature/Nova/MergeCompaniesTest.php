<?php

namespace Tests\Feature\Nova;

use App\Models\Company;
use Tests\NovaTestRequests;
use Tests\TestCase;

class MergeCompaniesTest extends TestCase
{
    use NovaTestRequests, MergesModels;

    protected function setUp(): void
    {
        parent::setUp();

        Company::factory()->count(2)->create();
    }

    /** @test */
    public function testCompaniesResourceHasMergeAction()
    {
        $this->signInWithPermission('companies.merge');

        $this->listResourceActions('companies')
            ->assertJsonFragment(['name' => 'Merge']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotMergeCompanies()
    {
        $this->signInWithPermission('companies.delete');

        $this->performMerge('companies', [1, 2])
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanMergeCompanies()
    {
        $this->signInWithPermission('companies.merge');

        $this->performMerge('companies', [1, 2])
            ->assertJsonMissing(['danger' => 'Sorry! You are not authorized to perform this action.'])
            ->assertSuccessful();
    }
}
