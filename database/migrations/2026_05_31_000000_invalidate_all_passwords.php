<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InvalidateAllPasswords extends Migration
{
    /**
     * Post-incident remediation: invalidate every active user's password so
     * they are forced through the email-based reset flow on next login.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('password')
            ->where('password', '!=', User::PASSWORD_RESET_SENTINEL)
            ->update([
                'password' => User::PASSWORD_RESET_SENTINEL,
                'remember_token' => null,
            ]);

        if (Schema::hasTable('password_resets')) {
            DB::table('password_resets')->truncate();
        }

        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->truncate();
        }
    }

    public function down(): void
    {
        // No-op: invalidated passwords cannot be restored. Roll back by
        // restoring from a pre-incident database backup.
    }
}
