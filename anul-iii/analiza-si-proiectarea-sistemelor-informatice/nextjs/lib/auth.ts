'use server'

import {
  createSessionToken,
  destroySession,
  getSessionToken,
  setSessionCookie,
  clearSessionCookie,
  sessions,
} from './auth-cookies'
import { dbStore, type User, type Role } from './data'

const DEMO_PASSWORDS: Record<string, string> = {
  'admin@eloquence.local': 'admin123',
  'client@eloquence.local': 'client123',
  'operator@eloquence.local': 'operator123',
}

export async function getCurrentUser(): Promise<User | null> {
  const token = await getSessionToken()
  if (!token) return null

  const session = sessions.get(token)
  if (!session || session.expires < new Date()) {
    sessions.delete(token)
    return null
  }

  return dbStore.getUserById(session.userId) ?? null
}

export async function getCurrentUserRole(): Promise<Role | null> {
  const user = await getCurrentUser()
  if (!user || !user.roleId) return null
  return dbStore.getRoleByName(user.roleId as any) ?? null
}

export async function verifyPassword(email: string, password: string): Promise<boolean> {
  const expected = DEMO_PASSWORDS[email]
  return expected === password
}

export type AuthUser = User & { role: Role | null }

export async function getAuthUser(): Promise<AuthUser | null> {
  const user = await getCurrentUser()
  if (!user) return null
  const role = user.roleId ? dbStore.getRoleByName(user.roleId as any) ?? null : null
  return { ...user, role }
}
