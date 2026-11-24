import { cookies } from 'next/headers'
import { PublicLayout } from '@/components/layout'
import { dbStore } from '@/lib/data'
import { t, type Locale } from '@/lib/translations'

const CATEGORY_TITLES: Record<string, { ro: string; en: string }> = {
  hosting: { ro: 'Hosting', en: 'Hosting' },
  wordpress: { ro: 'WordPress', en: 'WordPress' },
  vps: { ro: 'VPS', en: 'VPS' },
  cloud: { ro: 'Cloud', en: 'Cloud' },
  domain: { ro: 'Domenii', en: 'Domains' },
  email: { ro: 'Email', en: 'Email' },
  backup: { ro: 'Backup', en: 'Backup' },
  ssl: { ro: 'SSL', en: 'SSL' },
  apps: { ro: 'Aplicații', en: 'Apps' },
  dedicated: { ro: 'Dedicat', en: 'Dedicated' },
  gpu: { ro: 'GPU', en: 'GPU' },
  managed: { ro: 'Management', en: 'Managed' },
  storage: { ro: 'Stocare', en: 'Storage' },
  database: { ro: 'Baze de date', en: 'Databases' },
  security: { ro: 'Securitate', en: 'Security' },
  devops: { ro: 'DevOps', en: 'DevOps' },
}

export async function generateStaticParams() {
  return ['hosting', 'wordpress', 'vps', 'cloud', 'domain', 'email', 'backup', 'ssl', 'apps', 'dedicated', 'gpu', 'managed', 'storage', 'database', 'security', 'devops'].map((category) => ({ category }))
}

export default async function CatalogCategoryPage({ params }: { params: Promise<{ category: string }> }) {
  const { category } = await params
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  const products = dbStore.getProducts({ category, isActive: true })
  const title = CATEGORY_TITLES[category]?.[locale] || category

  if (!dbStore.getProducts().some((p) => p.category === category)) {
    return (
      <PublicLayout locale={locale} title="Nu a fost găsit">
        <section className="py-20 md:py-32 bg-white">
          <div className="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="font-serif text-3xl font-bold text-slate-900 mb-4">Categoria nu există</h1>
            <a href="/" className="text-slate-900 underline">Înapoi la pagina principală</a>
          </div>
        </section>
      </PublicLayout>
    )
  }

  return (
    <PublicLayout locale={locale} title={title}>
      <section className="py-20 md:py-32 bg-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="mb-12">
            <h1 className="font-serif text-3xl md:text-4xl font-bold text-slate-900 mb-2">{title}</h1>
            <p className="text-slate-600">{t(locale, 'catalog.intro')}</p>
          </div>
          {products.length === 0 ? (
            <div className="text-center py-20 text-slate-600">{t(locale, 'catalog.empty')}</div>
          ) : (
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
                    <span className="text-sm text-slate-500 ml-1">/{product.billingPeriod}</span>
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
          )}
        </div>
      </section>
    </PublicLayout>
  )
}
