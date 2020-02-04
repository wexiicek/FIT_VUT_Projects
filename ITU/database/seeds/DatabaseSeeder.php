<?php

use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        User::create([
           'name' => 'Dominik',
           'password' => bcrypt('asd'),
           'phone_number' => '421888444555',
           'username' => 'tester',
           'email' => 'test@test.com',
           'street' => 'Ceska 420',
           'city' => 'Brno',
           'zip' => '69420',
           'state' => 'Czechia'
        ]);
    }
}
