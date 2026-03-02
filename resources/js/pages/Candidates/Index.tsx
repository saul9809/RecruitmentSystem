import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gestión de Candidatos',
        href: dashboard().url,
    },
];

export default function indexCandidates() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Candidatos" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                Sitio Gestion de candidatos
            </div>
        </AppLayout>
    );
}