<?php

namespace Tests\Feature\Nova;

use App\Aircraft;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageAircraftTest extends TestCase
{
    use NovaTestRequests;

    public function modeProvider()
    {
        return [
            'create' => ['store'],
            'edit' => ['update'],
        ];
    }

    /** @test */
    public function testAuthorizedUsersCanIndexAircraft()
    {
        $this->signInGod();

        $this->indexResource('aircraft')
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanViewAnAircraft()
    {
        $aircraft = Aircraft::create(['name' => 'FooJet']);

        $this->signInGod();

        $this->showResource('aircraft', $aircraft->id)
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanCreateAnAircraft()
    {
        $this->signInGod();

        $this->storeAircraft(['name' => 'TurboFoo'])
            ->assertCreated();

        $this->assertDatabaseHas('aircraft', ['name' => 'TurboFoo']);
    }

    /** @test */
    public function testAuthorizedUsersCanEditAnAircraft()
    {
        $aircraft = Aircraft::create(['name' => 'FooJet']);

        $this->signInGod();

        $this->updateAircraft(['name' => 'TurboFoo'], $aircraft)
            ->assertOk();

        $this->assertDatabaseHas('aircraft', ['id' => $aircraft->id, 'name' => 'TurboFoo']);
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testNameIsRequired($verb)
    {
        $this->signInGod();

        $this->{$verb . 'Aircraft'}(['name' => null])
            ->assertJsonValidationErrors('name');
    }

    /**
     * Submits a request to create a aircraft.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function storeAircraft(array $overrides = [])
    {
        return $this->storeResource('aircraft', array_merge(
            ['name' => 'FooJet'],
            $overrides
        ));
    }

    /**
     * Submits a request to update an existing aircraft.
     *
     * @param  array  $data
     * @param  \App\Aircraft|null  $aircraft
     * @return \Illuminate\Testing\TestResponse
     */
    public function updateAircraft(array $data = [], Aircraft $aircraft = null)
    {
        $aircraft = $aircraft ?? Aircraft::create(['name' => 'FooJet']);

        return $this->updateResource(
            'aircraft', $aircraft->id,
            array_merge($aircraft->toArray(), $data)
        );
    }
}
