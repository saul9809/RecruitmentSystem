import { Head, Link, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { route } from 'ziggy-js';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

// -- Interface for Role data
interface Role {
    id: number;
    name: string;
    permissions: string[];
}

export default function Index({ roles }: { roles: Role[] }) {
    function handleDelete(roleId: number) {
        if (confirm('Are you sure you want to delete this role?')) {
            // Implement delete logic here, e.g., send a DELETE request to the server
            console.log(`Role with ID ${roleId} deleted.`);
            router.delete(route('roles.destroy', roleId));
        }
    }
    console.log('Roles Data', roles);
    const { t } = useTranslation('common');
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('roles_breadcrumb'),
            href: '/roles',
        },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('user_headline')} />

            <div className="p-3">
                <Link
                    href={route('roles.create')}
                    className="mb-4 rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                >
                    {t('create_role')}
                </Link>
                <div className="mt-4 overflow-x-auto">
                    <table className="w-full text-left text-sm text-gray-700">
                        <thead className="bg-gray-50 text-xs text-gray-700 uppercase">
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    {t('role_id')}
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    {t('role_name')}
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    {t('role_permissions')}
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
                            {roles.map((role) => (
                                <tr
                                    key={role.id}
                                    className="border-b border-gray-200 odd:bg-white even:bg-gray-50"
                                >
                                    <td className="px-6 py-2 font-medium text-gray-900">
                                        {role.id}
                                    </td>
                                    <td className="px-6 py-2 text-gray-700">
                                        {role.name}
                                    </td>
                                    <td className="px-6 py-2 text-gray-700">
                                        {role.permissions.join(', ')}
                                    </td>
                                    <td className="px-6 py-2 text-gray-700">
                                        {role.name === 'admin' ? (
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
                                            href={route('roles.edit', role.id)}
                                            className="cursor-pointer rounded-lg bg-blue-700 px-3 py-2 text-xs font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                                        >
                                            {t('edit')}
                                        </Link>
                                        <Link
                                            href={route('roles.show', role.id)}
                                            className="cursor-pointer rounded-lg bg-green-700 px-3 py-2 text-xs font-medium text-white hover:bg-green-800 focus:ring-4 focus:ring-green-300 focus:outline-none"
                                        >
                                            {t('show')}
                                        </Link>
                                        <Button
                                            onClick={() =>
                                                handleDelete(role.id)
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
