'use server'

import { redirect } from 'next/navigation'
import { cookies } from 'next/headers'
import { dbStore, type User, type Role } from '@/lib/data'
import {
  createSessionToken,
  destroySession,
  setSessionCookie,
  clearSessionCookie,
} from '@/lib/auth-cookies'
import {
  getCurrentUser,
  getCurrentUserRole,
  verifyPassword,
} from '@/lib/auth'
import { z } from 'zod'

const loginSchema = z.object({
  email: z.string().email(),
  password: z.string().min(1),
  remember: z.boolean().optional(),
})

const registerSchema = z.object({
  name: z.string().min(2, 'Numele este prea scurt.'),
  email: z.string().email(),
  company: z.string().optional(),
  password: z.string().min(10, 'Parola trebuie să aiba cel puțin 10 caractere.'),
  confirmPassword: z.string(),
}).refine((data) => data.password === data.confirmPassword, {
  message: 'Parolele nu se potrivesc.',
  path: ['confirmPassword'],
})

export async function loginAction(prevState: any, formData: FormData) {
  const email = formData.get('email') as string
  const password = formData.get('password') as string
  const remember = formData.get('remember') === 'on'

  const parsed = loginSchema.safeParse({ email, password, remember })
  if (!parsed.success) {
    return { error: 'Datele de autentificare nu sunt corecte.' }
  }

  const user = dbStore.getUserByEmail(email)
  if (!user || !user.isActive) {
    return { error: 'Datele de autentificare nu sunt corecte.' }
  }

  const valid = await verifyPassword(email, password)
  if (!valid) {
    return { error: 'Datele de autentificare nu sunt corecte.' }
  }

  const token = await createSessionToken(user.id)
  await setSessionCookie(token)

  const role = user.roleId ? dbStore.getRoleByName(user.roleId as any)?.name : null

  const callbackUrl = formData.get('callbackUrl') as string | null
  if (callbackUrl && callbackUrl.startsWith('/')) {
    redirect(callbackUrl)
  }

  redirect(role === 'admin' ? '/admin/dashboard' : role === 'payment_operator' ? '/operator/payments' : '/client/dashboard')
}

export async function registerAction(prevState: any, formData: FormData) {
  const name = formData.get('name') as string
  const email = formData.get('email') as string
  const company = formData.get('company') as string | undefined
  const password = formData.get('password') as string
  const confirmPassword = formData.get('confirmPassword') as string

  const parsed = registerSchema.safeParse({ name, email, company, password, confirmPassword })
  if (!parsed.success) {
    return { error: parsed.error.errors[0]?.message || 'Date invalide.' }
  }

  if (dbStore.getUserByEmail(email)) {
    return { error: 'Există deja un cont cu acest e-mail.' }
  }

  const clientRole = dbStore.getRoleByName('client')
  if (!clientRole) {
    return { error: 'Eroare internă: rolul client lipsește.' }
  }

  const user = dbStore.createUser({
    name,
    email,
    password,
    roleId: clientRole.id,
    isActive: true,
    emailVerifiedAt: null,
    rememberToken: null,
  })

  dbStore.createClient({
    userId: user.id,
    company: company || null,
    phone: null,
    address: null,
    city: null,
    country: 'RO',
    taxId: null,
  })

  dbStore.createAuditLog({
    userId: user.id,
    action: 'Client account created',
    entityType: 'client',
    entityId: user.id,
    metadata: null,
    ipAddress: null,
  })

  const token = await createSessionToken(user.id)
  await setSessionCookie(token)

  redirect('/client/dashboard')
}

export async function logoutAction() {
  await destroySession()
  await clearSessionCookie()
  redirect('/')
}

export async function getAuthUserAction() {
  const user = await getCurrentUser()
  if (!user) return null
  const role = user.roleId ? dbStore.getRoleByName(user.roleId as any) : null
  return { user, role }
}
