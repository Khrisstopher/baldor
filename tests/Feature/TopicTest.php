<?php

use App\Models\Exercise;
use App\Models\Topic;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

// Comprueba que los invitados son redirigidos al login.
test('guests are redirected to the login page', function () {
    $topic = Topic::factory()->create();

    $this->get(route('topics.show', $topic))
        ->assertRedirect(route('login'));
});

// Comprueba que un usuario autenticado puede visualizar un tema y sus ejercicios.
test('authenticated users can view a topic with its exercises', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()
        ->has(
            Exercise::factory()->count(3)->sequence(
                fn ($s) => ['position' => $s->index + 1]
            )
        )
        ->create();

    $this->actingAs($user)
        ->get(route('topics.show', $topic))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('topics/show')
            ->where('topic.id', $topic->id)
            ->where('topic.slug', $topic->slug)
            ->where('topic.title', $topic->title)
            ->has('topic.exercises', 3)
            ->where('topic.exercises.0.position', 1)
            ->where('topic.exercises.1.position', 2)
            ->where('topic.exercises.2.position', 3)
        );
});

// Comprueba que las opciones de los ejercicios no se envían a Inertia.
test('topic exercises do not expose their options', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $topic = Topic::factory()
        ->has(
            Exercise::factory()->count(2)->sequence(
                fn ($s) => ['position' => $s->index + 1]
            )
        )
        ->create();

    $this->actingAs($user)
        ->get(route('topics.show', $topic))
        ->assertInertia(fn (Assert $page) => $page
            ->component('topics/show')
            ->has('topic.exercises', 2)
            ->missing('topic.exercises.0.options')
            ->missing('topic.exercises.1.options')
        );
});
