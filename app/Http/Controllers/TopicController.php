<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TopicController extends Controller
{
    public function show(Request $request, Topic $topic): Response
    {
        $userId = $request->user()->id;

        $topic->load([
            'exercises' => function (HasMany $query) use ($userId) {
                $query
                    ->orderBy('position')
                    ->withExists([
                        'attempts as solved' => function (Builder $attempts) use ($userId) {
                            $attempts
                                ->where('user_id', $userId)
                                ->where('is_correct', true);
                        },
                    ]);
            },
        ]);

        return Inertia::render('topics/show', [
            'topic' => [
                'id' => $topic->id,
                'slug' => $topic->slug,
                'title' => $topic->title,
                'description' => $topic->description,
                'exercises' => $topic->exercises->map(fn ($exercise) => [
                    'id' => $exercise->id,
                    'position' => $exercise->position,
                    'statement' => $exercise->statement,
                    'options' => $exercise->options,
                    'solved' => (bool) $exercise->getAttribute('solved'),
                ]),
            ],
        ]);
    }
}
