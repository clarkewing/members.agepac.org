<?php

namespace Tests\Feature;

use App\Models\User;
use ClarkeWing\LegacySync\Facades\LegacySync;
use ClarkeWing\LegacySync\LegacySyncManager;
use Tests\Helpers\LegacySyncHelpers as Helpers;
use Tests\TestCase;

class LegacySyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        LegacySync::swap(new LegacySyncManager);
        Helpers::setupNewDatabase();
    }

    /** @test */
    public function testANewlyCreatedUserIsSyncedToTheNewApp()
    {
        $user = User::factory()->create(['first_name' => 'Jimmy', 'last_name' => 'Cricket']);

        Helpers::verifyNewSync($user->getTable(), $user->getKey(), ['first_name' => 'Jimmy', 'last_name' => 'Cricket']);
    }
}
