'use server'

import { db } from '@/lib/data'
import { z } from 'zod'

const schema = z.object({
  name: z.string().min(2),
  email: z.string().email(),
  company: z.string().optional(),
  message: z.string().min(10),
})

export async function sendInquiry(prevState: any, formData: FormData) {
  const name = formData.get('name') as string
  const email = formData.get('email') as string
  const company = formData.get('company') as string | undefined
  const message = formData.get('message') as string

  const parsed = schema.safeParse({ name, email, company, message })
  if (!parsed.success) {
    return { error: 'Date invalide. Te rugăm să completeze corect formularul.' }
  }

  db.createHostingInquiry({
    name,
    email,
    company: company || null,
    message,
  })

  return {
    success: 'Mesajul a ajuns la echipa noastră. Revenim către tine în curând.',
  }
}
