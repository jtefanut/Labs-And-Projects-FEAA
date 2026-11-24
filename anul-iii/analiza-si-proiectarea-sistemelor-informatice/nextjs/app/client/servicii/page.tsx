import { getAuthUser } from '@/lib/auth'
import { dbStore } from '@/lib/data'

export default async function ClientServicesPage() {
  const auth = await getAuthUser()
  if (!auth) return null

  const client = dbStore.getClientByUserId(auth.id)
  if (!client) return null

  const services = dbStore.getServicesByClient(client.id)

  return (
    <div className="space-y-6">
      <h2 className="font-serif text-2xl font-bold text-slate-900">Serviciile mele</h2>
      <div className="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-slate-50">
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Serviciu</th>
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Produs</th>
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Reînnoire</th>
              <th className="px-6 py-3 text-left text-slate-500 font-medium">Stare</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {services.map((s) => {
              const product = dbStore.getProductById(s.productId)
              return (
                <tr key={s.id} className="hover:bg-slate-50">
                  <td className="px-6 py-4 text-slate-900 font-medium">{s.serviceNumber}</td>
                  <td className="px-6 py-4 text-slate-600">{product?.name || '—'}</td>
                  <td className="px-6 py-4 text-slate-600">{s.renewsAt ? new Date(s.renewsAt).toLocaleDateString('ro-RO') : '—'}</td>
                  <td className="px-6 py-4">
                    <span className={`px-2.5 py-0.5 rounded text-xs font-medium ${
                      s.status === 'active' ? 'bg-green-100 text-green-700' :
                      s.status === 'suspended' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700'
                    }`}>{s.status}</span>
                  </td>
                </tr>
              )
            })}
            {services.length === 0 && (
              <tr><td colSpan={4} className="px-6 py-8 text-center text-slate-500">Nu aveți servicii active.</td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  )
}
