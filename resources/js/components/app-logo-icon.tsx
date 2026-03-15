import { cn } from "@/lib/utils"; // Utilidad shadcn/ui para clases

interface AppLogoIconProps {
    className?: string;
    width?: number;
    height?: number;
}

export default function AppLogoIcon({
    className,
    width = 40,
    height = 42
}: AppLogoIconProps) {
    return (
        <img
            src="/logo.png"
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

