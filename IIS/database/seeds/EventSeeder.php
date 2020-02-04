<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //factory(App\Event::class, 50)->create();

        DB::table('event')->insert([
            'name' => 'IIS 1',
            'description' => 'Information and information system',
            'type' => 'Lecture',
            'performers' => 'Burget Radek, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'IIS 2',
            'description' => 'Serialization - basic formats',
            'type' => 'Lecture',
            'performers' => 'Burget Radek, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'IIS 3',
            'description' => 'Visualization - extended, DOM manipulation, JavaScript, client JavaScript',
            'type' => 'Lecture',
            'performers' => 'Burget Radek, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'IIS 4',
            'description' => 'Information Systems Architecture',
            'type' => 'Lecture',
            'performers' => 'Burget Radek, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'IIS 5',
            'description' => 'The methodology of OLTP information system design',
            'type' => 'Lecture',
            'performers' => 'Burget Radek, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'IIS 6',
            'description' => 'Other information systems',
            'type' => 'Lecture',
            'performers' => 'Burget Radek, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'IIS 7',
            'description' => 'Server - PHP, information systems application on server, database connection',
            'type' => 'Lecture',
            'performers' => 'Burget Radek, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'ITU 1',
            'description' => 'Course organization, introduction to GUI',
            'type' => 'Lecture',
            'performers' => 'Beran Vítězslav, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'ITU 2',
            'description' => 'GUI programming principles in WinAPI ',
            'type' => 'Lecture',
            'performers' => 'Beran Vítězslav, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'ITU 3',
            'description' => 'Advanced tools and libraries for Windows (WPF, .NET) ',
            'type' => 'Lecture',
            'performers' => 'Beran Vítězslav, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'ITU 4',
            'description' => 'UX in practice ',
            'type' => 'Lecture',
            'performers' => 'Beran Vítězslav, Ing., Ph.D.'
        ]);

        DB::table('event')->insert([
            'name' => 'Once upon a time in Hollywood',
            'description' => 'A faded television actor and his stunt double strive to achieve fame and success in the film industry during the final years of Hollywoods Golden Age in 1969 Los Angeles',
            'type' => 'Movie',
            'performers' => ' Leonardo DiCaprio, Brad Pitt, Margot Robbie'
        ]);

        DB::table('event')->insert([
            'name' => 'Mission: Impossible - Fallout',
            'description' => 'Ethan Hunt and his IMF team, along with some familiar allies, race against time after a mission gone wrong.',
            'type' => 'Movie',
            'performers' => ' Tom Cruise, Henry Cavill, Ving Rhames '
        ]);

        DB::table('event')->insert([
            'name' => 'Star Wars: The Last Jedi',
            'description' => 'Rey develops her newly discovered abilities with the guidance of Luke Skywalker, who is unsettled by the strength of her powers. Meanwhile, the Resistance prepares for battle with the First Order.',
            'type' => 'Movie',
            'performers' => '  Daisy Ridley, John Boyega, Mark Hamill'
        ]);

        DB::table('event')->insert([
            'name' => 'Inside man',
            'description' => 'A police detective, a bank robber, and a high-power broker enter high-stakes negotiations after the criminals brilliant heist spirals into a hostage situation.',
            'type' => 'Movie',
            'performers' => ' Denzel Washington, Clive Owen, Jodie Foster'
        ]);



    }
}
