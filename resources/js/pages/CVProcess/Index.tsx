/* eslint-disable @typescript-eslint/no-explicit-any */
import { Head } from '@inertiajs/react';
import { DataTableCV } from '@/components/data_table_cv';
import FileUpload06 from '@/components/file-upload-06';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

export default function index({
    cv_process,
    result,
}: {
    cv_process?: any[];
    result?: any;
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Procesamiento de Currículos',
            href: '/cv-process',
        },
    ];
    console.log('CV Process Data', cv_process);
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Procesamiento de Currículos" />

            <div className="flex">
                <div className="felx min-h-screen w-2/3 gap-0.5 rounded-lg p-4">
                    <DataTableCV data={cv_process || []} />
                </div>
                <div className="ml-auto min-h-screen w-105 rounded-lg bg-[#f1f3f5] p-4">
                    <FileUpload06 />
                    {result && (
                        <Card className="overflow-auto bg-black p-4 text-xs text-green-400">
                            <pre>{JSON.stringify(result, null, 2)}</pre>
                        </Card>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
