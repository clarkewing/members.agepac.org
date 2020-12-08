<?php

namespace Tests\Feature\Nova;

use App\Models\Aircraft;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageAircraftTest extends TestCase
{
    use NovaTestRequests;

    public function permissionProvider()
    {
        return [
            'edit' => ['edit'],
            'delete' => ['delete'],
        ];
    }

    /** @test */
    public function testUnauthorizedUsersCannotIndexAircraft()
    {
        $this->signIn();

        $this->indexResource('aircraft')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewAnAircraft()
    {
        $aircraft = Aircraft::create(['name' => 'FooJet']);

        $this->signIn();

        $this->showResource('aircraft', $aircraft->id)
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotCreateAnAircraft()
    {
        $this->signIn();

        $this->storeAircraft(['name' => 'FakeBus'])
            ->assertForbidden();

        $this->assertDatabaseMissing('aircraft', ['name' => 'FakeBus']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditAnAircraft()
    {
        $aircraft = Aircraft::create(['name' => 'FooJet']);

        $this->signIn();

        $this->updateAircraft(['name' => 'FakeBus'], $aircraft)
            ->assertForbidden();

        $this->assertEquals('FooJet', $aircraft->fresh()->name);
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteAnAircraft()
    {
        $aircraft = Aircraft::create(['name' => 'FooJet']);

        $this->signIn();

        $this->deleteResource('aircraft', $aircraft->id)
            ->assertForbidden();

        $this->assertDatabaseHas('aircraft', ['id' => $aircraft->id]);
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanIndexAircraft($permission)
    {
        $this->signInWithPermission('aircraft.'.$permission);

        $this->indexResource('aircraft')
            ->assertOk();
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanViewAnAircraft($permission)
    {
        $aircraft = Aircraft::create(['name' => 'FooJet']);

        $this->signInWithPermission('aircraft.'.$permission);

        $this->showResource('aircraft', $aircraft->id)
            ->assertOk();
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanCreateAnAircraft($permission)
    {
        $this->signInWithPermission('aircraft.'.$permission);

        $this->storeAircraft(['name' => 'TurboFoo'])
            ->assertCreated();

        $this->assertDatabaseHas('aircraft', ['name' => 'TurboFoo']);
    }

    /** @test */
    public function testAuthorizedUsersCanEditAnAircraft()
    {
        $aircraft = Aircraft::create(['name' => 'FooJet']);

        $this->signInWithPermission('aircraft.edit');

        $this->updateAircraft(['name' => 'TurboFoo'], $aircraft)
            ->assertOk();

        $this->assertDatabaseHas('aircraft', ['id' => $aircraft->id, 'name' => 'TurboFoo']);
    }

    /** @test */
    public function testAuthorizedUsersCanDeleteAnAircraft()
    {
        $aircraft = Aircraft::create(['name' => 'FooJet']);

        $this->signInWithPermission('aircraft.delete');

        $this->deleteResource('aircraft', $aircraft->id)
            ->assertOk();

        $this->assertDatabaseMissing('aircraft', ['id' => $aircraft->id]);
    }

    /** @test */
    public function testNameIsRequired()
    {
        $this->signInWithPermission('aircraft.edit');

        $this->updateAircraft(['name' => null])
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
     * @param  \App\Models\Aircraft|null  $aircraft
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
