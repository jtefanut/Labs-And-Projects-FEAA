import { cookies } from 'next/headers'
import { PublicLayout } from '@/components/layout'
import { dbStore } from '@/lib/data'
import { t, type Locale } from '@/lib/translations'

export default async function CatalogPage() {
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  const products = dbStore.getProducts({ category: 'hosting', isActive: true })

  return (
    <PublicLayout locale={locale} title="Hosting">
      <section className="py-20 md:py-32 bg-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="mb-12">
            <h1 className="font-serif text-3xl md:text-4xl font-bold text-slate-900 mb-2">Hosting</h1>
            <p className="text-slate-600">{t(locale, 'catalog.intro')}</p>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {products.map((product) => (
              <article key={product.id} className="p-6 rounded-xl border border-slate-200 bg-white hover:shadow-xl transition-all">
                {product.isFeatured && (
                  <span className="inline-block px-2 py-1 text-xs font-bold text-white bg-slate-900 rounded mb-4">Recomandat</span>
                )}
                <h3 className="font-semibold text-slate-900 mb-2">{product.name}</h3>
                <p className="text-sm text-slate-600 mb-4 line-clamp-2">{product.summary}</p>
                <div className="mb-4">
                  <span className="text-2xl font-bold text-slate-900">{new Intl.NumberFormat('ro-RO', { style: 'currency', currency: 'RON' }).format(product.price)}</span>
                  <span className="text-sm text-slate-500 ml-1">/lunar</span>
                </div>
                <ul className="space-y-2 mb-6 text-sm text-slate-600">
                  {(product.features || []).map((f) => (
                    <li key={f} className="flex items-center gap-2"><span className="text-slate-400">+</span> {f}</li>
                  ))}
                </ul>
                <a href="/register" className="block text-center px-6 py-3 border border-slate-200 text-sm font-medium text-slate-900 rounded-md hover:bg-slate-50 transition-colors">
                  Comandă acum <span>↗</span>
                </a>
              </article>
            ))}
          </div>
        </div>
      </section>
    </PublicLayout>
  )
}
