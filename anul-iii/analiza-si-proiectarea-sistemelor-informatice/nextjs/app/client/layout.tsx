import { PortalLayout } from '@/components/portal-layout'
import { getAuthUser } from '@/lib/auth'
import { redirect } from 'next/navigation'
import { t, type Locale } from '@/lib/translations'
import { cookies } from 'next/headers'

export default async function ClientLayout({ children }: { children: React.ReactNode }) {
  const cookieStore = await cookies()
  const locale = (cookieStore.get('eloquence_locale')?.value as Locale) || 'ro'

  const auth = await getAuthUser()
  if (!auth) {
    redirect('/login')
  }
  const isAllowed = auth.role?.name === 'admin' || auth.role?.name === 'client'
  if (!isAllowed) {
    redirect('/login')
  }

  const user = {
    name: auth.name,
    role: auth.role?.name ?? null,
    isAdmin: auth.role?.name === 'admin',
    isOperator: auth.role?.name === 'payment_operator',
    isClient: auth.role?.name === 'client' || auth.role?.name === 'admin',
  }

  return (
    <PortalLayout locale={locale} user={user} title={t(locale, 'portal.dashboard')} activeSection="client">
      {children}
    </PortalLayout>
  )
}
