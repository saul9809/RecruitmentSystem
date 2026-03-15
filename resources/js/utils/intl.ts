import { useTranslation } from 'react-i18next';

export const useIntl = () => {
    const { i18n } = useTranslation();

    const formatDate = (
        date: Date | string,
        options?: Intl.DateTimeFormatOptions,
    ) => {
        return new Intl.DateTimeFormat(i18n.language, options).format(
            new Date(date),
        );
    };

    const formatNumber = (
        number: number,
        options?: Intl.NumberFormatOptions,
    ) => {
        return new Intl.NumberFormat(i18n.language, options).format(number);
    };

    const formatCurrency = (amount: number, currency: string = 'USD') => {
        return new Intl.NumberFormat(i18n.language, {
            style: 'currency',
            currency,
        }).format(amount);
    };

    return { formatDate, formatNumber, formatCurrency };
};
