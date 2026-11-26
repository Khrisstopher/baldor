<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Exercise;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ExerciseController extends Controller
{
    public function show(Request $request, Topic $topic, Exercise $exercise): Response
    {
        abort_unless($exercise->topic_id === $topic->id, 404);

        $userId = $request->user()->id;

        $previousExerciseId = $topic->exercises()
            ->where('position', '<', $exercise->position)
            ->orderByDesc('position')
            ->value('id');

        $nextExerciseId = $topic->exercises()
            ->where('position', '>', $exercise->position)
            ->orderBy('position')
            ->value('id');

        $exercise->loadExists([
            'attempts as solved' => function (Builder $attempts) use ($userId) {
                $attempts
                    ->where('user_id', $userId)
                    ->where('is_correct', true);
            },
        ]);

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
                'solved' => (bool) $exercise->getAttribute('solved'),
            ],
            'previousExercise' => $previousExerciseId,
            'nextExercise' => $nextExerciseId,
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
