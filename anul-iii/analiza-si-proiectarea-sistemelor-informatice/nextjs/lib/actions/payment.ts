// 'use server' removed for static export

import { db } from '@/lib/data'
import { getAuthUser } from '@/lib/auth'
import { redirect } from 'next/navigation'
import { z } from 'zod'

const paymentSchema = z.object({
  invoiceId: z.string().refine((v) => !isNaN(Number(v)), 'Factură invalidă.'),
})

export async function simulatePaymentAction(prevState: any, formData: FormData) {
  const auth = await getAuthUser()
  if (!auth) {
    redirect('/login')
  }

  const invoiceId = parseInt(formData.get('invoiceId') as string)

  const parsed = paymentSchema.safeParse({ invoiceId: String(invoiceId) })
  if (!parsed.success) {
    return { error: 'Date invalide.' }
  }

  const invoice = db.getInvoiceById(invoiceId)
  if (!invoice || invoice.clientId !== auth.id) {
    return { error: 'Factura nu a fost găsită sau nu îți aparține.' }
  }

  if (!['unpaid', 'overdue'].includes(invoice.status)) {
    return { error: 'Factura nu poate fi plătită în starea curentă.' }
  }

  const payment = db.createPayment({
    invoiceId: invoice.id,
    reference: `SIM-${Math.random().toString(36).slice(2, 14).toUpperCase()}`,
    amount: invoice.total,
    method: 'simulation',
    status: 'completed',
    paidAt: new Date(),
  })

  db.updateInvoice(invoice.id, { status: 'paid', paidAt: new Date() })

  db.createAuditLog({
    userId: auth.id,
    action: 'Simulated invoice payment',
    entityType: 'invoice',
    entityId: invoice.id,
    metadata: { paymentId: payment.id, amount: payment.amount },
    ipAddress: null,
  })

  redirect('/client/facturi')
}
