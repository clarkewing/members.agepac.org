<?php

namespace Tests\Feature\Nova;

use App\Models\Company;
use App\Models\Occupation;
use Tests\NovaTestRequests;
use Tests\TestCase;

class MergeModelsActionTest extends TestCase
{
    use NovaTestRequests, MergesModels;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signInWithRole('Administrator');

        Company::factory()->count(2)->create();
    }

    /** @test */
    public function testAtLeastTwoModelsMustBeSelectedToMerge()
    {
        $this->performMerge('companies', [1])
            ->assertJson(['danger' => 'At least two models must be selected to merge.']);
    }

    /** @test */
    public function testPreservedIdMustBeInSelectedResources()
    {
        $this->performMerge('companies', [1, 2], 99)
            ->assertJson(['danger' => 'The model ID to be preserved was not found in the merge selection.']);
    }

    /** @test */
    public function testAllModelsButPreservedModelAreDeleted()
    {
        $this->performMerge('companies', [1, 2], 1)
            ->assertJson(['message' => 'Models merged successfully.']);

        $this->assertDatabaseHas('companies', ['id' => 1]);
        $this->assertDatabaseMissing('companies', ['id' => 2]);
    }

    /** @test */
    public function testBelongsToManyRelationshipsAreTransferredToPreservedModel()
    {
        Occupation::factory()->create(['company_id' => 2]);

        $this->assertEquals(0, Company::find(1)->employees()->count());
        $this->assertEquals(1, Company::find(2)->employees()->count());

        $this->performMerge('companies', [1, 2], 1)
            ->assertJson(['message' => 'Models merged successfully.']);

        $this->assertEquals(1, Company::find(1)->employees()->count());
    }
}
