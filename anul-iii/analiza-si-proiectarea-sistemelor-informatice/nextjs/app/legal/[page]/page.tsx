import { cookies } from 'next/headers'
import { PublicLayout } from '@/components/layout'
import { t, type Locale } from '@/lib/translations'

const LEGAL_PAGES: Record<string, { ro: string; en: string }> = {
  terms: { ro: 'Termeni și condiții', en: 'Terms and conditions' },
  privacy: { ro: 'Confidențialitate', en: 'Privacy' },
  cookies: { ro: 'Cookies', en: 'Cookies' },
  withdrawal: { ro: 'Retragere', en: 'Withdrawal' },
  complaints: { ro: 'Reclamații', en: 'Complaints' },
}

const LEGAL_CONTENT: Record<string, Record<string, string>> = {
  terms: { ro: 'Acești termeni reglementează utilizarea serviciilor ELOQUENCE HOSTING.', en: 'These terms govern the use of ELOQUENCE HOSTING services.' },
  privacy: { ro: 'Politica de confidențialitate descrie cum colectăm și folosim datele.', en: 'Privacy policy describes how we collect and use data.' },
  cookies: { ro: 'Folosește cookie-uri pentru a îmbunătăți experiența.', en: 'We use cookies to improve your experience.' },
  withdrawal: { ro: 'Dreptul de retragere din contract în 14 zile.', en: 'Right of withdrawal within 14 days.' },
  complaints: { ro: 'Procedura de reclamare pentru clienți.', en: 'Complaint procedure for clients.' },
}

export default async function LegalPage({ params }: { params: Promise<{ page: string }> }) {
  const { page } = await params
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  const title = LEGAL_PAGES[page]?.[locale] || page
  const content = LEGAL_CONTENT[page]?.[locale] || 'Conținut neavailability.'

  if (!LEGAL_PAGES[page]) {
    return (
      <PublicLayout locale={locale} title="Nu a fost găsit">
        <section className="py-20 md:py-32 bg-white">
          <div className="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="font-serif text-3xl font-bold text-slate-900 mb-4">Pagina nu există</h1>
            <a href="/" className="text-slate-900 underline">Înapoi acasă</a>
          </div>
        </section>
      </PublicLayout>
    )
  }

  return (
    <PublicLayout locale={locale} title={title}>
      <section className="py-20 md:py-32 bg-white">
        <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
          <h1 className="font-serif text-3xl font-bold text-slate-900 mb-8">{title}</h1>
          <p className="text-slate-600 leading-relaxed">{content}</p>
        </div>
      </section>
    </PublicLayout>
  )
}
