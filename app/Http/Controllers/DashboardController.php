<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Topic;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $userId = $request->user()->id;

        $topics = Topic::query()
            ->withCount([
                'exercises as total',
                'exercises as done' => fn ($q) => $q->whereHas('attempts',
                    fn ($a) => $a->where('user_id', $userId)->where('is_correct', true)),
            ])
            ->orderBy('position')
            ->get()
            ->map(fn (Topic $t) => [
                'id' => $t->id,
                'title' => $t->title,
                'done' => (int) $t->done,
                'total' => (int) $t->total,
            ]);

        $last = Attempt::query()
            ->where('user_id', $userId)
            ->with('exercise.topic')
            ->latest('id')
            ->first();

        return Inertia::render('dashboard', [
            'lastActivity' => $last ? [
                'topic' => $last->exercise->topic->title,
                'exercise' => $last->exercise->position,
                'total' => $topics->firstWhere('id', $last->exercise->topic_id)['total'] ?? 0,
            ] : null,
            'progress' => [
                'solved' => $topics->sum('done'),
                'total' => $topics->sum('total'),
                'topicsDone' => $topics->filter(fn ($t) => $t['total'] > 0 && $t['done'] >= $t['total'])->count(),
                'topicsTotal' => $topics->count(),
            ],
            'topics' => $topics,
        ]);
    }
}
