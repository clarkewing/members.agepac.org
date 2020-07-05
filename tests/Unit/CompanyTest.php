<?php

namespace Tests\Unit;

use App\Company;
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
}
