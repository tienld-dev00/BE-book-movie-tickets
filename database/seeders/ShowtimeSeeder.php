<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShowtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (\DB::table('showtimes')->count() > 0) {
            return;
        }

        $now = Carbon::now();

        \DB::table('showtimes')->insert([
            [
                'start_time' => '2024-06-10 14:00:00',
                'end_time' => '2024-06-10 16:00:00',
                'price' => 10.00,
                'movie_id' => 1,
                'room_id' => 1,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'start_time' => '2024-06-10 17:00:00',
                'end_time' => '2024-06-10 19:00:00',
                'price' => 12.50,
                'movie_id' => 2,
                'room_id' => 1,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
