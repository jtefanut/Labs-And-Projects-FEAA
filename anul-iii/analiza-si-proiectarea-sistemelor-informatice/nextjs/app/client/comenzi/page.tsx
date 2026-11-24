import { getAuthUser } from '@/lib/auth'
import { dbStore } from '@/lib/data'
import { formatMoney } from '@/lib/utils'
import { ClientOrderForm } from '@/components/client-order-form'

export default async function ClientOrdersPage() {
  const auth = await getAuthUser()
  if (!auth) return null

  const client = dbStore.getClientByUserId(auth.id)
  if (!client) return null

  const orders = dbStore.getOrdersByClient(client.id)
  const products = dbStore.getProducts({ isActive: true })

  return (
    <div className="space-y-8">
      <div className="flex flex-col md:flex-row items-end justify-between gap-4">
        <h2 className="font-serif text-2xl font-bold text-slate-900">Comenzile mele</h2>
        <ClientOrderForm products={products} />
      </div>
      <div className="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-slate-50">
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Referință</th>
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Total</th>
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Dată</th>
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Stare</th>
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Produs</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {orders.map((o) => {
              const items = dbStore.getOrderItemsByOrder(o.id)
              const productNames = items.map((i) => {
                const p = dbStore.getProductById(i.productId)
                return p?.name || 'Produs'
              }).join(', ')
              return (
                <tr key={o.id} className="hover:bg-slate-50">
                  <td className="px-6 py-4 text-slate-900 font-medium">{o.orderNumber}</td>
                  <td className="px-6 py-4 text-slate-600">{formatMoney(o.total)}</td>
                  <td className="px-6 py-4 text-slate-600">{new Date(o.createdAt).toLocaleDateString('ro-RO')}</td>
                  <td className="px-6 py-4">
                    <span className={`px-2.5 py-0.5 rounded text-xs font-medium ${
                      o.status === 'pending' ? 'bg-amber-100 text-amber-700' :
                      o.status === 'processing' ? 'bg-blue-100 text-blue-700' :
                      o.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700'
                    }`}>{o.status}</span>
                  </td>
                  <td className="px-6 py-4 text-slate-600">{productNames}</td>
                </tr>
              )
            })}
            {orders.length === 0 && (
              <tr><td colSpan={5} className="px-6 py-8 text-center text-slate-500">Nu există comenzi.</td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  )
}
