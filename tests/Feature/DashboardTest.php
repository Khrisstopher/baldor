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

// Comprueba que los intentos de otros usuarios no afectan el progreso del usuario autenticado.
test('progress only counts the authenticated user attempts', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $topic = Topic::factory()->has(
        Exercise::factory()->count(2)->sequence(fn ($s) => ['position' => $s->index + 1])
    )->create();

    Attempt::factory()->correct()->for($other)->for($topic->exercises()->first())->create();

    $this->actingAs($user)->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('progress.solved', 0)
            ->where('lastActivity', null));
});

// Comprueba que un intento incorrecto se muestra como última actividad pero no cuenta como resuelto.
test('a wrong attempt is last activity but not solved', function () {
    $user = User::factory()->create();
    $topic = Topic::factory()->has(
        Exercise::factory()->count(2)->sequence(fn ($s) => ['position' => $s->index + 1])
    )->create();

    Attempt::factory()->for($user)->for($topic->exercises()->first())->create();

    $this->actingAs($user)->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('progress.solved', 0)
            ->where('lastActivity.exercise', 1));
});

// Comprueba que un tema con todos los ejercicios resueltos se cuenta como completado.
test('a topic with all exercises solved counts as completed', function () {
    $user = User::factory()->create();
    $topic = Topic::factory()->has(
        Exercise::factory()->count(2)->sequence(fn ($s) => ['position' => $s->index + 1])
    )->create();

    $topic->exercises->each(fn ($e) => Attempt::factory()->correct()->for($user)->for($e)->create());

    $this->actingAs($user)->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('progress.topicsDone', 1)
            ->where('progress.solved', 2));
});
