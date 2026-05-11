import { Head, Link, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { route } from 'ziggy-js';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

interface Permission {
    id: number;
    name: string;
}

export default function Create({
    permissions = [],
}: {
    permissions: Permission[];
}) {
    const { data, setData, post, errors, processing } = useForm<{
        name: string;
        permissions: number[];
    }>({
        name: '',
        permissions: [],
    });

    const { t } = useTranslation('common');

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('role_create_breadcrumb'),
            href: route('roles.index'),
        },
    ];

    function togglePermission(id: number) {
        if (data.permissions.includes(id)) {
            setData(
                'permissions',
                data.permissions.filter((p) => p !== id),
            );
        } else {
            setData('permissions', [...data.permissions, id]);
        }
    }

    function submit(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        post(route('roles.store'));
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('role_create_headline')} />

            <div className="p-3">
                <Link
                    href={route('roles.index')}
                    className="mb-4 rounded-lg bg-blue-700 px-4 py-2 text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                >
                    {t('back_to_roles')}
                </Link>
            </div>

            <form onSubmit={submit} className="mx-auto mt-4 max-w-md space-y-6">
                {/* NAME */}
                <div className="grid gap-2">
                    <label htmlFor="name">{t('roles_name')}</label>

                    <input
                        id="name"
                        name="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />

                    {errors.name && (
                        <p className="text-sm text-red-500">{errors.name}</p>
                    )}
                </div>

                {/* PERMISSIONS */}
                <div className="grid gap-2">
                    <label>{t('permissions')}</label>

                    <div className="text-sm leading-none font-medium select-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                        {Array.isArray(permissions) &&
                        permissions.length > 0 ? (
                            permissions.map((permission) => (
                                <label
                                    key={permission.id}
                                    className="flex items-center gap-2"
                                >
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        checked={data.permissions.includes(
                                            permission.id,
                                        )}
                                        onChange={() =>
                                            togglePermission(permission.id)
                                        }
                                        className="form-checkbox h-5 w-5 rounded text-blue-600 focus:ring-2 focus:ring-blue-500"
                                    />

                                    {permission.name}
                                </label>
                            ))
                        ) : (
                            <p className="text-sm text-gray-500">
                                No permissions available
                            </p>
                        )}
                    </div>
                </div>

                {/* SUBMIT */}
                <button
                    type="submit"
                    disabled={processing || permissions.length === 0}
                    className="rounded-md bg-green-600 px-4 py-2 font-medium text-white transition hover:bg-green-700"
                >
                    {t('submit')}
                </button>
            </form>
        </AppLayout>
    );
}
