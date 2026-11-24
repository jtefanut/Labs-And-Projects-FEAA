import { cookies } from 'next/headers'
import { PublicLayout } from '@/components/layout'
import { loginAction } from '@/lib/actions/auth'
import { t, type Locale } from '@/lib/translations'

export default async function LoginPage() {
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  return (
    <PublicLayout locale={locale} title={t(locale, 'auth.login')}>
      <section className="py-20 md:py-32 bg-white">
        <div className="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-8">
            <div className="flex items-center gap-2 justify-center">
              <span className="w-2 h-2 rounded-full bg-slate-900"></span>
              <span className="w-2 h-2 rounded-full bg-slate-900"></span>
              <span className="w-2 h-2 rounded-full bg-slate-900"></span>
            </div>
            <span className="font-serif text-xl font-bold tracking-tight">ELOQUENCE<span className="text-slate-500"> HOSTING</span></span>
          </div>
          <h1 className="text-2xl font-bold text-slate-900 mb-6">{t(locale, 'auth.login')}</h1>
          <form action={async (fd) => { await loginAction(undefined, fd) }} className="space-y-4" method="POST">
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">{t(locale, 'auth.email')}</label>
              <input name="email" type="email" required className="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-900 focus:outline-none" />
            </div>
            <div className="flex items-center justify-between mb-1">
              <label className="block text-sm font-medium text-slate-700">{t(locale, 'auth.password')}</label>
              <a href="/forgot-password" className="text-xs text-slate-500 hover:text-slate-900 underline">{t(locale, 'auth.forgot_password')}</a>
            </div>
            <div>
              <input name="password" type="password" required className="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-900 focus:outline-none" />
            </div>
            <div className="flex items-center gap-2">
              <input name="remember" type="checkbox" className="rounded border-slate-300" />
              <label className="text-sm text-slate-600">{t(locale, 'auth.remember')}</label>
            </div>
            <button type="submit" className="w-full px-6 py-3 bg-slate-900 text-white font-medium rounded-md hover:bg-slate-800 transition-colors">{t(locale, 'auth.sign_in')}</button>
          </form>
          <p className="mt-6 text-center text-sm text-slate-600">
            {t(locale, 'auth.no_account')} <a href="/register" className="font-medium text-slate-900 underline">{t(locale, 'auth.create_account')}</a>
          </p>
        </div>
      </section>
    </PublicLayout>
  )
}
