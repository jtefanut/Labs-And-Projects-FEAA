import { cookies } from 'next/headers'
import { PublicLayout } from '@/components/layout'
import { t, type Locale } from '@/lib/translations'

export default async function AboutPage() {
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  return (
    <PublicLayout locale={locale} title="Despre noi">
      <section className="py-20 md:py-32 bg-white">
        <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
          <h1 className="font-serif text-3xl md:text-4xl font-bold text-slate-900 mb-8">Despre ELOQUENCE HOSTING</h1>
          <p className="text-lg text-slate-600 leading-relaxed mb-6">
            ELOQUENCE HOSTING este o platformă de infrastructură digitală construită pentru a sprijina proiectele tale online — de la site-uri personale și micro-afaceri până la aplicații cloud avansate.
          </p>
          <p className="text-lg text-slate-600 leading-relaxed mb-6">
            Ne concentram pe trei piloni: performanță, securitate și suport. Toate serviciile sunt activate în mediul demonstrativ cu uptime garantat și echipă de suport disponibil 24/7.
          </p>
          <h2 className="text-xl font-semibold text-slate-900 mb-4">Ce oferim</h2>
          <ul className="list-disc list-inside text-slate-600 space-y-3 mb-6">
            <li>Hosting web rapid și sigur</li>
            <li>Servere private virtuale (VPS)</li>
            <li>Domenii (.ro, .com și altele)</li>
            <li>Email profesional</li>
            <li>Cloud și backup</li>
            <li>Certificate SSL</li>
          </ul>
          <p className="text-lg text-slate-600 leading-relaxed">
            Această aplicație este un proiect demo dezvoltat în scop educațional. Toate datele sunt simulate.
          </p>
        </div>
      </section>
    </PublicLayout>
  )
}
