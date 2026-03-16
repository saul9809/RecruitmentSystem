import { router } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { route } from 'ziggy-js';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export default function LanguageSwitcher() {
    const [open, setOpen] = useState(false);
    const { i18n, t } = useTranslation();

    const changeLanguage = (lng: string) => {
        i18n.changeLanguage(lng);
        router.post(route('locale.update'), { locale: lng }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <DropdownMenu open={open} onOpenChange={setOpen}>
            <DropdownMenuTrigger asChild>
                <Button variant="outline" size="sm" aria-label={t('select_language')}>
                    {i18n.language.toUpperCase()}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                <DropdownMenuItem onSelect={async () => {
                    await changeLanguage('en')
                    setOpen(false)
                }}>
                    EN
                </DropdownMenuItem>
                <DropdownMenuItem onSelect={async () => {
                    await changeLanguage('es')
                    setOpen(false)
                }}>
                    ES
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}