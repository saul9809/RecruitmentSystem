import { cn } from "@/lib/utils"; // Utilidad shadcn/ui para clases

interface AppLogoIconProps {
    className?: string;
    width?: number;
    height?: number;
}

export default function AppLogoIcon({
    className,
    width = 56,
    height = 58
}: AppLogoIconProps) {
    return (
        <img
            src="/logo_2.png"
            alt="App Logo"
            width={width}
            height={height}
            className={cn(
                "object-contain", // Mantiene proporciones
                className
            )}
        />
    );
}

