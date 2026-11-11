<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = Topic::all();

        foreach ($topics as $topic) {
            Exercise::factory()
                ->count(5)
                ->for($topic)
                ->sequence(fn (Sequence $sequence) => [
                    'position' => $sequence->index + 1,
                ])
                ->create();
        }
    }
}
