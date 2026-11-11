<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            'Suma Algebraica',
            'Resta Algebraica',
            'Multiplicación Algebraica',
            'División Algebraica',
            'Productos Notables',
            'Factorización',
        ];

        foreach ($topics as $index => $title) {
            Topic::create([
                'slug' => Str::slug($title),
                'title' => $title,
                'description' => "Descripción del tema: $title",
                'position' => $index + 1,
            ]);
        }
    }
}
