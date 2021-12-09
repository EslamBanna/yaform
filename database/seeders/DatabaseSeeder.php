<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // form question types
        $question_types = [
            [
                'form_q_id' => 0,
                'name' => 'Short Answer'
            ],
            [
                'form_q_id' => 1,
                'name' => 'Paragraph'
            ],
            [
                'form_q_id' => 2,
                'name' => 'Multiple choice'
            ],
            [
                'form_q_id' => 3,
                'name' => 'Checkboxes'
            ],
            [
                'form_q_id' => 4,
                'name' => 'Dropdown'
            ],
            [
                'form_q_id' => 5,
                'name' => 'Date'
            ],
            [
                'form_q_id' => 6,
                'name' => 'Time'
            ],
            [
                'form_q_id' => 7,
                'name' => 'Phone number'
            ],
            [
                'form_q_id' => 8,
                'name' => 'video'
            ],
            [
                'form_q_id' => 9,
                'name' => 'image'
            ],
            [
                'form_q_id' => 10,
                'name' => 'head'
            ],
        ];
        DB::table('form_q_types')->insert($question_types);
    }
}
