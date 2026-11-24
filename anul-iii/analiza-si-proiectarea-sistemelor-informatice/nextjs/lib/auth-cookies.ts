import { cookies } from 'next/headers'

const SESSION_COOKIE = 'eloquence_session'
const SESSION_TTL_MS = 30 * 24 * 60 * 60 * 1000 // 30 days

const sessions = new Map<string, { userId: number; expires: Date }>()

export async function createSessionToken(userId: number): Promise<string> {
  const token = crypto.randomUUID()
  sessions.set(token, { userId, expires: new Date(Date.now() + SESSION_TTL_MS) })
  return token
}

export async function getSessionToken(): Promise<string | null> {
  const cookieStore = await cookies()
  return cookieStore.get(SESSION_COOKIE)?.value ?? null
}

export async function destroySession(): Promise<void> {
  const token = await getSessionToken()
  if (token) {
    sessions.delete(token)
  }
}

export async function setSessionCookie(token: string): Promise<void> {
  const cookieStore = await cookies()
  cookieStore.set(SESSION_COOKIE, token, {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    path: '/',
    maxAge: SESSION_TTL_MS / 1000,
  })
}

export async function clearSessionCookie(): Promise<void> {
  const cookieStore = await cookies()
  cookieStore.set(SESSION_COOKIE, '', {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    path: '/',
    maxAge: 0,
  })
}

export { sessions }
