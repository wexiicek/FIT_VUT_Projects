<?php

use Illuminate\Database\Seeder;
class EventInstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\EventInstance::class, 50)->create();
    }
}
