<?php

namespace Tests\Unit;

use App\Exceptions\UnknownOccupationStatusException;
use App\Models\Occupation;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OccupationTest extends TestCase
{
    /** @test */
    public function testCanGetArrayOfDefinedStatuses()
    {
        $this->assertCount(4, Occupation::statusStrings());
    }

    /** @test */
    public function testKnowsIfItIsPilotOccupation()
    {
        $pilotOccupation = Occupation::factory()->pilot()->create();

        $this->assertTrue($pilotOccupation->is_pilot);

        $otherOccupation = Occupation::factory()->notPilot()->create();

        $this->assertFalse($otherOccupation->is_pilot);
    }

    /** @test */
    public function testHasTitle()
    {
        $pilotOccupation = Occupation::factory()->pilot()->create();

        $this->assertEquals(
            "{$pilotOccupation->position} sur {$pilotOccupation->aircraft->name}",
            $pilotOccupation->title
        );

        $otherOccupation = Occupation::factory()->notPilot()->create();

        $this->assertEquals($otherOccupation->position, $otherOccupation->title);
    }

    /** @test */
    public function testGetsStatusAsString()
    {
        $this->assertEquals(
            'Salarié à temps plein',
            Occupation::factory()->make(['status_code' => Occupation::EMPLOYED_FULL_TIME])->status
        );

        $this->assertEquals(
            'Salarié à temps partiel',
            Occupation::factory()->make(['status_code' => Occupation::EMPLOYED_PART_TIME])->status
        );

        $this->assertEquals(
            'Auto-entrepreneur',
            Occupation::factory()->make(['status_code' => Occupation::SELF_EMPLOYED])->status
        );

        $this->assertEquals(
            'Bénévole',
            Occupation::factory()->make(['status_code' => Occupation::UNPAID])->status
        );
    }

    /** @test */
    public function testGettingUnknownStatusThrowsException()
    {
        $this->expectException(UnknownOccupationStatusException::class);

        $id = DB::table('occupations')->insertGetId(
            array_merge(Occupation::factory()->make()->getAttributes(), ['status_code' => 999])
        );

        Occupation::find($id)->status;
    }

    /** @test */
    public function testSetsStatusAsInt()
    {
        $occupation = Occupation::factory()->create(['status' => 'Salarié à temps plein']);

        $this->assertDatabaseHas('occupations', [
            'id' => $occupation->id,
            'status_code' => Occupation::EMPLOYED_FULL_TIME,
        ]);

        $occupation = Occupation::factory()->create(['status' => 'Salarié à temps partiel']);

        $this->assertDatabaseHas('occupations', [
            'id' => $occupation->id,
            'status_code' => Occupation::EMPLOYED_PART_TIME,
        ]);

        $occupation = Occupation::factory()->create(['status' => 'Auto-entrepreneur']);

        $this->assertDatabaseHas('occupations', [
            'id' => $occupation->id,
            'status_code' => Occupation::SELF_EMPLOYED,
        ]);

        $occupation = Occupation::factory()->create(['status' => 'Bénévole']);

        $this->assertDatabaseHas('occupations', [
            'id' => $occupation->id,
            'status_code' => Occupation::UNPAID,
        ]);
    }

    /** @test */
    public function testSettingUnknownStatusStringThrowsException()
    {
        $this->expectException(UnknownOccupationStatusException::class);

        Occupation::factory()->create(['status' => 'foobar']);
    }

    /** @test */
    public function testSettingUnknownStatusIntegerThrowsException()
    {
        $this->expectException(UnknownOccupationStatusException::class);

        Occupation::factory()->create(['status' => 999]);
    }
}
