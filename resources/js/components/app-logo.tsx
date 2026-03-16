import AppLogoIcon from './app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-10 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
                <AppLogoIcon className="size-9 fill-current text-white dark:text-black" />
            </div>
            <div className="ml-2 grid flex-2 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold">
                    Selección & Contratación
                </span>
            </div>
        </>
    );
}
