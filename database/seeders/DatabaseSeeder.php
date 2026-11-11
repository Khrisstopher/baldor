<?php

namespace Database\Seeders;

use App\Models\Attempt;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            TopicSeeder::class,
            ExerciseSeeder::class,
        ]);

        $topic = Topic::query()
            ->orderBy('position')
            ->first();

        if ($topic) {
            $exercises = $topic->exercises()
                ->orderBy('position')
                ->take(3)
                ->get();

            $exercises->each(function ($exercise) use ($user) {
                $alreadySolved = Attempt::query()
                    ->where('user_id', $user->id)
                    ->where('exercise_id', $exercise->id)
                    ->where('is_correct', true)
                    ->exists();

                if (! $alreadySolved) {
                    Attempt::factory()
                        ->correct()
                        ->for($user)
                        ->for($exercise)
                        ->create();
                }
            });
        }
    }
}
