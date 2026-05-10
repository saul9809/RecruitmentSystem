import { Head } from '@inertiajs/react';
import ChatWindow from '@/components/chat-window';
import { DataTable } from '@/components/data-table';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

interface Candidates {
    id: number;
    candidate_name: string;
    candidate_phone: string;
    candidate_address: string;
    candidate_email: string;
    status: boolean;
    last_position: string;
}

export default function index({ candidates }: { candidates: Candidates[] }) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Gestión de Candidatos',
            href: '/candidates',
        },
    ];
    console.log('Candidates Data', candidates);
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Candidatos" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <DataTable data={candidates} />
                <ChatWindow />
            </div>
        </AppLayout>
    );
}
