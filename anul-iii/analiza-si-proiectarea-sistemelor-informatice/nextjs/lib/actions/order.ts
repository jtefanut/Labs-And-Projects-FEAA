// 'use server' removed for static export

import { db } from '@/lib/data'
import { getAuthUser } from '@/lib/auth'
import { z } from 'zod'
import { redirect } from 'next/navigation'

const orderSchema = z.object({
  productId: z.string().refine((v) => !isNaN(Number(v)), 'Produs invalid.'),
  termsAccepted: z.literal('on', {
    errorMap: () => ({ message: 'Trebuie să accepți termenii și condițiile.' }),
  }),
})

export async function createOrderAction(prevState: any, formData: FormData) {
  const auth = await getAuthUser()
  if (!auth) {
    redirect('/login')
  }

  const productId = parseInt(formData.get('productId') as string)
  const termsAccepted = formData.get('terms_accepted')

  const parsed = orderSchema.safeParse({ productId: String(productId), termsAccepted })
  if (!parsed.success) {
    return { error: parsed.error.errors[0]?.message || 'Date invalide.' }
  }

  const product = db.getProductById(productId)
  if (!product || !product.isActive) {
    return { error: 'Produsul nu este disponibil.' }
  }

  const client = db.getClientByUserId(auth.id)
  if (!client) {
    return { error: 'Profilul de client nu a fost găsit.' }
  }

  const orderNumber = `EH-${new Date().toISOString().slice(2, 10).replace(/-/g, '')}-${Math.random().toString(36).slice(2, 7).toUpperCase()}`

  const order = db.createOrder({
    orderNumber,
    clientId: client.id,
    status: 'pending',
    subtotal: product.price,
    tax: 0,
    total: product.price,
    currency: 'RON',
  })

  db.createOrderItem({
    orderId: order.id,
    productId: product.id,
    quantity: 1,
    unitPrice: product.price,
    lineTotal: product.price,
  })

  db.createAuditLog({
    userId: auth.id,
    action: 'Order placed',
    entityType: 'order',
    entityId: order.id,
    metadata: { termsAccepted: true, termsVersion: '2026-10-30' },
    ipAddress: null,
  })

  redirect('/client/comenzi')
}
