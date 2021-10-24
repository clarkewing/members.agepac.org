<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserProfilePhotoField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo_path', 2048)->nullable()->after('phone');
        });

        DB::transaction(function () {
            User::whereNotNull('avatar_path')->chunk(10, function ($users) {
                foreach ($users as $user) {
                    $newPath = 'profile-photos/' . basename($user->avatar_path);

                    Storage::move($user->avatar_path, $newPath);

                    $user->update(['profile_photo_path' => $newPath]);
                }
            });
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('phone');
        });

        DB::transaction(function () {
            User::whereNotNull('profile_photo_path')->chunk(10, function ($users) {
                foreach ($users as $user) {
                    $newPath = 'avatars/' . basename($user->profile_photo_path);

                    Storage::move($user->profile_photo_path, $newPath);

                    $user->update(['avatar_path' => $newPath]);
                }
            });
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo_path');
        });
    }
}
