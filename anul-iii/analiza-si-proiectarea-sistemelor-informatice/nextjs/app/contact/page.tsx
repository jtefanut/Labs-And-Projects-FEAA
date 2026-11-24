import { cookies } from 'next/headers'
import { PublicLayout } from '@/components/layout'
import { t, type Locale } from '@/lib/translations'
import { sendInquiry } from '@/lib/actions/inquiry'

export default async function ContactPage() {
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  return (
    <PublicLayout locale={locale} title="Contact">
      <section className="py-20 md:py-32 bg-white">
        <div className="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
          <h1 className="font-serif text-3xl md:text-4xl font-bold text-slate-900 mb-8">Contactează-ne</h1>
          <form action={async (fd) => { await sendInquiry(undefined, fd) }} className="space-y-6" method="POST">
            <div className="grid md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1">Nume</label>
                <input name="name" type="text" required className="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-900 focus:outline-none" />
              </div>
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1">E-mail</label>
                <input name="email" type="email" required className="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-900 focus:outline-none" />
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Companie (opțional)</label>
              <input name="company" type="text" className="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-900 focus:outline-none" />
            </div>
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Mesaj</label>
              <textarea name="message" rows={5} required className="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-900 focus:outline-none" />
            </div>
            <button type="submit" className="px-8 py-3 bg-slate-900 text-white font-medium rounded-md hover:bg-slate-800 transition-colors">Trimite mesaj</button>
          </form>
        </div>
      </section>
    </PublicLayout>
  )
}
