<?php

use App\Models\Attempt;
use App\Models\Exercise;
use App\Models\Topic;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

/*
Estamos probando que todo funciona:
- Eloquent puede crear los registros relacionados.
- Las relaciones entre modelos funcionan.
- El controlador calcula correctamente los ejercicios resueltos.
- El controlador identifica la última actividad.
- Inertia entrega los datos correctos a React.
*/
test('dashboard shows progress from the database', function () {
    $user = User::factory()->create();

    $topic = Topic::factory()
        ->has(
            Exercise::factory()->count(4)->sequence(
                fn ($s) => ['position' => $s->index + 1]
            )
        )
        ->create();

    Attempt::create([
        'user_id' => $user->id,
        'exercise_id' => $topic->exercises()->first()->id,
        'given_answer' => '1',
        'is_correct' => true,
    ]);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('progress.solved', 1)
            ->where('progress.total', 4)
            ->where('lastActivity.topic', $topic->title));
});
