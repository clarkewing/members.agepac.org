<?php

namespace Tests\Unit;

use App\Location;
use App\Profile;
use Illuminate\Support\Arr;
use Tests\TestCase;

class HasLocationTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = create(Profile::class); // Uses HasLocation
    }

    /** @test */
    public function testHasALocation()
    {
        $this->assertNotNull($this->model->location);
        $this->assertInstanceOf(Location::class, $this->model->location);
    }

    /** @test */
    public function testAssociatedLocationIsDeletedOnDelete()
    {
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
        $this->model->location()->delete();

        $this->assertDatabaseCount('locations', 0);

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
        $this->assertDatabaseCount('locations', 1);

        $this->model->setLocation(null);

        $this->assertNull($this->model->location);
        $this->assertDatabaseCount('locations', 0);
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
