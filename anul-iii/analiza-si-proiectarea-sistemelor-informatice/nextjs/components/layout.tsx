import Link from 'next/link'
import { LanguageToggle } from './language-toggle'
import { ThemeToggle } from './theme-toggle'
import { t, type Locale } from '@/lib/translations'

export interface PublicLayoutProps {
  children: React.ReactNode
  locale: Locale
  title?: string
}

export function PublicLayout({ children, locale, title }: PublicLayoutProps) {
  return (
    <html lang={locale} data-theme="light">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#eaf0e9" />
        <meta
          name="description"
          content="ELOQUENCE HOSTING — web hosting and infrastructure for digital projects."
        />
        <title>
          {title ? `${title} — ELOQUENCE HOSTING` : `${t(locale, 'home.title')} — ELOQUENCE HOSTING`}
        </title>
        <link
          rel="preconnect"
          href="https://fonts.googleapis.com"
          crossOrigin="anonymous"
        />
        <link
          rel="preconnect"
          href="https://fonts.gstatic.com"
          crossOrigin="anonymous"
        />
        <link
          href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Source+Sans+3:wght@400;500;600;700&display=swap"
          rel="stylesheet"
        />
      </head>
      <body className="public-site bg-white text-slate-900 antialiased">
        <header className="site-header border-b border-slate-200 bg-white sticky top-0 z-40">
          <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div className="flex flex-wrap items-center justify-between gap-3 py-4">
              <Link
                href="/"
                className="wordmark flex items-center gap-2"
                aria-label="ELOQUENCE HOSTING"
              >
                <span className="brand-symbol flex items-center gap-1" aria-hidden="true">
                  <span className="w-2 h-2 rounded-full bg-slate-900"></span>
                  <span className="w-2 h-2 rounded-full bg-slate-900"></span>
                  <span className="w-2 h-2 rounded-full bg-slate-900"></span>
                </span>
                <span className="font-serif text-xl font-bold tracking-tight">
                  ELOQUENCE<span className="text-slate-500"> HOSTING</span>
                </span>
              </Link>

              <nav className="site-nav hidden md:flex gap-6 text-sm font-medium text-slate-600" aria-label={t(locale, 'nav.menu')}>
                <Link href="/hosting" className="hover:text-slate-900 transition-colors">
                  {t(locale, 'nav.hosting')}
                </Link>
                <Link href="/vps" className="hover:text-slate-900 transition-colors">
                  {t(locale, 'nav.vps')}
                </Link>
                <Link href="/domenii" className="hover:text-slate-900 transition-colors">
                  {t(locale, 'nav.domains')}
                </Link>
                <Link href="/email" className="hover:text-slate-900 transition-colors">
                  {t(locale, 'nav.email')}
                </Link>
                <Link href="/about" className="hover:text-slate-900 transition-colors">
                  {t(locale, 'nav.about')}
                </Link>
                <Link href="/contact" className="hover:text-slate-900 transition-colors">
                  {t(locale, 'nav.contact')}
                </Link>
              </nav>

              <div className="flex items-center gap-3">
                <LanguageToggle currentLocale={locale} />
                <ThemeToggle />
              </div>
            </div>
          </div>
        </header>

        <main>{children}</main>

        <footer className="site-footer border-t border-slate-200 bg-slate-50 mt-auto py-8">
          <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <Link href="/" className="wordmark flex items-center gap-2">
              <span className="brand-symbol flex items-center gap-1" aria-hidden="true">
                <span className="w-2 h-2 rounded-full bg-slate-900"></span>
                <span className="w-2 h-2 rounded-full bg-slate-900"></span>
                <span className="w-2 h-2 rounded-full bg-slate-900"></span>
              </span>
              <span className="font-serif text-xl font-bold tracking-tight">
                ELOQUENCE<span className="text-slate-500"> HOSTING</span>
              </span>
            </Link>

            <nav className="legal-links flex gap-4 text-sm text-slate-500" aria-label="Legal links">
              <Link href="/termeni">{t(locale, 'legal.pages.terms.title')}</Link>
              <Link href="/confidentialitate">{t(locale, 'legal.pages.privacy.title')}</Link>
              <Link href="/cookies">{t(locale, 'legal.pages.cookies.title')}</Link>
              <Link href="/retragere">{t(locale, 'legal.pages.withdrawal.title')}</Link>
              <Link href="/reclamatii">{t(locale, 'legal.pages.complaints.title')}</Link>
            </nav>

            <p className="text-sm text-slate-500">
              {t(locale, 'home.footer_tagline')}<br />
              <span>
                © {new Date().getFullYear()} ELOQUENCE HOSTING ·
                {locale === 'ro' ? 'PROIECT DEMO' : 'DEMO PROJECT'}
              </span>
            </p>
          </div>
        </footer>
      </body>
    </html>
  )
}
