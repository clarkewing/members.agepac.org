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
            make(Company::class, ['type_code' => Company::AIRLINE])->type
        );

        $this->assertEquals(
            'Travail aérien',
            make(Company::class, ['type_code' => Company::AIRWORK])->type
        );

        $this->assertEquals(
            'École',
            make(Company::class, ['type_code' => Company::SCHOOL])->type
        );

        $this->assertEquals(
            'Aéroclub',
            make(Company::class, ['type_code' => Company::FLYING_CLUB])->type
        );

        $this->assertEquals(
            'Agence gouvernementale',
            make(Company::class, ['type_code' => Company::GOV_AGENCY])->type
        );

        $this->assertEquals(
            'Association',
            make(Company::class, ['type_code' => Company::ASSOCIATION])->type
        );

        $this->assertEquals(
            'Autre entreprise',
            make(Company::class, ['type_code' => Company::OTHER_BUSINESS])->type
        );
    }

    /** @test */
    public function testSlugGeneratedOnCreate()
    {
        $company = create(Company::class, ['name' => 'Air France']);

        $this->assertEquals('air-france', $company->slug);
    }

    /** @test */
    public function testSlugRegeneratedOnUpdate()
    {
        $company = create(Company::class, ['name' => 'Hop!']);

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
        $company = create(Company::class, ['name' => 'Air France', 'description' => null]);

        $this->assertNotEmpty($company->description);

        // Left empty if not found.
        $company = create(Company::class, ['name' => 'Para76', 'description' => null]);

        $this->assertNull($company->description);
    }

    /** @test */
    public function testCanGetEmployees()
    {
        $company = create(Company::class);

        $occupations = create(Occupation::class, ['company_id' => $company->id], 3);

        $this->assertCount(3, $company->employees);
    }

    /** @test */
    public function testCanGetCurrentEmployees()
    {
        $company = create(Company::class);

        $pastOccupations = factory(Occupation::class, 3)
            ->states('past')->create(['company_id' => $company->id]);
        $currentOccupations = factory(Occupation::class, 2)
            ->states('current')->create(['company_id' => $company->id]);

        $this->assertCount(2, $company->current_employees);
    }

    /** @test */
    public function testCanGetFormerEmployees()
    {
        $company = create(Company::class);

        $formerOccupations = factory(Occupation::class, 3)
            ->states('past')->create(['company_id' => $company->id]);
        $currentOccupations = factory(Occupation::class, 2)
            ->states('current')->create(['company_id' => $company->id]);

        $this->assertCount(3, $company->former_employees);
    }
}
