<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $this->migrateLocationType('address', 'street');
        $this->migrateLocationType('busStop', 'bus');
        $this->migrateLocationType('trainStation', 'train');
    }

    public function down(): void
    {
        $this->migrateLocationType('street', 'address');
        $this->migrateLocationType('bus', 'busStop');
        $this->migrateLocationType('train', 'trainStation');
    }

    protected function migrateLocationType(string $from, string $to): void
    {
        DB::table('locations')
            ->where('type', $from)
            ->update(['type' => $to]);
    }
};
