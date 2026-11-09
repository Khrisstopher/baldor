import { Head, Link, usePage } from '@inertiajs/react';
import AppLogoIcon from '@/components/app-logo-icon';
import { dashboard, login, register } from '@/routes';
import { t } from '@/lib/i18n';

const features = [
    {
        title: 'All the book',
        text: "Every exercise from Baldor's algebra in one place.",
    },
    {
        title: 'Step by step',
        text: 'Move through topics, from the simplest to the most complex.',
    },
    {
        title: 'Unlimited practice',
        text: 'Solve, check your answers and try again.',
    },
];

export default function Welcome() {
    const { auth, name } = usePage().props;

    return (
        <>
            <Head title={t('Welcome')} />
            <div className="flex min-h-screen flex-col bg-background text-foreground">
                <header className="mx-auto flex w-full max-w-5xl items-center justify-between p-6">
                    <div className="flex items-center gap-3">
                        <AppLogoIcon className="h-10 w-auto" />
                        <span className="text-lg font-bold">{name}</span>
                    </div>
                    <nav className="flex items-center justify-end gap-4 text-sm">
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="rounded-md bg-primary px-4 py-2 font-medium text-primary-foreground hover:bg-primary/90"
                            >
                                {t('Enter')}
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={login()}
                                    className="rounded-md px-4 py-2 font-medium text-primary hover:bg-accent"
                                >
                                    {t('Log in')}
                                </Link>
                                <Link
                                    href={register()}
                                    className="rounded-md bg-cta px-4 py-2 font-medium text-cta-foreground hover:bg-cta/90"
                                >
                                    {t('Register')}
                                </Link>
                            </>
                        )}
                    </nav>
                </header>
                <main className="mx-auto flex w-full max-w-5xl flex-1 flex-col items-center justify-center px-6 py-16 text-center">
                    <h1 className="max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl">
                        {t('Learn algebra by solving every Baldor exercise')}
                    </h1>

                    <p className="mt-6 max-w-2xl text-lg text-muted-foreground">
                        {t(
                            'Practice step by step with the book exercises, organized by topic and in order of difficulty.',
                        )}
                    </p>

                    {!auth.user && (
                        <div className="mt-10 flex flex-col gap-3 sm:flex-row">
                            <Link
                                href={register()}
                                className="rounded-md bg-cta px-8 py-3 text-base font-semibold text-cta-foreground hover:bg-cta/90"
                            >
                                {t('Get started')}
                            </Link>
                            <Link
                                href={login()}
                                className="rounded-md border border-primary px-8 py-3 text-base font-semibold text-primary hover:bg-accent"
                            >
                                {t('I already have an account')}
                            </Link>
                        </div>
                    )}

                    <div className="mt-16 grid w-full gap-6 sm:grid-cols-3">
                        {features.map((feature) => (
                            <div
                                key={feature.title}
                                className="rounded-lg border border-border p-6 text-left"
                            >
                                <h2 className="font-semibold text-primary">
                                    {t(feature.title)}
                                </h2>
                                <p className="mt-2 text-sm text-muted-foreground">
                                    {t(feature.text)}
                                </p>
                            </div>
                        ))}
                    </div>
                </main>
                <footer className="p-6 text-center text-sm text-muted-foreground">
                    © {new Date().getFullYear()} {name}
                </footer>
            </div>
        </>
    );
}
