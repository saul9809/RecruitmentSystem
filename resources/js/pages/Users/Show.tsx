import { Head, Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { route } from 'ziggy-js';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'user';
}
export default function Show({ user }: { user: User }) {
    const { t } = useTranslation('common');
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('user_show_breadcrumb'),
            href: route('users.index'),
        },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('user_show_headline')} />

            <div className="p-3">
                <Link
                    className="mb-4 rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                    href={route('users.index')}
                >
                    {t('back_to_users')}
                </Link>
                <div className="mt-4">
                    <h1 className="text-2xl font-bold">{user.name}</h1>
                    <p className="text-gray-600">{user.email}</p>
                    <p className="text-gray-600">{user.role}</p>
                </div>
            </div>
        </AppLayout>
    );
}
