import { Link } from '@inertiajs/react';
import {
    FileUser,
    LayoutGrid,
    FileSymlink,
    /*CircleUserRound,
    Notebook,*/
} from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
import AppLogo from './app-logo';

//Futuros fitchure una ves que se integre el sistema con los docuemntos de la nuve
const footerNavItems: NavItem[] = [
    /* {
         title: 'Documentacion',
         href: 'https://laravel.com/docs/starter-kits#react',
         icon: BookOpen,
     },*/
];

export function AppSidebar() {
    const { t } = useTranslation('common');

    const mainNavItems: NavItem[] = [
        {
            title: t('dashboard'),
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: t('candidates'),
            href: '/candidates',
            icon: FileUser,
        },
        {
            title: t('cv-process'),
            href: '/cv-process',
            icon: FileSymlink,
        },
        /*{
            title: t('user'),
            href: '/users',
            icon: CircleUserRound,
        },
        {
            title: t('role'),
            href: '/roles',
            icon: Notebook,
        },*/
    ];
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
