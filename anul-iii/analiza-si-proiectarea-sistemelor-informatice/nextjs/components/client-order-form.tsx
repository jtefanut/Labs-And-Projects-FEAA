'use client'

import { useState } from 'react'
import { createOrderAction } from '@/lib/actions/order'
import { formatMoney } from '@/lib/utils'

export function ClientOrderForm({ products }: { products: any[] }) {
  const [pending, setPending] = useState(false)

  return (
    <form action={async (fd) => { setPending(true); await createOrderAction(undefined, fd); setPending(false) }} className="flex flex-col sm:flex-row gap-4" method="POST">
      <select name="productId" className="flex-1 px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-900 focus:outline-none">
        <option value="">Selectează un produs</option>
        {products.map((p) => (
          <option key={p.id} value={p.id}>{p.name} — {formatMoney(p.price)} / {p.billingPeriod}</option>
        ))}
      </select>
      <label className="flex items-center gap-2 text-sm text-slate-600">
        <input name="terms_accepted" type="checkbox" className="rounded border-slate-300" />
        Accept termenii și condițiile
      </label>
      <button type="submit" disabled={pending} className="px-6 py-2 bg-slate-900 text-white font-medium rounded-md hover:bg-slate-800 disabled:opacity-60 transition-colors">
        {pending ? 'Se plasează...' : 'Comandă acum'}
      </button>
    </form>
  )
}
