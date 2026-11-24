<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Exercise;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ExerciseController extends Controller
{
    public function show(Topic $topic, Exercise $exercise): Response
    {
        abort_unless($exercise->topic_id === $topic->id, 404);

        $nextExercise = $topic->exercises()
            ->where('position', '>', $exercise->position)
            ->orderBy('position')
            ->first();

        return Inertia::render('exercises/show', [
            'topic' => [
                'title' => $topic->title,
                'slug' => $topic->slug,
            ],
            'exercise' => [
                'id' => $exercise->id,
                'position' => $exercise->position,
                'statement' => $exercise->statement,
                'options' => $exercise->options,
            ],
            'nextExercise' => $nextExercise
                ? [
                    'id' => $nextExercise->id,
                    'position' => $nextExercise->position,
                    'statement' => $nextExercise->statement,
                    'options' => $nextExercise->options,
                ]
                : null,
        ]);
    }

    public function store(
        Request $request,
        Topic $topic,
        Exercise $exercise
    ): RedirectResponse {
        abort_unless($exercise->topic_id === $topic->id, 404);

        $validated = $request->validate([
            'answer' => [
                'required',
                'string',
                Rule::in($exercise->options),
            ],
        ]);

        $isCorrect = $validated['answer'] === $exercise->answer;

        $attempt = new Attempt;
        $attempt->user()->associate($request->user());
        $attempt->given_answer = $validated['answer'];
        $attempt->is_correct = $isCorrect;

        $exercise->attempts()->save($attempt);

        return back()->with(
            'result',
            $isCorrect ? 'correct' : 'incorrect'
        );
    }
}
