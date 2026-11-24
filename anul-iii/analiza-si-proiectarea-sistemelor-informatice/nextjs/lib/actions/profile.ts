'use server'

import { db } from '@/lib/data'
import { getAuthUser } from '@/lib/auth'
import { redirect } from 'next/navigation'
import { z } from 'zod'

const profileSchema = z.object({
  name: z.string().min(2, 'Numele este prea scurt.'),
  email: z.string().email(),
  company: z.string().optional(),
  phone: z.string().optional(),
  address: z.string().optional(),
  city: z.string().optional(),
})

export async function updateProfileAction(prevState: any, formData: FormData) {
  const auth = await getAuthUser()
  if (!auth) {
    redirect('/login')
  }

  const name = formData.get('name') as string
  const email = formData.get('email') as string
  const company = formData.get('company') as string | undefined
  const phone = formData.get('phone') as string | undefined
  const address = formData.get('address') as string | undefined
  const city = formData.get('city') as string | undefined

  const parsed = profileSchema.safeParse({ name, email, company, phone, address, city })
  if (!parsed.success) {
    return { error: parsed.error.errors[0]?.message || 'Date invalide.' }
  }

  const user = db.getUserById(auth.id)
  if (!user) {
    return { error: 'Utilizatorul nu a fost găsit.' }
  }

  const existing = db.getUserByEmail(email)
  if (existing && existing.id !== user.id) {
    return { error: 'Acest e-mail este deja folosit de un alt cont.' }
  }

  db.updateUser(user.id, { name, email })

  const client = db.getClientByUserId(user.id)
  if (client) {
    db.updateClient(client.id, {
      company: company || null,
      phone: phone || null,
      address: address || null,
      city: city || null,
    })
  }

  db.createAuditLog({
    userId: user.id,
    action: 'Client profile updated',
    entityType: 'client',
    entityId: client?.id ?? null,
    metadata: null,
    ipAddress: null,
  })

  redirect('/client/profil')
}
