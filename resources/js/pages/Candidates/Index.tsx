import { Head } from '@inertiajs/react';
import ChatWindow from '@/components/chat-window';
import { DataTable } from '@/components/data-table';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gestión de Candidatos',
        href: '/candidates',
    },
];


export default function indexCandidates() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Candidatos" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <DataTable data={[]} />
                <ChatWindow />
            </div>
        </AppLayout>
    );
}