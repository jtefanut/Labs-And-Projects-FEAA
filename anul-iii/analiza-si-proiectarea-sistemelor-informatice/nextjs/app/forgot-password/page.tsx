import { cookies } from 'next/headers'
import { PublicLayout } from '@/components/layout'
import { t, type Locale } from '@/lib/translations'

export default async function ForgotPasswordPage() {
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  return (
    <PublicLayout locale={locale} title={t(locale, 'auth.forgot_password')}>
      <section className="py-20 md:py-32 bg-white">
        <div className="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-8">
            <span className="font-serif text-xl font-bold tracking-tight">ELOQUENCE<span className="text-slate-500"> HOSTING</span></span>
          </div>
          <h1 className="text-2xl font-bold text-slate-900 mb-6">{t(locale, 'auth.forgot_password')}</h1>
          <p className="text-slate-600 mb-6">Introduceți adresa de e-mail asociată contului dvs.</p>
          <form className="space-y-4" method="POST">
            <input type="hidden" name="_action" value="forgot-password" />
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">{t(locale, 'auth.email')}</label>
              <input name="email" type="email" required className="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-900 focus:outline-none" />
            </div>
            <button type="submit" className="w-full px-6 py-3 bg-slate-900 text-white font-medium rounded-md hover:bg-slate-800 transition-colors">Trimite link de resetare</button>
          </form>
          <p className="mt-6 text-center text-sm text-slate-600">Ai deja cont? <a href="/login" className="font-medium text-slate-900 underline">Conectează-te</a></p>
        </div>
      </section>
    </PublicLayout>
  )
}
