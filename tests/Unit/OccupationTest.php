<?php

namespace Tests\Unit;

use App\Occupation;
use Tests\TestCase;

class OccupationTest extends TestCase
{
    /** @test */
    public function testCanGetArrayOfDefinedStatuses()
    {
        $this->assertCount(4, Occupation::definedStatuses());
    }

    /** @test */
    public function testHasPresentableStatus()
    {
        $this->assertEquals(
            'Salarié à temps plein',
            make(Occupation::class, ['status' => Occupation::EMPLOYED_FULL_TIME])->status()
        );

        $this->assertEquals(
            'Salarié à temps partiel',
            make(Occupation::class, ['status' => Occupation::EMPLOYED_PART_TIME])->status()
        );

        $this->assertEquals(
            'Auto-entrepreneur',
            make(Occupation::class, ['status' => Occupation::SELF_EMPLOYED])->status()
        );

        $this->assertEquals(
            'Bénévole',
            make(Occupation::class, ['status' => Occupation::UNPAID])->status()
        );
    }

    /** @test */
    public function testKnowsIfItIsPilotOccupation()
    {
        $pilotOccupation = factory(Occupation::class)->states('pilot')->create();

        $this->assertTrue($pilotOccupation->is_pilot);

        $otherOccupation = factory(Occupation::class)->states('not_pilot')->create();

        $this->assertFalse($otherOccupation->is_pilot);
    }

    /** @test */
    public function testHasTitle()
    {
        $pilotOccupation = factory(Occupation::class)->states('pilot')->create();

        $this->assertEquals(
            "{$pilotOccupation->position} sur {$pilotOccupation->aircraft->name}",
            $pilotOccupation->title
        );

        $otherOccupation = factory(Occupation::class)->states('not_pilot')->create();

        $this->assertEquals($otherOccupation->position, $otherOccupation->title);
    }
}
