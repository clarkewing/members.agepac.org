<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        factory(User::class)
            ->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'username' => 'john.doe',
                'email' => 'john@example.com',
            ]);
    }
}
