import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'
import { cookies } from 'next/headers'

const PUBLIC_ROUTES = [
  '/',
  '/login',
  '/register',
  '/forgot-password',
  '/reset-password',
  '/contact',
  '/about',
  '/hosting',
  '/vps',
  '/domenii',
  '/email',
  '/cloud',
  '/wordpress',
  '/backup',
  '/ssl',
  '/termeni',
  '/confidentialitate',
  '/cookies',
  '/retragere',
  '/reclamatii',
]

const CLIENT_PREFIX = '/client'
const ADMIN_PREFIX = '/admin'
const OPERATOR_PREFIX = '/operator'
const LOGOUT_ROUTE = '/logout'

const LOCALES = ['ro', 'en']
const DEFAULT_LOCALE = 'ro'

export async function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl

  if (pathname.startsWith('/_next') || pathname.startsWith('/api') || pathname.startsWith('/favicon')) {
    return NextResponse.next()
  }

  const cookieStore = await cookies()
  const token = cookieStore.get('eloquence_session')?.value

  let locale = DEFAULT_LOCALE
  const localeCookie = cookieStore.get('eloquence_locale')?.value
  if (localeCookie && LOCALES.includes(localeCookie)) {
    locale = localeCookie
  } else {
    const acceptLanguage = request.headers.get('accept-language') ?? ''
    const match = acceptLanguage.split(',').map((l) => l.split(';')[0].trim().split('-')[0])
    const found = match.find((l) => LOCALES.includes(l))
    if (found) locale = found
  }

  const isClient = pathname.startsWith(CLIENT_PREFIX)
  const isAdmin = pathname.startsWith(ADMIN_PREFIX)
  const isOperator = pathname.startsWith(OPERATOR_PREFIX)
  const isLogout = pathname === LOGOUT_ROUTE

  if (isLogout) {
    const response = NextResponse.redirect(new URL('/', request.url))
    response.cookies.set('eloquence_session', '', { maxAge: 0, path: '/' })
    return response
  }

  if ((isClient || isAdmin || isOperator) && !token) {
    const loginUrl = new URL('/login', request.url)
    loginUrl.searchParams.set('callbackUrl', pathname)
    return NextResponse.redirect(loginUrl)
  }

  const response = NextResponse.next()
  response.cookies.set('eloquence_locale', locale, {
    path: '/',
    maxAge: 60 * 60 * 24 * 365,
    sameSite: 'lax',
  })
  return response
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|favicon.ico|.*\\..*$).*)'],
}
