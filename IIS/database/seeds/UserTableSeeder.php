<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'firstname' => 'Anna',
            'lastname' => 'Dee',
            'username' => 'admin',
            'phoneNumber' => rand(10000000,999999999),
            'email' => 'admin.vut.proj@seznam.cz',
            'role' => 'admin',
            'password' => bcrypt('admin'),
        ]);

        DB::table('users')->insert([
            'firstname' => 'Jenna',
            'lastname' => 'Marble',
            'username' => 'cashier',
            'phoneNumber' => rand(10000000,999999999),
            'email' => 'cashier.vut.poj@seznam.cz',
            'role' => 'cashier',
            'password' => bcrypt('cashier'),
        ]);

        DB::table('users')->insert([
            'firstname' => 'Josh',
            'lastname' => 'Nash',
            'username' => 'director',
            'phoneNumber' => Str::random(10),
            'email' => 'director.vut.proj@seznam.cz',
            'role' => 'director',
            'password' => bcrypt('director'),
        ]);

        DB::table('users')->insert([
            'firstname' => 'Harry',
            'lastname' => 'Potter',
            'username' => 'user',
            'phoneNumber' => Str::random(10),
            'email' => 'user.vut.proj@seznam.cz',
            'role' => 'user',
            'password' => bcrypt('user'),
        ]);

        //factory(App\Models\User::class, 15)->create();
    }
}
