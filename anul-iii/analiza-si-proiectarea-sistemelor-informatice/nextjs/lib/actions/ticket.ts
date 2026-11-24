'use server'

import { db } from '@/lib/data'
import { getAuthUser } from '@/lib/auth'
import { redirect } from 'next/navigation'
import { z } from 'zod'

const createTicketSchema = z.object({
  subject: z.string().min(3, 'Subiectul este prea scurt.'),
  message: z.string().min(10, 'Mesajul este prea scurt.'),
  priority: z.enum(['low', 'normal', 'high']),
  category: z.enum(['general', 'billing', 'technical', 'domain']),
  serviceId: z.string().optional(),
})

const replySchema = z.object({
  message: z.string().min(3, 'Mesajul este prea scurt.'),
})

export async function createTicketAction(prevState: any, formData: FormData) {
  const auth = await getAuthUser()
  if (!auth) {
    redirect('/login')
  }

  const subject = formData.get('subject') as string
  const message = formData.get('message') as string
  const priority = formData.get('priority') as string
  const category = formData.get('category') as string
  const serviceId = formData.get('service_id') as string | undefined

  const parsed = createTicketSchema.safeParse({ subject, message, priority, category, serviceId })
  if (!parsed.success) {
    return { error: parsed.error.errors[0]?.message || 'Date invalide.' }
  }

  const client = db.getClientByUserId(auth.id)
  if (!client) {
    return { error: 'Profilul de client nu a fost găsit.' }
  }

  const ticketNumber = `TKT-${Math.random().toString(36).slice(2, 10).toUpperCase()}`

  const ticket = db.createTicket({
    ticketNumber,
    clientId: client.id,
    serviceId: serviceId ? parseInt(serviceId) : null,
    subject,
    priority: priority as any,
    category,
    status: 'open',
  })

  db.createTicketMessage({
    ticketId: ticket.id,
    userId: auth.id,
    body: message,
  })

  db.createAuditLog({
    userId: auth.id,
    action: 'Support ticket opened',
    entityType: 'ticket',
    entityId: ticket.id,
    metadata: null,
    ipAddress: null,
  })

  redirect('/client/tichete')
}

export async function replyToTicketAction(prevState: any, formData: FormData) {
  const auth = await getAuthUser()
  if (!auth) {
    redirect('/login')
  }

  const ticketId = parseInt(formData.get('ticketId') as string)
  const message = formData.get('message') as string

  const parsed = replySchema.safeParse({ message })
  if (!parsed.success) {
    return { error: 'Mesaj invalid.' }
  }

  const ticket = db.getTicketById(ticketId)
  if (!ticket) {
    return { error: 'Tichetul nu a fost găsit.' }
  }

  const client = db.getClientByUserId(auth.id)
  if (!client || ticket.clientId !== client.id) {
    return { error: 'Nu ai acces la acest tichet.' }
  }

  if (!['open', 'in_progress', 'waiting_client'].includes(ticket.status)) {
    return { error: 'Acest tichet este închis.' }
  }

  db.createTicketMessage({
    ticketId: ticket.id,
    userId: auth.id,
    body: message,
  })

  db.updateTicket(ticketId, { status: 'open' })

  db.createAuditLog({
    userId: auth.id,
    action: 'Client replied to support ticket',
    entityType: 'ticket',
    entityId: ticket.id,
    metadata: null,
    ipAddress: null,
  })

  redirect('/client/tichete')
}
