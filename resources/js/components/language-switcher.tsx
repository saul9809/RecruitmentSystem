import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export default function LanguageSwitcher() {
    const { i18n, t } = useTranslation();

    const changeLanguage = (lng: string) => {
        i18n.changeLanguage(lng);
        router.post(route('locale.update'), { locale: lng }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="outline" size="sm" aria-label={t('select_language')}>
                    {i18n.language.toUpperCase()}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                <DropdownMenuItem onClick={() => changeLanguage('en')}>
                    EN
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => changeLanguage('es')}>
                    ES
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}