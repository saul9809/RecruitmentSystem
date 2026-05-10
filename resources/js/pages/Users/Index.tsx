import { Head, Link, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { route } from 'ziggy-js';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

// -- Interface for User data
interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'user';
}

export default function Index({ users }: { users: User[] }) {
    function handleDelete(userId: number) {
        if (confirm('Are you sure you want to delete this user?')) {
            // Implement delete logic here, e.g., send a DELETE request to the server
            console.log(`User with ID ${userId} deleted.`);
            router.delete(route('users.destroy', userId));
        }
    }
    console.log('Users Data', users);
    const { t } = useTranslation('common');
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('user_breadcrumb'),
            href: '/users',
        },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('user_headline')} />

            <div className="p-3">
                <Link
                    href={route('users.create')}
                    className="mb-4 rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                >
                    {t('create_user')}
                </Link>
                <div className="mt-4 overflow-x-auto">
                    <table className="w-full text-left text-sm text-gray-700">
                        <thead className="bg-gray-50 text-xs text-gray-700 uppercase">
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    {t('user_id')}
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    {t('user_name')}
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    {t('user_email')}
                                </th>
                                <th scope="col" className="w-70 px-6 py-3">
                                    {t('roles')}
                                </th>
                                <th scope="col" className="w-70 px-6 py-3">
                                    {t('actions')}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {users.map((user) => (
                                <tr
                                    key={user.id}
                                    className="border-b border-gray-200 odd:bg-white even:bg-gray-50"
                                >
                                    <td className="px-6 py-2 font-medium text-gray-900">
                                        {user.id}
                                    </td>
                                    <td className="px-6 py-2 text-gray-700">
                                        {user.name}
                                    </td>
                                    <td className="px-6 py-2 text-gray-700">
                                        {user.email}
                                    </td>
                                    <td className="px-6 py-2 text-gray-700">
                                        {user.role === 'admin' ? (
                                            <span className="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                                                {t('admin')}
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                                {t('user')}
                                            </span>
                                        )}
                                    </td>
                                    <td className="space-x-1 px-6 py-2">
                                        <Link
                                            href={route('users.edit', user.id)}
                                            className="cursor-pointer rounded-lg bg-blue-700 px-3 py-2 text-xs font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                                        >
                                            {t('edit')}
                                        </Link>
                                        <Link
                                            href={route('users.show', user.id)}
                                            className="cursor-pointer rounded-lg bg-green-700 px-3 py-2 text-xs font-medium text-white hover:bg-green-800 focus:ring-4 focus:ring-green-300 focus:outline-none"
                                        >
                                            {t('show')}
                                        </Link>
                                        <Button
                                            onClick={() =>
                                                handleDelete(user.id)
                                            }
                                            className="cursor-pointer rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:ring-4 focus:ring-red-300 focus:outline-none"
                                        >
                                            {t('delete')}
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
