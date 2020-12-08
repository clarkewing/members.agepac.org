<?php

namespace Tests\Unit;

use App\Company;
use App\Occupation;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    /** @test */
    public function testCanGetArrayOfDefinedTypes()
    {
        $this->assertCount(7, Company::typeStrings());
    }

    /** @test */
    public function testGetsTypeAsString()
    {
        $this->assertEquals(
            'Compagnie aérienne',
            Company::factory()->make(['type_code' => Company::AIRLINE])->type
        );

        $this->assertEquals(
            'Travail aérien',
            Company::factory()->make(['type_code' => Company::AIRWORK])->type
        );

        $this->assertEquals(
            'École',
            Company::factory()->make(['type_code' => Company::SCHOOL])->type
        );

        $this->assertEquals(
            'Aéroclub',
            Company::factory()->make(['type_code' => Company::FLYING_CLUB])->type
        );

        $this->assertEquals(
            'Agence gouvernementale',
            Company::factory()->make(['type_code' => Company::GOV_AGENCY])->type
        );

        $this->assertEquals(
            'Association',
            Company::factory()->make(['type_code' => Company::ASSOCIATION])->type
        );

        $this->assertEquals(
            'Autre entreprise',
            Company::factory()->make(['type_code' => Company::OTHER_BUSINESS])->type
        );
    }

    /** @test */
    public function testSlugGeneratedOnCreate()
    {
        $company = Company::factory()->create(['name' => 'Air France']);

        $this->assertEquals('air-france', $company->slug);
    }

    /** @test */
    public function testSlugRegeneratedOnUpdate()
    {
        $company = Company::factory()->create(['name' => 'Hop!']);

        $this->assertEquals('hop', $company->slug);

        $company->update(['name' => 'Air France Hop!']);

        $this->assertEquals('air-france-hop', $company->slug);
    }

    /**
     * @test
     * @group external-api
     * @group wikipedia-api
     */
    public function testDescriptionFetchedFromWikipediaWhenEmptyOnCreation()
    {
        // Filled if found.
        $company = Company::factory()->create(['name' => 'Air France', 'description' => null]);

        $this->assertNotEmpty($company->description);

        // Left empty if not found.
        $company = Company::factory()->create(['name' => 'Para76', 'description' => null]);

        $this->assertNull($company->description);
    }

    /** @test */
    public function testCanGetEmployees()
    {
        $company = Company::factory()->create();

        $occupations = Occupation::factory()->count(3)->create(['company_id' => $company->id]);

        $this->assertCount(3, $company->employees);
    }

    /** @test */
    public function testCanGetCurrentEmployees()
    {
        $company = Company::factory()->create();

        $pastOccupations = Occupation::factory()->count(3)->past()->create(['company_id' => $company->id]);
        $currentOccupations = Occupation::factory()->count(2)->current()->create(['company_id' => $company->id]);

        $this->assertCount(2, $company->current_employees);
    }

    /** @test */
    public function testCanGetFormerEmployees()
    {
        $company = Company::factory()->create();

        $formerOccupations = Occupation::factory()->count(3)->past()->create(['company_id' => $company->id]);
        $currentOccupations = Occupation::factory()->count(2)->current()->create(['company_id' => $company->id]);

        $this->assertCount(3, $company->former_employees);
    }
}
