import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import LanguageDetector from 'i18next-browser-languagedetector';

import commonEn from './locales/common/en.json';
import commonEs from './locales/common/es.json';
import authEn from './locales/auth/en.json';
import authEs from './locales/auth/es.json';
import dashboardEn from './locales/dashboard/en.json';
import dashboardEs from './locales/dashboard/es.json';
import candidatesEn from './locales/candidates/en.json';
import candidatesEs from './locales/candidates/es.json';

i18n.use(LanguageDetector)
    .use(initReactI18next)
    .init({
        resources: {
            en: {
                common: commonEn,
                auth: authEn,
                dashboard: dashboardEn,
                candidates: candidatesEn,
            },
            es: {
                common: commonEs,
                auth: authEs,
                dashboard: dashboardEs,
                candidates: candidatesEs,
            },
        },
        fallbackLng: 'en',
        ns: ['common', 'auth', 'dashboard', 'candidates'],
        defaultNS: 'common',
        detection: {
            order: ['localStorage', 'cookie', 'navigator'],
            caches: ['localStorage', 'cookie'],
            lookupCookie: 'app_locale',
            lookupLocalStorage: 'i18nextLng',
        },
        interpolation: {
            escapeValue: false,
        },
    });

export default i18n;
