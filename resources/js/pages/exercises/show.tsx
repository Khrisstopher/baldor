import { Head, Link, router } from '@inertiajs/react';
import { t } from '@/lib/i18n';
import { show as showTopic } from '@/routes/topics';
import { store } from '@/routes/exercises/attempts';
import { show as showExercise } from '@/routes/exercises';
import { useState } from 'react';

type Topic = {
    title: string;
    slug: string;
};

type Exercise = {
    id: number;
    position: number;
    statement: string;
    options: string[];
    solved: boolean;
};

type Props = {
    topic: Topic;
    exercise: Exercise;
    previousExercise: number | null;
    nextExercise: number | null;
    nextTopic: {
        slug: string;
        title: string;
    } | null;
};

export default function Show({
    topic,
    exercise,
    previousExercise,
    nextExercise,
    nextTopic,
}: Props) {
    const [selectedOption, setSelectedOption] = useState<string | null>(null);
    const [result, setResult] = useState<'correct' | 'incorrect' | null>(null);
    const [processing, setProcessing] = useState(false);

    function checkAnswer() {
        if (selectedOption === null || processing || result === 'correct')
            return;

        setProcessing(true);

        router.post(
            store({
                topic: topic.slug,
                exercise: exercise.id,
            }).url,
            {
                answer: selectedOption,
            },
            {
                onSuccess: (page) => {
                    const flash = (
                        page.props as {
                            flash?: { result?: string };
                        }
                    ).flash;

                    setResult(
                        flash?.result === 'correct'
                            ? 'correct'
                            : flash?.result === 'incorrect'
                              ? 'incorrect'
                              : null,
                    );
                },
                onFinish: () => {
                    setProcessing(false);
                },
            },
        );
    }

    return (
        <>
            <Head title={`${t('Exercise')} ${exercise.position}`} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <section>
                    <Link
                        href={showTopic({ topic: topic.slug })}
                        className="text-sm text-muted-foreground hover:text-foreground"
                    >
                        {t('Back to')} {topic.title}
                    </Link>

                    <h1 className="mt-4 text-2xl font-bold">{topic.title}</h1>

                    <div className="mt-2 flex flex-wrap items-center gap-3">
                        <p className="text-muted-foreground">
                            {t('Exercise')} {exercise.position}
                        </p>

                        {exercise.solved && (
                            <span className="inline-flex items-center rounded-full border border-success/30 bg-success/10 px-3 py-1 text-xs font-semibold text-success">
                                ✓ {t('Already solved')}
                            </span>
                        )}
                    </div>
                </section>

                <section className="rounded-xl border border-border bg-card p-5 text-card-foreground">
                    <h2 className="text-lg font-semibold">
                        {exercise.statement}
                    </h2>

                    <div className="mt-6 flex flex-col gap-3">
                        {exercise.options.map((option, index) => {
                            const isSelected = selectedOption === option;
                            const isCorrect =
                                isSelected && result === 'correct';
                            const isIncorrect =
                                isSelected && result === 'incorrect';

                            return (
                                <button
                                    key={index}
                                    type="button"
                                    aria-pressed={isSelected}
                                    disabled={
                                        result === 'correct' || processing
                                    }
                                    onClick={() => {
                                        setSelectedOption(option);
                                        setResult(null);
                                    }}
                                    className={`rounded-lg border p-4 text-left transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-default ${
                                        isCorrect
                                            ? 'border-success bg-success/10'
                                            : isIncorrect
                                              ? 'border-destructive bg-destructive/10'
                                              : isSelected
                                                ? 'border-primary bg-primary/10'
                                                : 'border-border hover:bg-muted'
                                    }`}
                                >
                                    <span className="font-medium">
                                        {String.fromCharCode(65 + index)}.
                                    </span>{' '}
                                    {option}
                                    {isCorrect && (
                                        <span
                                            role="status"
                                            className="mt-3 block border-t border-success/30 pt-3 text-sm font-semibold text-success"
                                        >
                                            {t('Correct! Well done.')}
                                        </span>
                                    )}
                                    {isIncorrect && (
                                        <span
                                            role="status"
                                            className="mt-3 block border-t border-destructive/30 pt-3 text-sm font-semibold text-destructive"
                                        >
                                            {t('Incorrect answer. Try again.')}
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                    <div className="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        {previousExercise !== null && (
                            <Link
                                href={showExercise({
                                    topic: topic.slug,
                                    exercise: previousExercise,
                                })}
                                className="inline-flex justify-center rounded-lg border border-border px-5 py-3 text-center font-medium transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                            >
                                ← {t('Previous exercise')}
                            </Link>
                        )}

                        <button
                            type="button"
                            onClick={checkAnswer}
                            disabled={
                                selectedOption === null ||
                                processing ||
                                result === 'correct'
                            }
                            className="rounded-lg bg-primary px-5 py-3 text-center font-medium text-primary-foreground transition-opacity hover:opacity-90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {processing
                                ? t('Checking...')
                                : result === 'correct'
                                  ? t('Correct!')
                                  : t('Check Answer')}
                        </button>

                        {(exercise.solved || result === 'correct') &&
                            (nextExercise !== null ? (
                                <Link
                                    href={showExercise({
                                        topic: topic.slug,
                                        exercise: nextExercise,
                                    })}
                                    className="inline-flex justify-center rounded-lg bg-cta px-5 py-3 text-center font-medium text-cta-foreground transition-opacity hover:opacity-90 focus-visible:ring-2 focus-visible:ring-cta focus-visible:outline-none"
                                >
                                    {t('Next exercise')} →
                                </Link>
                            ) : nextTopic !== null ? (
                                <Link
                                    href={showTopic({ topic: nextTopic.slug })}
                                    className="inline-flex justify-center rounded-lg bg-cta px-5 py-3 text-center font-medium text-cta-foreground transition-opacity hover:opacity-90 focus-visible:ring-2 focus-visible:ring-cta focus-visible:outline-none"
                                >
                                    {t('Next topic')} →
                                </Link>
                            ) : null)}
                    </div>
                </section>
            </div>
        </>
    );
}
