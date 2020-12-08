<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        User::truncate();

        Schema::enableForeignKeyConstraints();

        User::factory()
            ->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'username' => 'john.doe',
                'email' => 'john@example.com',
            ])
            ->assignRole('Administrator');

        User::factory()->count(50)
            ->create();
    }
}
