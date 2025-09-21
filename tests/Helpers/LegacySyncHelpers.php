<?php

namespace Tests\Helpers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class LegacySyncHelpers
{
    /**
     * Setup new database with in-memory SQLite
     */
    public static function setupNewDatabase(): void
    {
        config()->set('database.connections.squawk', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Purge and reconnect to force Laravel to use the new config
        DB::purge('squawk');
        DB::reconnect('squawk');

        Schema::connection('squawk')->create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('class_course', 30)->nullable();
            $table->year('class_year')->nullable();
            $table->string('gender', 1);
            $table->date('birth_date')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('avatar_path')->nullable();
            $table->unsignedInteger('reputation')->default(0);
            $table->text('bio')->nullable();
            $table->unsignedMediumInteger('flight_hours')->nullable();
            $table->rememberToken();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->string('stripe_id')->nullable();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });
    }

    /**
     * Setup legacy database and verify sync between databases
     *
     * @param  string  $table  The table name to check
     * @param  int  $id  The record ID to check
     * @param  array  $expectedMappings  Key-value pairs of expected column mappings to verify
     */
    public static function verifyNewSync(string $table, int $id, array $expectedMappings = []): void
    {
        $legacyRecord = DB::connection('squawk')
            ->table($table)
            ->where('id', $id)
            ->first();

        assertNotNull($legacyRecord);

        foreach ($expectedMappings as $column => $value) {
            assertEquals($legacyRecord->{$column}, $value);
        }
    }
}
