<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('temas/{topic:slug}', [TopicController::class, 'show'])->name('topics.show');
    Route::get('temas/{topic:slug}/ejercicios/{exercise}', [ExerciseController::class, 'show'])->name('exercises.show');
    Route::post('temas/{topic:slug}/ejercicios/{exercise}/intentos', [ExerciseController::class, 'store'])->name('exercises.attempts.store');
});

require __DIR__.'/settings.php';
