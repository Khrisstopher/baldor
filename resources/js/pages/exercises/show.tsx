import { Head, Link, router } from '@inertiajs/react';
import { t } from '@/lib/i18n';
import { show as showTopic } from '@/routes/topics';
import { store } from '@/routes/exercises/attempts';
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
};

type Props = {
    topic: Topic;
    exercise: Exercise;
};

export default function Show({ topic, exercise }: Props) {
    const [selectedOption, setSelectedOption] = useState<string | null>(null);
    const [result, setResult] = useState<string | null>(null);
    const [processing, setProcessing] = useState(false);
    function checkAnswer() {
        if (selectedOption === null || processing) return;

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

                    setResult(flash?.result ?? null);
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

                    <p className="mt-2 text-muted-foreground">
                        {t('Exercise')} {exercise.position}
                    </p>
                </section>

                <section className="rounded-xl border border-border bg-card p-5 text-card-foreground">
                    <h2 className="text-lg font-semibold">
                        {exercise.statement}
                    </h2>

                    <div className="mt-6 flex flex-col gap-3">
                        {exercise.options.map((option, index) => (
                            <button
                                key={index}
                                type="button"
                                onClick={() => {
                                    setSelectedOption(option);
                                    setResult(null);
                                }}
                                className={`rounded-lg border p-4 text-left transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none ${
                                    selectedOption === option
                                        ? 'border-primary bg-primary/10'
                                        : 'border-border hover:bg-muted'
                                }`}
                            >
                                <span className="font-medium">
                                    {String.fromCharCode(65 + index)}.
                                </span>{' '}
                                {option}
                            </button>
                        ))}
                    </div>

                    <button
                        type="button"
                        onClick={checkAnswer}
                        disabled={selectedOption === null || processing}
                        className="mt-5 rounded-lg bg-primary px-5 py-3 font-medium text-primary-foreground transition-opacity focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {processing ? t('Checking...') : t('Check Answer')}
                    </button>
                    {result && (
                        <p
                            role="status"
                            className={`mt-4 rounded-lg p-4 font-medium ${
                                result === 'correct'
                                    ? 'bg-success text-success-foreground'
                                    : 'bg-destructive text-destructive-foreground'
                            }`}
                        >
                            {result === 'correct'
                                ? t('Correct! Well done.')
                                : t('Incorrect answer. Try again.')}
                        </p>
                    )}
                </section>
            </div>
        </>
    );
}
