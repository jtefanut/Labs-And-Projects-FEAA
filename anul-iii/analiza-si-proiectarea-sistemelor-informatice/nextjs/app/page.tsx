import { cookies } from 'next/headers'
import { PublicLayout } from '@/components/layout'
import { dbStore, type Product } from '@/lib/data'
import { t, type Locale } from '@/lib/translations'
import { ArrowUpRight, Globe, Layers, Shield, Sparkles } from 'lucide-react'

const CATEGORY_ICONS: Record<string, React.ComponentType<{ size?: number }>> = {
  hosting: Globe,
  wordpress: Layers,
  vps: ZapIcon,
  cloud: CloudIcon,
  domain: Globe,
  email: Shield,
  backup: Shield,
  ssl: Shield,
}

function ZapIcon({ size = 24 }: { size?: number }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <polyline points="13 2 3 14 12 14 23 2"></polyline>
    </svg>
  )
}

function CloudIcon({ size = 24 }: { size?: number }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <path d="M18.2 16a6 6 0 0 1-6-6 8.6 8.6 0 0 0-3.2 1.4 8.6 8.6 0 0 0-4.3-1.1 6 6 0 1 1 9.6 6 6 6 0 0 1 0.1 2" />
      <path d="M20.2 9c0 .2-.1.4-.2.6-.7 1.2-1.8 2-3.1 2-.8 0-1.5-.3-2.1-.8" />
    </svg>
  )
}

export default async function HomePage() {
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  const plans = dbStore.getProducts({ category: 'hosting', isActive: true })

  const services = [
    { title: t(locale, 'messages.home.web_title'), summary: t(locale, 'messages.home.web_text'), route: '/hosting', glyph: 'W', icon: <Globe size={24} /> },
    { title: t(locale, 'messages.home.wordpress_title'), summary: t(locale, 'messages.home.wordpress_text'), route: '/wordpress', glyph: 'WP', icon: <Layers size={24} /> },
    { title: t(locale, 'messages.home.vps_title'), summary: t(locale, 'messages.home.vps_text'), route: '/vps', glyph: 'V', icon: <ZapIcon size={24} /> },
    { title: t(locale, 'messages.home.cloud_title'), summary: t(locale, 'messages.home.cloud_text'), route: '/cloud', glyph: 'C', icon: <CloudIcon size={24} /> },
    { title: t(locale, 'messages.home.domain_title'), summary: t(locale, 'messages.home.domain_text'), route: '/domenii', glyph: '.ro', icon: <Globe size={24} /> },
    { title: t(locale, 'messages.home.email_title'), summary: t(locale, 'messages.home.email_text'), route: '/email', glyph: '@', icon: <Shield size={24} /> },
    { title: t(locale, 'messages.home.backup_title'), summary: t(locale, 'messages.home.backup_text'), route: '/backup', glyph: 'B', icon: <Shield size={24} /> },
    { title: t(locale, 'messages.home.ssl_title'), summary: t(locale, 'messages.home.ssl_text'), route: '/ssl', glyph: 'S', icon: <Shield size={24} /> },
  ]

  return (
    <PublicLayout locale={locale} title={t(locale, 'home.title')}>
      <section className="relative bg-slate-50/30 py-20 md:py-32 overflow-hidden">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center">
          <div>
            <h1 className="font-serif text-4xl md:text-5xl font-bold text-slate-900 tracking-tight leading-tight">
              {t(locale, 'home.headline_a')}<br />
              <em className="text-slate-600">{t(locale, 'home.headline_b')}</em><br />
              {t(locale, 'home.headline_c')}
            </h1>
            <p className="mt-6 text-lg text-slate-600 leading-relaxed max-w-xl">
              {t(locale, 'home.intro')}
            </p>
            <div className="mt-10 flex flex-col sm:flex-row gap-4">
              <a href="#servicii" className="inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-900 text-white font-medium rounded-md hover:bg-slate-800 transition-colors">
                {t(locale, 'home.services_cta')} <ArrowUpRight size={16} />
              </a>
              <a href="/login" className="inline-flex items-center justify-center gap-2 px-8 py-4 border border-slate-200 text-slate-900 font-medium rounded-md hover:bg-slate-50 transition-colors">
                {t(locale, 'home.portal_cta')} <ArrowUpRight size={16} />
              </a>
            </div>
          </div>
          <div className="relative h-64 md:h-80 flex items-center justify-center opacity-80">
            <div className="relative w-full h-full max-w-md">
              <div className="absolute inset-0 bg-slate-900/10 rounded-2xl blur-[40px]"></div>
              <div className="relative z-10 w-full h-full rounded-2xl border border-slate-200 bg-white/80 backdrop-blur shadow-xl p-6">
                <div className="flex gap-4 mb-4">
                  {[1, 2, 3].map((i) => (
                    <div key={i} className="w-2 h-12 rounded-full bg-slate-200 animate-pulse" style={{ animationDelay: `${i * 0.2}s` }} />
                  ))}
                </div>
                <div className="space-y-3">
                  <div className="h-4 w-3/4 rounded bg-slate-200" />
                  <div className="h-4 w-1/2 rounded bg-slate-200" />
                  <div className="h-4 w-3/4 rounded bg-slate-200" />
                </div>
                <div className="mt-6 flex items-center gap-4 p-4 rounded-lg bg-slate-50">
                  <div className="w-8 h-8 rounded bg-green-100 flex items-center justify-center text-green-700">
                    <Sparkles size={16} />
                  </div>
                  <div className="text-sm text-slate-600">Uptime 99.9%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="servicii" className="py-20 md:py-32 border-y border-slate-200 bg-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row items-end justify-between gap-6 mb-12">
            <div>
              <h2 className="font-serif text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">
                {t(locale, 'home.services_title_a')}<br />
                <em className="text-slate-600">{t(locale, 'home.services_title_b')}</em>
              </h2>
              <p className="mt-4 text-slate-600 max-w-xl">{t(locale, 'home.services_intro')}</p>
            </div>
            <div className="flex items-center gap-2">
              <p className="text-sm text-slate-500">{t(locale, 'home.contact_cta')}</p>
              <a href="/contact" className="text-sm font-medium text-slate-900 hover:underline">
                Contactează-ne <span>↗</span>
              </a>
            </div>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {services.map((service) => (
              <a key={service.route} href={service.route} className="block group p-6 rounded-xl border border-slate-200 hover:border-slate-400 hover:shadow-xl transition-all">
                <div className="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-slate-900 group-hover:text-white transition-colors mb-4">
                  {service.icon}
                </div>
                <h3 className="font-semibold text-slate-900 mb-2">{service.title}</h3>
                <p className="text-sm text-slate-600 mb-4 line-clamp-2">{service.summary}</p>
                <span className="text-xs font-medium text-slate-500 flex items-center gap-1">
                  {t(locale, 'home.view_service')} <span>↗</span>
                </span>
              </a>
            ))}
          </div>
        </div>
      </section>

      <section id="hosting" className="py-20 md:py-32 bg-slate-50">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row items-end justify-between gap-6 mb-12">
            <div>
              <h2 className="font-serif text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">
                {t(locale, 'home.pricing_title_a')}<br />
                <em className="text-slate-600">{t(locale, 'home.pricing_title_b')}</em>
              </h2>
              <p className="mt-4 text-slate-600">{t(locale, 'home.pricing_note')}</p>
            </div>
            <a href="/hosting" className="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white font-medium rounded-md hover:bg-slate-800 transition-colors">
              {t(locale, 'home.order')} <span>↗</span>
            </a>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {plans.map((plan) => (
              <article key={plan.id} className={`p-6 rounded-xl border border-slate-200 bg-white hover:shadow-xl transition-all ${plan.isFeatured ? 'ring-2 ring-slate-900 relative' : ''}`}>
                {plan.isFeatured && (
                  <span className="absolute top-4 left-4 z-10 px-2 py-1 text-xs font-bold text-white bg-slate-900 rounded">{t(locale, 'home.recommended')}</span>
                )}
                <h3 className="font-semibold text-slate-900 mb-2">{plan.name}</h3>
                <p className="text-sm text-slate-600 mb-4 line-clamp-2">{plan.summary}</p>
                <div className="mb-4">
                  <span className="text-2xl font-bold text-slate-900">{new Intl.NumberFormat('ro-RO', { style: 'currency', currency: 'RON' }).format(plan.price)}</span>
                  <span className="text-sm text-slate-500 ml-1">/{plan.billingPeriod === 'yearly' ? t(locale, 'home.annual') : t(locale, 'home.monthly')}</span>
                </div>
                <ul className="space-y-2 mb-6 text-sm text-slate-600">
                  {(plan.features || []).map((f) => (
                    <li key={f} className="flex items-center gap-2"><span className="text-slate-400">+</span> {f}</li>
                  ))}
                </ul>
                <a href="/register" className="block text-center px-6 py-3 border border-slate-200 text-sm font-medium text-slate-900 rounded-md hover:bg-slate-50 transition-colors">
                  {t(locale, 'home.order')} <span>↗</span>
                </a>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20 md:py-32 bg-white border-t border-slate-200">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 className="font-serif text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">
              {t(locale, 'home.portal_title_a')}<br />
              <em className="text-slate-600">{t(locale, 'home.portal_title_b')}</em>
            </h2>
            <p className="mt-4 text-slate-600 max-w-md">{t(locale, 'home.portal_text')}</p>
            <a href="/register" className="mt-6 inline-flex items-center gap-2 px-8 py-3 border border-slate-200 text-slate-900 font-medium rounded-md hover:bg-slate-50 transition-colors">
              {t(locale, 'home.account')} <span>↗</span>
            </a>
          </div>
          <div className="grid grid-cols-3 gap-4">
            {[
              { title: t(locale, 'portal.my_services'), text: t(locale, 'home.benefit_services') },
              { title: t(locale, 'portal.invoices'), text: t(locale, 'home.benefit_invoices') },
              { title: t(locale, 'portal.support_tickets'), text: t(locale, 'home.benefit_tickets') },
            ].map((b) => (
              <article key={b.title} className="p-4 rounded-xl border border-slate-100 bg-slate-50">
                <h3 className="font-semibold text-slate-900 mb-1">{b.title}</h3>
                <p className="text-sm text-slate-600">{b.text}</p>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20 md:py-32 bg-slate-900 text-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="font-serif text-3xl md:text-4xl font-bold mb-4">
            {t(locale, 'home.contact_title_a')}<br />
            <em className="text-slate-400">{t(locale, 'home.contact_title_b')}</em>
          </h2>
          <a href="/contact" className="inline-flex items-center gap-2 px-8 py-4 border border-white text-white font-medium rounded-md hover:bg-white/10 transition-colors">
            {t(locale, 'home.contact_cta')} <span>↗</span>
          </a>
        </div>
      </section>
    </PublicLayout>
  )
}
