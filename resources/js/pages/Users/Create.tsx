import { Head, Link, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { route } from 'ziggy-js';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

export default function Create() {
    const { data, setData, post, errors } = useForm({
        name: '',
        email: '',
        password: '',
        role: 'admin',
    });
    function submit(e: React.SubmitEvent<HTMLFormElement>) {
        e.preventDefault();
        post(route('users.store'));
    }
    const { t } = useTranslation('common');
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('user_create_breadcrumb'),
            href: route('users.index'),
        },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('user_create_headline')} />

            <div className="p-3">
                <Link
                    className="mb-4 rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                    href={route('users.index')}
                >
                    {t('back_to_users')}
                </Link>
            </div>
            <form onSubmit={submit} className="mx-auto mt-4 max-w-md space-y-6">
                <div className="grid gap-2">
                    <label
                        form="name"
                        className="text-sm leading-none font-medium select-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50"
                    >
                        Name:
                    </label>
                    <input
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        name="name"
                        className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Enter your name"
                    />
                    {errors.name && (
                        <p className="mt-1 text-sm text-red-500">
                            {' '}
                            {errors.name}
                        </p>
                    )}
                </div>
                <div className="grid gap-2">
                    <label
                        form="name"
                        className="text-sm leading-none font-medium select-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50"
                    >
                        Password:
                    </label>
                    <input
                        id="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        name="password"
                        type="password"
                        className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Enter your password"
                    />
                    {errors.password && (
                        <p className="mt-1 text-sm text-red-500">
                            {' '}
                            {errors.password}
                        </p>
                    )}
                </div>
                <div className="grid gap-2">
                    <label
                        form="email"
                        className="text-sm leading-none font-medium select-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50"
                    >
                        Email:
                    </label>
                    <input
                        id="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        name="email"
                        className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Enter your email"
                    />
                    {errors.email && (
                        <p className="mt-1 text-sm text-red-500">
                            {' '}
                            {errors.email}
                        </p>
                    )}
                </div>
                <div className="grid gap-2">
                    <label
                        form="name"
                        className="text-sm leading-none font-medium select-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50"
                    >
                        Role:
                    </label>
                    <input
                        id="role"
                        value={data.role}
                        onChange={(e) => setData('role', e.target.value)}
                        name="role"
                        className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Enter your role"
                    />
                    {errors.role && (
                        <p className="mt-1 text-sm text-red-500">
                            {' '}
                            {errors.role}
                        </p>
                    )}
                </div>

                <button
                    type="submit"
                    className="rounded-md bg-green-600 px-4 py-2 font-medium text-white transition hover:bg-green-700"
                >
                    Submit
                </button>
            </form>
        </AppLayout>
    );
}
