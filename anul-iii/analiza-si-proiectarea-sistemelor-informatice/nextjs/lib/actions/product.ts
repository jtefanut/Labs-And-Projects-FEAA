// 'use server' removed for static export

import { db } from '@/lib/data'
import { getAuthUser } from '@/lib/auth'
import { redirect } from 'next/navigation'
import { z } from 'zod'

const productSchema = z.object({
  name: z.string().min(1),
  slug: z.string().min(1),
  category: z.string(),
  summary: z.string().min(1),
  price: z.string().refine((v) => !isNaN(Number(v)) && Number(v) >= 0, 'Preț invalid.'),
  billingPeriod: z.enum(['monthly', 'yearly']),
  features: z.string(),
  isActive: z.optional(z.boolean()),
  isFeatured: z.optional(z.boolean()),
  sortOrder: z.string().optional(),
  cpuCores: z.string().optional(),
  ramMb: z.string().optional(),
  storageGb: z.string().optional(),
})

export async function createProductAction(prevState: any, formData: FormData) {
  const auth = await getAuthUser()
  if (!auth || auth.role?.name !== 'admin') {
    redirect('/login')
  }

  const data = Object.fromEntries(formData.entries()) as Record<string, unknown>
  data.isActive = data.isActive === 'on'
  data.isFeatured = data.isFeatured === 'on'

  const parsed = productSchema.safeParse(data)
  if (!parsed.success) {
    return { error: parsed.error.errors[0]?.message || 'Date invalide.' }
  }

  if (db.getProducts().some((p) => p.slug === parsed.data.slug)) {
    return { error: 'Există deja un produs cu acest slug.' }
  }

  const features = parsed.data.features
    .split('\n')
    .map((l) => l.trim())
    .filter(Boolean)

  db.createProduct({
    name: parsed.data.name,
    slug: parsed.data.slug,
    category: parsed.data.category,
    summary: parsed.data.summary,
    cpuCores: parsed.data.cpuCores ? parseInt(parsed.data.cpuCores) : null,
    ramMb: parsed.data.ramMb ? parseInt(parsed.data.ramMb) : null,
    storageGb: parsed.data.storageGb ? parseInt(parsed.data.storageGb) : null,
    price: parseFloat(parsed.data.price),
    billingPeriod: parsed.data.billingPeriod,
    features,
    isActive: parsed.data.isActive !== undefined ? parsed.data.isActive : true,
    isFeatured: parsed.data.isFeatured !== undefined ? parsed.data.isFeatured : false,
    sortOrder: parsed.data.sortOrder ? parseInt(parsed.data.sortOrder) : 0,
  })

  db.createAuditLog({
    userId: auth.id,
    action: 'Product created',
    entityType: 'product',
    entityId: null,
    metadata: null,
    ipAddress: null,
  })

  redirect('/admin/produse')
}

export async function updateProductAction(prevState: any, formData: FormData) {
  const auth = await getAuthUser()
  if (!auth || auth.role?.name !== 'admin') {
    redirect('/login')
  }

  const id = parseInt(formData.get('id') as string)
  const data = Object.fromEntries(formData.entries()) as Record<string, unknown>
  data.isActive = data.isActive === 'on'

  const parsed = productSchema.safeParse(data)
  if (!parsed.success) {
    return { error: parsed.error.errors[0]?.message || 'Date invalide.' }
  }

  const product = db.getProductById(id)
  if (!product) {
    return { error: 'Produsul nu a fost găsit.' }
  }

  const features = parsed.data.features
    .split('\n')
    .map((l) => l.trim())
    .filter(Boolean)

  db.updateProduct(id, {
    name: parsed.data.name,
    slug: parsed.data.slug,
    category: parsed.data.category,
    summary: parsed.data.summary,
    price: parseFloat(parsed.data.price),
    billingPeriod: parsed.data.billingPeriod,
    features,
    isActive: parsed.data.isActive !== undefined ? parsed.data.isActive : true,
    sortOrder: parsed.data.sortOrder ? parseInt(parsed.data.sortOrder) : 0,
  })

  db.createAuditLog({
    userId: auth.id,
    action: 'Product updated',
    entityType: 'product',
    entityId: id,
    metadata: null,
    ipAddress: null,
  })

  redirect('/admin/produse')
}

export async function updateStatusAction(prevState: any, formData: FormData) {
  const auth = await getAuthUser()
  if (!auth || auth.role?.name !== 'admin') {
    redirect('/login')
  }

  const section = formData.get('section') as string
  const id = parseInt(formData.get('id') as string)
  const status = formData.get('status') as string

  const config: Record<string, { model: string; allowed: string[] }> = {
    produse: { model: 'product', allowed: ['activ', 'inactiv'] },
    clienti: { model: 'client', allowed: ['activ', 'inactiv'] },
    comenzi: { model: 'order', allowed: ['processing', 'cancelled', 'completed'] },
    servicii: { model: 'service', allowed: ['active', 'suspended', 'expired'] },
    facturi: { model: 'invoice', allowed: ['overdue', 'cancelled'] },
    tichete: { model: 'ticket', allowed: ['open', 'in_progress', 'waiting_client', 'resolved', 'closed'] },
  }

  const sectionConfig = config[section]
  if (!sectionConfig) {
    return { error: 'Secțiune invalidă.' }
  }

  if (!sectionConfig.allowed.includes(status)) {
    return { error: 'Starea nu este permisă.' }
  }

  switch (sectionConfig.model) {
    case 'product': {
      const product = db.getProductById(id)
      if (!product) return { error: 'Produsul nu a fost găsit.' }
      db.updateProduct(id, { isActive: status === 'activ' })
      break
    }
    case 'client': {
      const client = db.getClientById(id)
      if (!client) return { error: 'Clientul nu a fost găsit.' }
      const user = db.getUserById(client.userId)
      if (user) db.updateUser(user.id, { isActive: status === 'activ' })
      break
    }
    case 'order': {
      const order = db.getOrderById(id)
      if (!order) return { error: 'Comanda nu a fost găsit.' }
      db.updateOrder(id, { status: status as any })
      break
    }
    case 'service': {
      const service = db.getServiceById(id)
      if (!service) return { error: 'Serviciul nu a fost găsit.' }
      db.updateService(id, { status: status as any })
      break
    }
    case 'invoice': {
      const invoice = db.getInvoiceById(id)
      if (!invoice) return { error: 'Factura nu a fost găsit.' }
      db.updateInvoice(id, { status: status as any })
      break
    }
    case 'ticket': {
      const ticket = db.getTicketById(id)
      if (!ticket) return { error: 'Tichetul nu a fost găsit.' }
      db.updateTicket(id, { status: status as any })
      break
    }
  }

  db.createAuditLog({
    userId: auth.id,
    action: 'Status changed',
    entityType: section,
    entityId: id,
    metadata: { to: status },
    ipAddress: null,
  })

  return { success: 'Starea a fost actualizată.' }
}
