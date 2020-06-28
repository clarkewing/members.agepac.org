<?php

namespace Tests\Unit;

use App\Exceptions\UnknownOccupationStatusException;
use App\Location;
use App\Occupation;
use Illuminate\Support\Arr;
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

    /** @test */
    public function testGetsStatusAsString()
    {
        $this->assertEquals(
            'Salarié à temps plein',
            make(Occupation::class, ['status_code' => Occupation::EMPLOYED_FULL_TIME])->status
        );

        $this->assertEquals(
            'Salarié à temps partiel',
            make(Occupation::class, ['status_code' => Occupation::EMPLOYED_PART_TIME])->status
        );

        $this->assertEquals(
            'Auto-entrepreneur',
            make(Occupation::class, ['status_code' => Occupation::SELF_EMPLOYED])->status
        );

        $this->assertEquals(
            'Bénévole',
            make(Occupation::class, ['status_code' => Occupation::UNPAID])->status
        );
    }

    /** @test */
    public function testGettingUnknownStatusThrowsException()
    {
        $this->expectException(UnknownOccupationStatusException::class);

        $id = DB::table('occupations')->insertGetId(
            array_merge(make(Occupation::class)->getAttributes(), ['status_code' => 999])
        );

        Occupation::find($id)->status;
    }

    /** @test */
    public function testSetsStatusAsInt()
    {
        $occupation = create(Occupation::class, ['status' => 'Salarié à temps plein']);

        $this->assertDatabaseHas('occupations', [
            'id' => $occupation->id,
            'status_code' => Occupation::EMPLOYED_FULL_TIME,
        ]);

        $occupation = create(Occupation::class, ['status' => 'Salarié à temps partiel']);

        $this->assertDatabaseHas('occupations', [
            'id' => $occupation->id,
            'status_code' => Occupation::EMPLOYED_PART_TIME,
        ]);

        $occupation = create(Occupation::class, ['status' => 'Auto-entrepreneur']);

        $this->assertDatabaseHas('occupations', [
            'id' => $occupation->id,
            'status_code' => Occupation::SELF_EMPLOYED,
        ]);

        $occupation = create(Occupation::class, ['status' => 'Bénévole']);

        $this->assertDatabaseHas('occupations', [
            'id' => $occupation->id,
            'status_code' => Occupation::UNPAID,
        ]);
    }

    /** @test */
    public function testSettingUnknownStatusStringThrowsException()
    {
        $this->expectException(UnknownOccupationStatusException::class);

        create(Occupation::class, ['status' => 'foobar']);
    }

    /** @test */
    public function testSettingUnknownStatusIntegerThrowsException()
    {
        $this->expectException(UnknownOccupationStatusException::class);

        create(Occupation::class, ['status' => 999]);
    }

    /** @test */
    public function testCanSetLocation()
    {
        $occupation = Occupation::forceCreate(
            make(Occupation::class)->setAppends([])->attributesToArray()
        );

        $this->assertNull($occupation->location);

        $locationData = Arr::except(make(Location::class)->toArray(), ['locatable_id', 'locatable_type']);

        $this->assertInstanceOf(Location::class,
            $occupation->setLocation($locationData)
        );

        $this->assertEmpty(array_diff_assoc(
            $locationData,
            $occupation->location->toArray()
        ));

        $this->assertDatabaseHas('locations', [
            'locatable_id' => $occupation->id,
            'locatable_type' => get_class($occupation)
        ] + $locationData);
    }

    /** @test */
    public function testAssociatedLocationIsDeletedOnDelete()
    {
        $occupation = create(Occupation::class);

        $occupation->delete();

        $this->assertDatabaseMissing('locations', ['id' => $occupation->location->id]);
    }
}
