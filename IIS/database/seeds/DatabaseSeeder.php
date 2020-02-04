<?php

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
        $this->call(UserTableSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(EventInstanceSeeder::class);
        //$this->call(TicketSeeder::class);
    }
}
