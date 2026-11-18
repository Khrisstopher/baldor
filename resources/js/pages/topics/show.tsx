import { Head, Link } from '@inertiajs/react';
import { t } from '@/lib/i18n';
import { show as showExercise } from '@/routes/exercises';

type Exercise = {
    id: number;
    position: number;
    statement: string;
    solved: boolean;
};

type Topic = {
    id: number;
    slug: string;
    title: string;
    description: string | null;
    exercises: Exercise[];
};

type Props = {
    topic: Topic;
};

export default function Show({ topic }: Props) {
    const solved = topic.exercises.filter((exercise) => exercise.solved).length;

    return (
        <>
            <Head title={topic.title} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <section>
                    <p className="text-sm text-muted-foreground">
                        {t('Topics')} / {topic.title}
                    </p>

                    <h1 className="mt-2 text-2xl font-bold">{topic.title}</h1>

                    {topic.description && (
                        <p className="mt-2 text-muted-foreground">
                            {topic.description}
                        </p>
                    )}
                </section>

                <section className="rounded-xl border border-border bg-card p-5 text-card-foreground">
                    <div className="flex items-center justify-between">
                        <h2 className="font-semibold">{t('Topic progress')}</h2>

                        <span className="text-sm text-muted-foreground">
                            {solved} {t('of')} {topic.exercises.length}{' '}
                            {t('solved')}
                        </span>
                    </div>

                    <div className="mt-3 h-2 w-full overflow-hidden rounded-full bg-muted">
                        <div
                            className="h-full rounded-full bg-primary transition-all"
                            style={{
                                width: `${
                                    topic.exercises.length
                                        ? (solved / topic.exercises.length) *
                                          100
                                        : 0
                                }%`,
                            }}
                        />
                    </div>
                </section>

                <section>
                    <h2 className="mb-4 text-lg font-bold">{t('Exercises')}</h2>

                    <div className="flex flex-col gap-3">
                        {topic.exercises.map((exercise) => (
                            <Link
                                key={exercise.id}
                                href={showExercise({
                                    topic: topic.slug,
                                    exercise: exercise.id,
                                })}
                                className="block rounded-xl border border-border bg-card p-5 text-card-foreground transition-colors hover:bg-muted/50 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                            >
                                <div className="flex items-start justify-between gap-3">
                                    <h3 className="font-semibold">
                                        {t('Exercise')} {exercise.position}
                                    </h3>

                                    <span
                                        className={`shrink-0 rounded-full px-2.5 py-1 text-xs font-medium ${
                                            exercise.solved
                                                ? 'bg-success text-success-foreground'
                                                : 'bg-muted text-muted-foreground'
                                        }`}
                                    >
                                        {exercise.solved
                                            ? t('Solved')
                                            : t('Pending')}
                                    </span>
                                </div>

                                <p className="mt-3 text-sm leading-relaxed">
                                    {exercise.statement}
                                </p>
                            </Link>
                        ))}
                    </div>
                </section>
            </div>
        </>
    );
}
