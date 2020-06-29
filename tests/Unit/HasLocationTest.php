<?php

namespace Tests\Unit;

use App\Location;
use App\User;
use Illuminate\Support\Arr;
use Tests\TestCase;

class HasLocationTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = create(User::class); // Uses HasLocation
    }

    /** @test */
    public function testHasALocation()
    {
        $this->assertNull($this->model->location);

        $location = $this->createModelLocation();

        $this->assertNotNull(($this->model->refresh())->location);
        $this->assertInstanceOf(Location::class, $this->model->location);
        $this->assertEquals($location->id, $this->model->location->id);
    }

    /** @test */
    public function testAssociatedLocationIsDeletedOnDelete()
    {
        $this->createModelLocation();

        $this->assertDatabaseCount('locations', 1);

        $this->model->delete();

        $this->assertDatabaseCount('locations', 0);
    }

    /** @test */
    public function testSetLocationReturnsLocation()
    {
        $data = $this->getLocationData();

        // setLocation method should return the set Location.
        $this->assertInstanceOf(Location::class, $this->model->setLocation($data));

        // The set Location should have the same data.
        $this->assertEmpty(array_diff_assoc(
            $data,
            $this->model->location->toArray()
        ));
    }

    /** @test */
    public function testSetLocationCreatesALocationIfNoneExists()
    {
        $this->assertNull($this->model->location);

        $this->model->setLocation($data = $this->getLocationData());

        // The set Location should have the same data.
        $this->assertEmpty(array_diff_assoc(
            $data,
            $this->model->location->toArray()
        ));

        // The Location should be stored in the database.
        $this->assertDatabaseHas('locations', $data);
    }

    /** @test */
    public function testSetLocationUpdatesExistingLocation()
    {
        $this->createModelLocation();

        $this->assertDatabaseCount('locations', 1);

        $this->model->setLocation($data = $this->getLocationData());

        $this->assertEmpty(array_diff_assoc(
            $data,
            $this->model->location->toArray()
        ));

        $this->assertDatabaseHas('locations', $data);
        $this->assertDatabaseCount('locations', 1);
    }

    /** @test */
    public function testSetLocationWorksWithNull()
    {
        $this->createModelLocation();

        $this->assertDatabaseCount('locations', 1);

        $this->model->setLocation(null);

        $this->assertNull($this->model->location);
        $this->assertDatabaseCount('locations', 0);
    }

    /**
     * Create a location related to the model.
     *
     * @return \App\Location
     */
    protected function createModelLocation()
    {
        return create(Location::class, [
            'locatable_id' => $this->model->id,
            'locatable_type' => get_class($this->model),
        ]);
    }

    /**
     * Make an array of location parameters.
     *
     * @return array
     */
    protected function getLocationData()
    {
        return Arr::except(
            make(Location::class)->toArray(),
            ['locatable_id', 'locatable_type']
        );
    }
}
