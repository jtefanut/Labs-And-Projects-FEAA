'use client'

import Link from 'next/link'
import { useRouter, usePathname } from 'next/navigation'
import { useEffect, useState } from 'react'
import { LogOut, Languages, Sun, Moon, Menu, X, Shield, Users, CreditCard, FileText, Headset, UserCircle, LayoutDashboard } from 'lucide-react'
import { t, type Locale } from '@/lib/translations'
import { LanguageToggle } from './language-toggle'
import { ThemeToggle } from './theme-toggle'

interface PortalLayoutProps {
  children: React.ReactNode
  locale: Locale
  user: { name: string; role: string | null; isAdmin: boolean; isOperator: boolean; isClient: boolean }
  title: string
  activeSection?: string
}

export function PortalLayout({ children, locale, user, title, activeSection }: PortalLayoutProps) {
  const router = useRouter()
  const pathname = usePathname()
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const [mounted, setMounted] = useState(false)

  useEffect(() => {
    setMounted(true)
  }, [])

  if (!mounted) return null

  const isAdmin = user.isAdmin
  const isOperator = user.isOperator
  const isClient = user.isClient

  const menuItems = isOperator
    ? [{ label: t(locale, 'operator.title'), href: '/operator/payments', icon: <CreditCard size={16} /> }]
    : isAdmin
      ? [
          { label: t(locale, 'portal.dashboard'), href: '/admin/dashboard', icon: <LayoutDashboard size={16} /> },
          { label: t(locale, 'portal.clients'), href: '/admin/clienti', icon: <Users size={16} /> },
          { label: t(locale, 'portal.users'), href: '/admin/utilizatori', icon: <Shield size={16} /> },
          { label: t(locale, 'portal.products'), href: '/admin/produse', icon: <Shield size={16} /> },
          { label: t(locale, 'portal.orders'), href: '/admin/comenzi', icon: <CreditCard size={16} /> },
          { label: t(locale, 'portal.services'), href: '/admin/servicii', icon: <Shield size={16} /> },
          { label: t(locale, 'portal.invoices'), href: '/admin/facturi', icon: <FileText size={16} /> },
          { label: t(locale, 'portal.tickets'), href: '/admin/tichete', icon: <Headset size={16} /> },
          { label: t(locale, 'portal.servers'), href: '/admin/servere', icon: <Shield size={16} /> },
          { label: t(locale, 'portal.suppliers'), href: '/admin/furnizori', icon: <Users size={16} /> },
          { label: t(locale, 'portal.reports'), href: '/admin/rapoarte', icon: <FileText size={16} /> },
          { label: t(locale, 'portal.audit'), href: '/admin/audit', icon: <Shield size={16} /> },
        ]
      : [
          { label: t(locale, 'portal.dashboard'), href: '/client/dashboard', icon: <LayoutDashboard size={16} /> },
          { label: t(locale, 'portal.my_services'), href: '/client/servicii', icon: <Shield size={16} /> },
          { label: t(locale, 'portal.my_orders'), href: '/client/comenzi', icon: <CreditCard size={16} /> },
          { label: t(locale, 'portal.invoices'), href: '/client/facturi', icon: <FileText size={16} /> },
          { label: t(locale, 'portal.support_tickets'), href: '/client/tichete', icon: <Headset size={16} /> },
          { label: t(locale, 'portal.profile'), href: '/client/profil', icon: <UserCircle size={16} /> },
        ]

  const activeItem = menuItems.find((i) => pathname.startsWith(i.href))

  const handleLogout = (e: React.FormEvent) => {
    e.preventDefault()
    fetch('/api/auth/logout', { method: 'POST' }).then(() => {
      router.push('/login')
    })
  }

  return (
    <html lang={locale} data-theme="light">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#173b32" />
        <title>{title} · ELOQUENCE HOSTING</title>
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
      <body className="portal-body bg-white text-slate-900 antialiased flex h-screen">
        <aside className="portal-sidebar hidden md:flex flex-col w-64 border-r border-slate-200 bg-white">
          <div className="flex items-center gap-2 p-6">
            <span className="portal-mark inline-flex h-8 w-8 items-center justify-center rounded bg-slate-900 text-white font-serif font-bold text-sm">
              EH
            </span>
            <div>
              <span className="portal-mark block font-serif font-bold text-lg tracking-tight">
                ELOQUENCE
              </span>
              <span className="text-xs text-slate-500 uppercase tracking-wider">
                {isAdmin ? t(locale, 'portal.admin') : isOperator ? t(locale, 'operator.title') : t(locale, 'portal.client_area')}
              </span>
            </div>
          </div>

          <nav className="portal-nav flex-1 px-4 space-y-1" aria-label={isAdmin ? t(locale, 'portal.admin') : isOperator ? t(locale, 'operator.title') : t(locale, 'portal.client_area')}>
            {menuItems.map((item) => {
              const active = activeItem === item || (item.href !== '/' && pathname.startsWith(item.href))
              return (
                <Link
                  key={item.label}
                  href={item.href}
                  className={`flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors ${
                    active
                      ? 'bg-slate-100 text-slate-900'
                      : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                  }`}
                >
                  {item.icon}
                  <span>{item.label}</span>
                </Link>
              )
            })}
          </nav>

          <div className="p-4 border-t border-slate-200">
            <Link
              href="/"
              className="flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900"
            >
              <span className="text-xs">↗</span> {t(locale, 'portal.public_site')}
            </Link>
            <form onSubmit={handleLogout} className="mt-3">
              <button
                type="submit"
                className="flex w-full items-center gap-2 px-3 py-2 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md"
              >
                <LogOut size={16} /> <span>{t(locale, 'portal.sign_out')} <span>↗</span></span>
              </button>
            </form>
          </div>
        </aside>

        {/* Mobile sidebar overlay */}
        <aside className="md:hidden fixed inset-0 z-50 bg-white shadow-xl transform transition-transform duration-300 {!sidebarOpen ? '-translate-x-full' : 'translate-x-0'}" aria-label="Sidebar">
          <div className="flex items-center justify-between p-4 border-b border-slate-200">
            <span className="font-serif font-bold text-lg tracking-tight">ELOQUENCE HOSTING</span>
            <button onClick={() => setSidebarOpen(false)} className="p-2 hover:bg-slate-100 rounded">
              <X size={20} />
            </button>
          </div>
          <nav className="p-4 space-y-2">
            {menuItems.map((item) => (
              <Link
                key={item.label}
                href={item.href}
                onClick={() => setSidebarOpen(false)}
                className={`flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium ${
                  activeItem === item ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                }`}
              >
                {item.icon} <span>{item.label}</span>
              </Link>
            ))}
          </nav>
          <div className="p-4 border-t border-slate-200">
            <form onSubmit={handleLogout} className="mb-4">
              <button
                type="submit"
                className="flex w-full items-center gap-2 px-3 py-2 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md"
              >
                <LogOut size={16} /> {t(locale, 'portal.sign_out')}
              </button>
            </form>
            <Link
              href="/"
              className="block text-center text-sm text-slate-600 hover:text-slate-900"
            >
              {t(locale, 'portal.public_site')} ↗
            </Link>
          </div>
        </aside>

        {/* Mobile sidebar backdrop */}
        {sidebarOpen && (
          <div className="md:hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-sm" onClick={() => setSidebarOpen(false)} />
        )}

        <div className="flex-1 flex flex-col min-w-0">
          <header className="portal-topbar flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
            <div>
              <h1 className="font-serif text-xl font-bold tracking-tight">{title}</h1>
            </div>
            <div className="flex items-center gap-3">
              <LanguageToggle currentLocale={locale} />
              <ThemeToggle />
              <div className="flex items-center gap-2 pl-4 border-l border-slate-200">
                <span className="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-medium text-slate-600">
                  {user.name.charAt(0).toUpperCase()}
                </span>
                <div className="hidden sm:block">
                  <div className="text-sm font-medium text-slate-900">{user.name}</div>
                  <div className="text-xs text-slate-500">
                    {isAdmin ? t(locale, 'portal.admin') : t(locale, 'portal.client_area')}
                  </div>
                </div>
              </div>
            </div>
          </header>

          <div className="portal-main flex-1 overflow-y-auto p-6">
            {children}
          </div>

          <footer className="portal-footer border-t border-slate-200 bg-slate-50 px-6 py-4 text-xs text-slate-500">
            <Link href="/termeni">{t(locale, 'legal.pages.terms.title')}</Link>
            <Link href="/confidentialitate">{t(locale, 'legal.pages.privacy.title')}</Link>
            <span>© {new Date().getFullYear()} ELOQUENCE HOSTING</span>
          </footer>
        </div>
      </body>
    </html>
  )
}
