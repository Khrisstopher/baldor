import translations from '../../../lang/es.json';

export function t(key: string): string {
    return translations[key as keyof typeof translations] ?? key;
}
