/* eslint-disable @typescript-eslint/no-explicit-any */
import { Head } from '@inertiajs/react';
import { DataTableCV } from '@/components/data_table_cv';
import FileUpload06 from '@/components/file-upload-06';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Procesamiento de Currículos',
        href: '/cv-process',
    },
];
export default function index({ cv_process, result, }: { cv_process?: any[]; result?: any; }) {
    console.log("CV Process Data", cv_process);
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Procesamiento de Currículos" />

            <div className='flex'>
                <div className='felx gap-0.5 w-2/3  min-h-screen p-4 rounded-lg'>
                    <DataTableCV data={cv_process || []} />
                </div>
                <div className="w-105 bg-[#f1f3f5] min-h-screen p-4 ml-auto rounded-lg">
                    <FileUpload06 />
                    {result && (
                        <Card className="p-4 bg-black text-green-400 text-xs overflow-auto">
                            <pre>
                                {JSON.stringify(result, null, 2)}
                            </pre>
                        </Card>
                    )}
                </div>
            </div>
        </AppLayout >
    );
}
