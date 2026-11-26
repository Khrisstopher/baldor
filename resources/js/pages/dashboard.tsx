import { Head, Link, usePage } from '@inertiajs/react';
import type { LastActivity, Progress, TopicSummary } from '@/types';

import { dashboard } from '@/routes';
import { show } from '@/routes/topics';
import { show as showExercise } from '@/routes/exercises';
import { t } from '@/lib/i18n';

type Props = {
    lastActivity: LastActivity;
    progress: Progress;
    topics: TopicSummary[];
    allCompleted: boolean;
};

function topicStatus(done: number, total: number) {
    if (done === 0) {
        return {
            label: 'Not started',
            style: 'bg-muted text-muted-foreground',
        };
    }
    if (done >= total) {
        return {
            label: 'Completed',
            style: 'bg-success text-success-foreground',
        };
    }
    return {
        label: 'In progress',
        style: 'bg-primary text-primary-foreground',
    };
}

export default function Dashboard({
    lastActivity,
    progress,
    topics,
    allCompleted,
}: Props) {
    const { auth } = usePage().props;

    const percent = progress.total
        ? Math.round((progress.solved / progress.total) * 100)
        : 0;

    return (
        <>
            <Head title={t('Home')} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <h1 className="text-2xl font-bold">
                    {t('Hello')}, {auth.user.name}
                </h1>

                {/* Bloque 1: continuar o felicitar al usuario */}
                <section className="rounded-xl border border-border bg-card p-6 text-card-foreground">
                    {allCompleted ? (
                        <>
                            <p className="text-sm font-semibold tracking-wide text-success uppercase">
                                {t('All completed')}
                            </p>

                            <h2 className="mt-2 text-xl font-bold">
                                {t('Congratulations!')}
                            </h2>

                            <p className="mt-2 text-muted-foreground">
                                {t(
                                    'You have completed all the exercises. Excellent work!',
                                )}
                            </p>
                        </>
                    ) : (
                        <>
                            <p className="text-sm font-semibold tracking-wide text-primary uppercase">
                                {lastActivity
                                    ? t('Continue where you left off')
                                    : t('Start with the first topic')}
                            </p>

                            {lastActivity ? (
                                <>
                                    <h2 className="mt-2 text-xl font-bold">
                                        {lastActivity.topic}
                                    </h2>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        {t('Exercise')} {lastActivity.exercise}{' '}
                                        {t('of')} {lastActivity.total}
                                    </p>
                                </>
                            ) : (
                                <p className="mt-2 text-muted-foreground">
                                    {t(
                                        'Practice step by step with the book exercises, organized by topic and in order of difficulty.',
                                    )}
                                </p>
                            )}

                            {lastActivity || topics.length > 0 ? (
                                <Link
                                    href={
                                        lastActivity
                                            ? showExercise({
                                                  topic: lastActivity.slug,
                                                  exercise:
                                                      lastActivity.exerciseId,
                                              })
                                            : show({ topic: topics[0].slug })
                                    }
                                    className="mt-5 inline-block rounded-md bg-cta px-6 py-2.5 text-sm font-semibold text-cta-foreground hover:bg-cta/90"
                                >
                                    {lastActivity ? t('Continue') : t('Start')}
                                </Link>
                            ) : null}
                        </>
                    )}
                </section>

                {/* Bloque 2: progreso general */}
                <section className="rounded-xl border border-border bg-card p-6 text-card-foreground">
                    <div className="flex items-baseline justify-between">
                        <h2 className="text-lg font-bold">
                            {t('Overall progress')}
                        </h2>
                        <span className="text-2xl font-extrabold text-primary">
                            {percent}%
                        </span>
                    </div>

                    <div
                        role="progressbar"
                        aria-valuenow={percent}
                        aria-valuemin={0}
                        aria-valuemax={100}
                        className="mt-4 h-3 w-full overflow-hidden rounded-full bg-muted"
                    >
                        <div
                            className="h-full rounded-full bg-primary"
                            style={{ width: `${percent}%` }}
                        />
                    </div>

                    <div className="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <p className="text-sm text-muted-foreground">
                                {t('Exercises solved')}
                            </p>
                            <p className="text-xl font-semibold">
                                {progress.solved} {t('of')} {progress.total}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">
                                {t('Topics completed')}
                            </p>
                            <p className="text-xl font-semibold">
                                {progress.topicsDone} {t('of')}{' '}
                                {progress.topicsTotal}
                            </p>
                        </div>
                    </div>
                </section>

                {/* Bloque 3: temas del Baldor */}
                <section>
                    <h2 className="mb-4 text-lg font-bold">{t('Topics')}</h2>

                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {topics.map((topic) => {
                            const status = topicStatus(topic.done, topic.total);
                            const topicPercent = topic.total
                                ? Math.round((topic.done / topic.total) * 100)
                                : 0;

                            return (
                                <Link
                                    key={topic.id}
                                    href={show({ topic: topic.slug })}
                                    className="block rounded-xl border border-border bg-card p-5 text-card-foreground transition hover:border-primary"
                                >
                                    <div className="flex items-start justify-between gap-3">
                                        <h3 className="font-semibold">
                                            {topic.title}
                                        </h3>
                                        <span
                                            className={`shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium ${status.style}`}
                                        >
                                            {t(status.label)}
                                        </span>
                                    </div>

                                    <div className="mt-4 h-2 w-full overflow-hidden rounded-full bg-muted">
                                        <div
                                            className="h-full rounded-full bg-primary"
                                            style={{
                                                width: `${topicPercent}%`,
                                            }}
                                        />
                                    </div>

                                    <p className="mt-2 text-sm text-muted-foreground">
                                        {topic.done} {t('of')} {topic.total}{' '}
                                        {t('exercises')}
                                    </p>
                                </Link>
                            );
                        })}
                    </div>
                </section>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: t('Home'),
            href: dashboard(),
        },
    ],
};
