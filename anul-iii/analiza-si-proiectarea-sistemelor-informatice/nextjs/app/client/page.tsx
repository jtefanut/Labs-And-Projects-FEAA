import { getAuthUser } from '@/lib/auth'
import { dbStore } from '@/lib/data'
import { formatMoney } from '@/lib/utils'

export default async function ClientDashboardPage() {
  const auth = await getAuthUser()
  if (!auth) return null

  const client = dbStore.getClientByUserId(auth.id)
  if (!client) return null

  const servicesCount = dbStore.getServicesByClient(client.id).filter((s) => s.status === 'active').length
  const openTicketsCount = dbStore.getTicketsByClient(client.id).filter((t) => t.status !== 'closed' && t.status !== 'resolved').length
  const unpaidTotal = dbStore.getInvoicesByClient(client.id)
    .filter((i) => i.status === 'unpaid' || i.status === 'overdue')
    .reduce((sum, i) => sum + i.total, 0)
  const orders = dbStore.getOrdersByClient(client.id).slice(0, 5)
  const products = dbStore.getProducts({ isActive: true })
  const notifications = dbStore.getNotificationsByUser(auth.id, 5)

  return (
    <div className="space-y-8">
      <div className="grid md:grid-cols-3 gap-6">
        <div className="p-6 rounded-xl border border-slate-200 bg-white">
          <p className="text-sm text-slate-500 uppercase tracking-wider font-semibold">Servicii active</p>
          <p className="text-3xl font-bold text-slate-900 mt-2">{servicesCount}</p>
        </div>
        <div className="p-6 rounded-xl border border-slate-200 bg-white">
          <p className="text-sm text-slate-500 uppercase tracking-wider font-semibold">Tichete deschise</p>
          <p className="text-3xl font-bold text-slate-900 mt-2">{openTicketsCount}</p>
        </div>
        <div className="p-6 rounded-xl border border-slate-200 bg-white">
          <p className="text-sm text-slate-500 uppercase tracking-wider font-semibold">Sold neîncasat</p>
          <p className="text-3xl font-bold text-slate-900 mt-2">{formatMoney(unpaidTotal)}</p>
        </div>
      </div>
      <div className="grid md:grid-cols-2 gap-8">
        <div>
          <h2 className="font-serif text-2xl font-bold text-slate-900 mb-6">Comenzi recente</h2>
          <div className="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <table className="w-full text-sm">
              <thead>
                <tr className="bg-slate-50">
                  <th className="px-4 py-3 text-left text-slate-500 font-medium">Referință</th>
                  <th className="px-4 py-3 text-left text-slate-500 font-medium">Total</th>
                  <th className="px-4 py-3 text-left text-slate-500 font-medium">Dată</th>
                  <th className="px-4 py-3 text-left text-slate-500 font-medium">Stare</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {orders.map((o) => (
                  <tr key={o.id} className="hover:bg-slate-50">
                    <td className="px-4 py-3 text-slate-900 font-medium">{o.orderNumber}</td>
                    <td className="px-4 py-3 text-slate-600">{formatMoney(o.total)}</td>
                    <td className="px-4 py-3 text-slate-600">{new Date(o.createdAt).toLocaleDateString('ro-RO')}</td>
                    <td className="px-4 py-3">
                      <span className={`px-2 py-1 rounded text-xs font-medium ${o.status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'}`}>
                        {o.status}
                      </span>
                    </td>
                  </tr>
                ))}
                {orders.length === 0 && (
                  <tr><td colSpan={4} className="px-4 py-6 text-center text-slate-500">Nu există comenzi.</td></tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
        <div>
          <h2 className="font-serif text-2xl font-bold text-slate-900 mb-6">Produse disponibile</h2>
          <div className="grid md:grid-cols-2 gap-4">
            {products.map((p) => (
              <div key={p.id} className="p-4 rounded-xl border border-slate-200 bg-white">
                <h3 className="font-semibold text-slate-900 mb-1">{p.name}</h3>
                <p className="text-sm text-slate-600 mb-3">{p.summary}</p>
                <div className="text-sm font-medium text-slate-900">{formatMoney(p.price)} / {p.billingPeriod}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
      <div>
        <h2 className="font-serif text-2xl font-bold text-slate-900 mb-6">Notificări recente</h2>
        <div className="rounded-xl border border-slate-200 bg-white">
          <div className="divide-y divide-slate-100">
            {notifications.map((n) => (
              <div key={n.id} className="px-6 py-4">
                <p className="text-sm text-slate-900 font-medium">{n.title}</p>
                <p className="text-sm text-slate-600 mt-1">{n.body}</p>
              </div>
            ))}
            {notifications.length === 0 && (
              <div className="px-6 py-6 text-center text-slate-500 text-sm">Nu există notificări noi.</div>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
