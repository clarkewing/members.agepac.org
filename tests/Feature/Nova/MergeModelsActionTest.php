<?php

namespace Tests\Feature\Nova;

use App\Models\Company;
use App\Models\Occupation;
use Tests\NovaTestRequests;
use Tests\TestCase;

class MergeModelsActionTest extends TestCase
{
    use NovaTestRequests;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signInWithPermission('companies.merge');

        Company::factory()->count(2)->create();
    }

    /** @test */
    public function testCompaniesResourceHasMergeAction()
    {
        $this->listResourceActions('companies')
            ->assertJsonFragment(['name' => 'Merge']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotMergeModels()
    {
        $this->signInWithPermission('companies.delete');

        $this->performMerge([1, 2])
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanMergeModels()
    {
        $this->performMerge([1, 2])
            ->assertJsonMissing(['danger' => 'Sorry! You are not authorized to perform this action.'])
            ->assertSuccessful();
    }

    /** @test */
    public function testAtLeastTwoModelsMustBeSelectedToMerge()
    {
        $this->performMerge([1])
            ->assertJson(['danger' => 'At least two models must be selected to merge.']);
    }

    /** @test */
    public function testPreservedIdMustBeInSelectedResources()
    {
        $this->performMerge([1, 2], 99)
            ->assertJson(['danger' => 'The model ID to be preserved was not found in the merge selection.']);
    }

    /** @test */
    public function testAllModelsButPreservedModelAreDeleted()
    {
        $this->performMerge([1, 2], 1)
            ->assertJson(['message' => 'Models merged successfully.']);

        $this->assertDatabaseHas('companies', ['id' => 1]);
        $this->assertDatabaseMissing('companies', ['id' => 2]);
    }

    /** @test */
    public function testRelationshipsAreTransferredToPreservedModel()
    {
        Occupation::factory()->create(['company_id' => 2]);

        $this->assertEquals(0, Company::find(1)->employees()->count());
        $this->assertEquals(1, Company::find(2)->employees()->count());

        $this->performMerge([1, 2], 1)
            ->assertJson(['message' => 'Models merged successfully.']);

        $this->assertEquals(1, Company::find(1)->employees()->count());
    }

    /**
     * @param  array  $resourceIds
     * @param  int|null  $preservedId
     * @return \Illuminate\Testing\TestResponse
     */
    protected function performMerge(array $resourceIds = [], int $preservedId = null): \Illuminate\Testing\TestResponse
    {
        return $this->performResourceAction('companies', 'merge', [
            'resources' => implode(',', $resourceIds),
            'preserved_id' => $preservedId ?? $resourceIds[0],
        ]);
    }
}
