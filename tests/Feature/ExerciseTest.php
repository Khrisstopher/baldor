<?php

use App\Models\Exercise;
use App\Models\Topic;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

// Comprueba que los invitados son redirigidos al login al intentar visualizar un ejercicio.
test('guests are redirected to the login page', function () {
    $topic = Topic::factory()->create();
    $exercise = Exercise::factory()->for($topic)->create();

    $this->get(route('exercises.show', [$topic, $exercise]))
        ->assertRedirect(route('login'));
});

// Comprueba que un usuario autenticado puede visualizar un ejercicio y sus opciones.
test('authenticated users can view an exercise with its options', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();
    $exercise = Exercise::factory()->for($topic)->create();

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $exercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('topic.title', $topic->title)
            ->where('topic.slug', $topic->slug)
            ->where('exercise.id', $exercise->id)
            ->where('exercise.position', $exercise->position)
            ->where('exercise.statement', $exercise->statement)
            ->where('exercise.options', $exercise->options)
        );
});

// Comprueba que un ejercicio perteneciente a otro tema devuelve un error 404.
test('an exercise belonging to another topic returns a 404', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();
    $anotherTopic = Topic::factory()->create();

    $exercise = Exercise::factory()->for($anotherTopic)->create();

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $exercise]))
        ->assertNotFound();
});

// Comprueba que la respuesta correcta no se expone en las props de Inertia.
test('the correct answer is not exposed in inertia props', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();
    $exercise = Exercise::factory()->for($topic)->create();

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $exercise]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->has('exercise.options')
            ->missing('exercise.answer')
        );
});

// Comprueba que una respuesta que no está entre las opciones genera un error de validación.
test('an answer outside the available options fails validation', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();
    $exercise = Exercise::factory()->for($topic)->create();

    $this->actingAs($user)
        ->post(route('exercises.attempts.store', [$topic, $exercise]), [
            'answer' => 'respuesta-invalida',
        ])
        ->assertSessionHasErrors('answer');

    $this->assertDatabaseCount('attempts', 0);
});

// Comprueba que una respuesta correcta guarda un intento con is_correct = true.
test('a correct answer saves an attempt as correct', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();
    $exercise = Exercise::factory()->for($topic)->create();

    $this->actingAs($user)
        ->post(route('exercises.attempts.store', [$topic, $exercise]), [
            'answer' => $exercise->answer,
        ])
        ->assertRedirect()
        ->assertSessionHas('result', 'correct');

    $this->assertDatabaseHas('attempts', [
        'user_id' => $user->id,
        'exercise_id' => $exercise->id,
        'given_answer' => $exercise->answer,
        'is_correct' => true,
    ]);
});

// Comprueba que una respuesta incorrecta guarda un intento con is_correct = false.
test('an incorrect answer saves an attempt as incorrect', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();
    $exercise = Exercise::factory()->for($topic)->create();

    $wrongAnswer = collect($exercise->options)
        ->first(fn ($option) => $option !== $exercise->answer);

    expect($wrongAnswer)->not->toBeNull();

    $this->actingAs($user)
        ->post(route('exercises.attempts.store', [$topic, $exercise]), [
            'answer' => $wrongAnswer,
        ])
        ->assertRedirect()
        ->assertSessionHas('result', 'incorrect');

    $this->assertDatabaseHas('attempts', [
        'user_id' => $user->id,
        'exercise_id' => $exercise->id,
        'given_answer' => $wrongAnswer,
        'is_correct' => false,
    ]);
});

test('an exercise receives the next exercise in inertia props', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();

    $exercise = Exercise::factory()->for($topic)->create([
        'position' => 1,
    ]);

    $nextExercise = Exercise::factory()->for($topic)->create([
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $exercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('nextExercise', $nextExercise->id)
        );
});

test('the last exercise receives null as next exercise', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();

    Exercise::factory()->for($topic)->create([
        'position' => 1,
    ]);

    $lastExercise = Exercise::factory()->for($topic)->create([
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $lastExercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('nextExercise', null)
        );
});

test('the next exercise is determined by position and not by id', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();

    $exerciseAtPosition30 = Exercise::factory()->for($topic)->create([
        'position' => 30,
    ]);

    $currentExercise = Exercise::factory()->for($topic)->create([
        'position' => 20,
    ]);

    Exercise::factory()->for($topic)->create([
        'position' => 40,
    ]);

    expect($exerciseAtPosition30->id)->toBeLessThan($currentExercise->id);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $currentExercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('nextExercise', $exerciseAtPosition30->id)
        );
});

test('an exercise from another topic is never returned as next exercise', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();
    $anotherTopic = Topic::factory()->create();

    $exercise = Exercise::factory()->for($topic)->create([
        'position' => 1,
    ]);

    Exercise::factory()->for($anotherTopic)->create([
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $exercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('nextExercise', null)
        );
});

test('an exercise receives the previous exercise id in inertia props', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();

    $previousExercise = Exercise::factory()->for($topic)->create([
        'position' => 1,
    ]);

    $currentExercise = Exercise::factory()->for($topic)->create([
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $currentExercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('previousExercise', $previousExercise->id)
        );
});

test('the first exercise receives null as previous exercise', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();

    $firstExercise = Exercise::factory()->for($topic)->create([
        'position' => 1,
    ]);

    Exercise::factory()->for($topic)->create([
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $firstExercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('previousExercise', null)
        );
});

test('the previous exercise is determined by position and not by id', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();

    $exerciseAtPosition15 = Exercise::factory()->for($topic)->create([
        'position' => 15,
    ]);

    Exercise::factory()->for($topic)->create([
        'position' => 10,
    ]);

    $currentExercise = Exercise::factory()->for($topic)->create([
        'position' => 20,
    ]);

    expect($exerciseAtPosition15->id)->toBeLessThan(
        $currentExercise->id
    );

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $currentExercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('previousExercise', $exerciseAtPosition15->id)
        );
});

test('an exercise from another topic is never returned as previous exercise', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();
    $anotherTopic = Topic::factory()->create();

    $currentExercise = Exercise::factory()->for($topic)->create([
        'position' => 2,
    ]);

    Exercise::factory()->for($anotherTopic)->create([
        'position' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $currentExercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('previousExercise', null)
        );
});

test('an exercise is solved only when the current user has a correct attempt', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $anotherUser = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();

    $exercise = Exercise::factory()->for($topic)->create();

    $wrongAnswer = collect($exercise->options)
        ->first(fn ($option) => $option !== $exercise->answer);

    // El usuario actual tiene un intento incorrecto.
    $exercise->attempts()->create([
        'user_id' => $user->id,
        'given_answer' => $wrongAnswer,
        'is_correct' => false,
    ]);

    // Otro usuario sí resolvió correctamente el ejercicio.
    $exercise->attempts()->create([
        'user_id' => $anotherUser->id,
        'given_answer' => $exercise->answer,
        'is_correct' => true,
    ]);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $exercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('exercise.solved', false)
        );
});

test('an exercise is solved when the current user has a correct attempt', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()->create();

    $exercise = Exercise::factory()->for($topic)->create();

    $exercise->attempts()->create([
        'user_id' => $user->id,
        'given_answer' => $exercise->answer,
        'is_correct' => true,
    ]);

    $this->actingAs($user)
        ->get(route('exercises.show', [$topic, $exercise]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('exercises/show')
            ->where('exercise.solved', true)
        );
});
