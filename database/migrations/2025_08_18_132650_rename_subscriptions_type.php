<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::table('subscriptions')
            ->where('name', 'default')
            ->update(['name' => 'membership']);
    }

    public function down(): void
    {
        DB::table('subscriptions')
            ->where('name', 'membership')
            ->update(['name' => 'default']);
    }
};
