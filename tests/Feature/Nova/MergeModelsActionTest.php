<?php

namespace Tests\Feature\Nova;

use App\Models\Company;
use App\Models\MentorshipTag;
use App\Models\Occupation;
use App\Models\Profile;
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

    /** @test */
    public function testIfTwoModelsRelatedToSameModelAreMergedThenDuplicateIsDeleted()
    {
        $this->signInWithPermission('mentorship_tags.merge');

        $profile = tap(Profile::factory()->create())->attachTags([
            MentorshipTag::create(['name' => 'foo']),
            MentorshipTag::create(['name' => 'bar']),
        ]);

        $this->assertEquals(2, $profile->mentorship_tags()->count());
        $this->assertDatabaseCount('taggables', 2);

        $this->performMerge('mentorship-tags', [1, 2])
            ->assertJson(['message' => 'Models merged successfully.']);

        $this->assertEquals(1, $profile->mentorship_tags()->count());
        $this->assertDatabaseCount('taggables', 1);
    }
}
