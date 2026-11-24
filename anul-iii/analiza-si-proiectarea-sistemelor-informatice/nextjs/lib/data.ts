import { formatMoney, formatDate, randomToken } from './utils'
import type { Locale } from './translations'

// ============================================================
// Types (identici cu schema SQLite / migrațiile Laravel)
// ============================================================

export interface User {
  id: number
  name: string
  email: string
  password: string
  roleId: number | null
  isActive: boolean
  createdAt: Date
  emailVerifiedAt: Date | null
  rememberToken: string | null
}

export interface Role {
  id: number
  name: 'admin' | 'client' | 'payment_operator'
  label: string
  createdAt: Date
  updatedAt: Date
}

export interface Client {
  id: number
  userId: number
  company: string | null
  phone: string | null
  address: string | null
  city: string | null
  country: string
  taxId: string | null
  createdAt: Date
  updatedAt: Date
}

export interface Product {
  id: number
  name: string
  slug: string
  category: string
  summary: string
  cpuCores: number | null
  ramMb: number | null
  storageGb: number | null
  price: number
  billingPeriod: 'monthly' | 'yearly'
  features: string[]
  isActive: boolean
  isFeatured: boolean
  sortOrder: number
  createdAt: Date
  updatedAt: Date
}

export interface Order {
  id: number
  orderNumber: string
  clientId: number
  status: 'pending' | 'processing' | 'completed' | 'cancelled'
  subtotal: number
  tax: number
  total: number
  currency: string
  createdAt: Date
  updatedAt: Date
}

export interface OrderItem {
  id: number
  orderId: number
  productId: number
  quantity: number
  unitPrice: number
  lineTotal: number
  createdAt: Date
  updatedAt: Date
}

export interface Service {
  id: number
  clientId: number
  productId: number
  orderId: number | null
  serviceNumber: string
  hostname: string | null
  status: 'pending' | 'active' | 'suspended' | 'expired'
  startsAt: Date | null
  renewsAt: Date | null
  expiresAt: Date | null
  createdAt: Date
  updatedAt: Date
}

export interface Invoice {
  id: number
  invoiceNumber: string
  clientId: number
  orderId: number | null
  subtotal: number
  tax: number
  total: number
  currency: string
  status: 'unpaid' | 'paid' | 'overdue' | 'cancelled'
  dueAt: Date | null
  paidAt: Date | null
  createdAt: Date
  updatedAt: Date
}

export interface Payment {
  id: number
  invoiceId: number
  reference: string
  amount: number
  method: string | null
  status: 'pending' | 'completed' | 'failed'
  paidAt: Date | null
  createdAt: Date
  updatedAt: Date
}

export interface SupportTicket {
  id: number
  ticketNumber: string
  clientId: number
  serviceId: number | null
  subject: string
  priority: 'low' | 'normal' | 'high'
  category: string
  status: 'open' | 'in_progress' | 'waiting_client' | 'resolved' | 'closed'
  createdAt: Date
  updatedAt: Date
}

export interface TicketMessage {
  id: number
  ticketId: number
  userId: number
  body: string
  createdAt: Date
  updatedAt: Date
}

export interface Server {
  id: number
  name: string
  hostname: string
  location: string | null
  provider: string | null
  status: string
  capacityPercent: number
  createdAt: Date
  updatedAt: Date
}

export interface Supplier {
  id: number
  name: string
  serviceType: string
  contactEmail: string | null
  monthlyCost: number
  status: string
  createdAt: Date
  updatedAt: Date
}

export interface AuditLog {
  id: number
  userId: number | null
  action: string
  entityType: string | null
  entityId: number | null
  metadata: Record<string, any> | null
  ipAddress: string | null
  createdAt: Date
  updatedAt: Date
}

export interface HostingInquiry {
  id: number
  name: string
  email: string
  company: string | null
  message: string
  createdAt: Date
  updatedAt: Date
}

export interface Notification {
  id: number
  userId: number
  title: string
  body: string
  url: string | null
  read: boolean
  createdAt: Date
}

// ============================================================
// DataStore (in-memory, schema identică cu migrațiile Laravel)
// Pentru conexiunea SQLite reală, vezi lib/db.ts
// ============================================================

class DataStore {
  users: User[] = []
  roles: Role[] = []
  clients: Client[] = []
  products: Product[] = []
  orders: Order[] = []
  orderItems: OrderItem[] = []
  services: Service[] = []
  invoices: Invoice[] = []
  invoiceItems: any[] = []
  payments: Payment[] = []
  supportTickets: SupportTicket[] = []
  ticketMessages: TicketMessage[] = []
  servers: Server[] = []
  suppliers: Supplier[] = []
  auditLogs: AuditLog[] = []
  hostingInquiries: HostingInquiry[] = []
  notifications: Notification[] = []

  idCounters: Record<string, number> = {}

  private nextId(entity: string): number {
    this.idCounters[entity] = (this.idCounters[entity] || 0) + 1
    return this.idCounters[entity]
  }

  // ---- Roles ----
  createRole(role: Omit<Role, 'id' | 'createdAt' | 'updatedAt'>): Role {
    const now = new Date()
    const r: Role = { id: this.nextId('roles'), ...role, createdAt: now, updatedAt: now }
    this.roles.push(r)
    return r
  }

  getRoleByName(name: Role['name']): Role | undefined {
    return this.roles.find((r) => r.name === name)
  }

  // ---- Users ----
  createUser(user: Omit<User, 'id' | 'createdAt'>): User {
    const now = new Date()
    const u: User = { id: this.nextId('users'), ...user, createdAt: now, emailVerifiedAt: null, rememberToken: null }
    this.users.push(u)
    return u
  }

  getUserById(id: number): User | undefined {
    return this.users.find((u) => u.id === id)
  }

  getUserByEmail(email: string): User | undefined {
    return this.users.find((u) => u.email === email)
  }

  updateUser(id: number, data: Partial<User>): User | undefined {
    const u = this.getUserById(id)
    if (!u) return undefined
    Object.assign(u, data)
    return u
  }

  // ---- Clients ----
  createClient(client: Omit<Client, 'id' | 'createdAt' | 'updatedAt'>): Client {
    const now = new Date()
    const c: Client = { id: this.nextId('clients'), ...client, createdAt: now, updatedAt: now }
    this.clients.push(c)
    return c
  }

  getClientByUserId(userId: number): Client | undefined {
    return this.clients.find((c) => c.userId === userId)
  }

  getClientById(id: number): Client | undefined {
    return this.clients.find((c) => c.id === id)
  }

  updateClient(id: number, data: Partial<Client>): Client | undefined {
    const c = this.getClientById(id)
    if (!c) return undefined
    Object.assign(c, { ...data, updatedAt: new Date() })
    return c
  }

  // ---- Products ----
  createProduct(product: Omit<Product, 'id' | 'createdAt' | 'updatedAt'>): Product {
    const now = new Date()
    const p: Product = { id: this.nextId('products'), ...product, createdAt: now, updatedAt: now }
    this.products.push(p)
    return p
  }

  getProducts(filters?: { category?: string; isActive?: boolean }): Product[] {
    return this.products.filter((p) => {
      if (filters?.category && p.category !== filters.category) return false
      if (filters?.isActive !== undefined && p.isActive !== filters.isActive) return false
      return true
    })
  }

  getProductById(id: number): Product | undefined {
    return this.products.find((p) => p.id === id)
  }

  getProductBySlug(slug: string): Product | undefined {
    return this.products.find((p) => p.slug === slug)
  }

  updateProduct(id: number, data: Partial<Product>): Product | undefined {
    const p = this.getProductById(id)
    if (!p) return undefined
    Object.assign(p, { ...data, updatedAt: new Date() })
    return p
  }

  // ---- Orders ----
  createOrder(order: Omit<Order, 'id' | 'createdAt' | 'updatedAt'>): Order {
    const now = new Date()
    const o: Order = { id: this.nextId('orders'), ...order, createdAt: now, updatedAt: now }
    this.orders.push(o)
    return o
  }

  getOrdersByClient(clientId: number): Order[] {
    return this.orders.filter((o) => o.clientId === clientId).sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  getAllOrders(): Order[] {
    return [...this.orders].sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  getOrderById(id: number): Order | undefined {
    return this.orders.find((o) => o.id === id)
  }

  updateOrder(id: number, data: Partial<Order>): Order | undefined {
    const o = this.getOrderById(id)
    if (!o) return undefined
    Object.assign(o, { ...data, updatedAt: new Date() })
    return o
  }

  // ---- Order Items ----
  createOrderItem(item: Omit<OrderItem, 'id' | 'createdAt' | 'updatedAt'>): OrderItem {
    const now = new Date()
    const oi: OrderItem = { id: this.nextId('order_items'), ...item, createdAt: now, updatedAt: now }
    this.orderItems.push(oi)
    return oi
  }

  getOrderItemsByOrder(orderId: number): OrderItem[] {
    return this.orderItems.filter((oi) => oi.orderId === orderId)
  }

  // ---- Services ----
  createService(service: Omit<Service, 'id' | 'createdAt' | 'updatedAt'>): Service {
    const now = new Date()
    const s: Service = { id: this.nextId('services'), ...service, createdAt: now, updatedAt: now }
    this.services.push(s)
    return s
  }

  getServicesByClient(clientId: number): Service[] {
    return this.services.filter((s) => s.clientId === clientId).sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  getAllServices(): Service[] {
    return [...this.services].sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  getServiceById(id: number): Service | undefined {
    return this.services.find((s) => s.id === id)
  }

  updateService(id: number, data: Partial<Service>): Service | undefined {
    const s = this.getServiceById(id)
    if (!s) return undefined
    Object.assign(s, { ...data, updatedAt: new Date() })
    return s
  }

  // ---- Invoices ----
  createInvoice(invoice: Omit<Invoice, 'id' | 'createdAt' | 'updatedAt'>): Invoice {
    const now = new Date()
    const inv: Invoice = { id: this.nextId('invoices'), ...invoice, createdAt: now, updatedAt: now }
    this.invoices.push(inv)
    return inv
  }

  getInvoicesByClient(clientId: number): Invoice[] {
    return this.invoices.filter((i) => i.clientId === clientId).sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  getAllInvoices(): Invoice[] {
    return [...this.invoices].sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  getInvoiceById(id: number): Invoice | undefined {
    return this.invoices.find((i) => i.id === id)
  }

  updateInvoice(id: number, data: Partial<Invoice>): Invoice | undefined {
    const inv = this.getInvoiceById(id)
    if (!inv) return undefined
    Object.assign(inv, { ...data, updatedAt: new Date() })
    return inv
  }

  // ---- Invoice Items ----
  createInvoiceItem(item: any): any {
    const now = new Date()
    const ii = { id: this.nextId('invoice_items'), ...item, createdAt: now, updatedAt: now }
    this.invoiceItems.push(ii)
    return ii
  }

  getInvoiceItemsByInvoice(invoiceId: number): any[] {
    return this.invoiceItems.filter((ii) => ii.invoiceId === invoiceId)
  }

  // ---- Payments ----
  createPayment(payment: Omit<Payment, 'id' | 'createdAt' | 'updatedAt'>): Payment {
    const now = new Date()
    const p: Payment = { id: this.nextId('payments'), ...payment, createdAt: now, updatedAt: now }
    this.payments.push(p)
    return p
  }

  getPayments(): Payment[] {
    return [...this.payments].sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  // ---- Support Tickets ----
  createTicket(ticket: Omit<SupportTicket, 'id' | 'createdAt' | 'updatedAt'>): SupportTicket {
    const now = new Date()
    const t: SupportTicket = { id: this.nextId('support_tickets'), ...ticket, createdAt: now, updatedAt: now }
    this.supportTickets.push(t)
    return t
  }

  getTicketsByClient(clientId: number): SupportTicket[] {
    return this.supportTickets.filter((t) => t.clientId === clientId).sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  getAllTickets(): SupportTicket[] {
    return [...this.supportTickets].sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  getTicketById(id: number): SupportTicket | undefined {
    return this.supportTickets.find((t) => t.id === id)
  }

  updateTicket(id: number, data: Partial<SupportTicket>): SupportTicket | undefined {
    const t = this.getTicketById(id)
    if (!t) return undefined
    Object.assign(t, { ...data, updatedAt: new Date() })
    return t
  }

  // ---- Ticket Messages ----
  createTicketMessage(message: Omit<TicketMessage, 'id' | 'createdAt' | 'updatedAt'>): TicketMessage {
    const now = new Date()
    const tm: TicketMessage = { id: this.nextId('ticket_messages'), ...message, createdAt: now, updatedAt: now }
    this.ticketMessages.push(tm)
    return tm
  }

  getTicketMessagesByTicket(ticketId: number): TicketMessage[] {
    return this.ticketMessages.filter((m) => m.ticketId === ticketId).sort((a, b) => a.createdAt.getTime() - b.createdAt.getTime())
  }

  // ---- Servers ----
  getAllServers(): Server[] {
    return [...this.servers].sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  // ---- Suppliers ----
  getAllSuppliers(): Supplier[] {
    return [...this.suppliers].sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
  }

  // ---- Audit Logs ----
  createAuditLog(log: Omit<AuditLog, 'id' | 'createdAt' | 'updatedAt'>): AuditLog {
    const now = new Date()
    const al: AuditLog = { id: this.nextId('audit_logs'), ...log, createdAt: now, updatedAt: now }
    this.auditLogs.push(al)
    return al
  }

  getAuditLogs(limit = 100): AuditLog[] {
    return [...this.auditLogs].sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime()).slice(0, limit)
  }

  // ---- Hosting Inquiries ----
  createHostingInquiry(inquiry: Omit<HostingInquiry, 'id' | 'createdAt' | 'updatedAt'>): HostingInquiry {
    const now = new Date()
    const hi: HostingInquiry = { id: this.nextId('hosting_inquiries'), ...inquiry, createdAt: now, updatedAt: now }
    this.hostingInquiries.push(hi)
    return hi
  }

  // ---- Notifications ----
  createNotification(notif: Omit<Notification, 'id' | 'createdAt'>): Notification {
    const now = new Date()
    const n: Notification = { id: this.nextId('notifications'), ...notif, createdAt: now, read: false }
    this.notifications.push(n)
    return n
  }

  getNotificationsByUser(userId: number, limit = 5): Notification[] {
    return this.notifications
      .filter((n) => n.userId === userId)
      .sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime())
      .slice(0, limit)
  }
}

// ============================================================
// Seed data (identic cu seed-ul Laravel)
// ============================================================

export const dbStore = new DataStore()

function seed() {
  const roleAdmin = dbStore.createRole({ name: 'admin', label: 'Administrator' })
  const roleClient = dbStore.createRole({ name: 'client', label: 'Client' })
  const roleOperator = dbStore.createRole({ name: 'payment_operator', label: 'Operator plăți' })

  const adminUser = dbStore.createUser({
    name: 'Administrator',
    email: 'admin@eloquence.local',
    password: 'admin123',
    roleId: roleAdmin.id,
    isActive: true,
    emailVerifiedAt: null,
    rememberToken: null,
  })
  const clientUser = dbStore.createUser({
    name: 'Ion Popescu',
    email: 'client@eloquence.local',
    password: 'client123',
    roleId: roleClient.id,
    isActive: true,
    emailVerifiedAt: null,
    rememberToken: null,
  })
  const operatorUser = dbStore.createUser({
    name: 'Operator Plăți',
    email: 'operator@eloquence.local',
    password: 'operator123',
    roleId: roleOperator.id,
    isActive: true,
    emailVerifiedAt: null,
    rememberToken: null,
  })

  const client = dbStore.createClient({
    userId: clientUser.id,
    company: 'SC Popescu SRL',
    phone: '+40722123456',
    address: 'Str. Victoriei 12',
    city: 'București',
    country: 'RO',
    taxId: 'RO12345678',
  })

  const products: any[] = [
    { name: 'Hosting Start', slug: 'hosting-start', category: 'hosting', summary: 'Ideal pentru site-uri personale și micro-afaceri.', cpuCores: 1, ramMb: 1024, storageGb: 20, price: 19.99, billingPeriod: 'monthly', features: ['1 GB RAM', '20 GB SSD', '1 domeniu', 'Suport 24/7', 'Backup zilnic'], isActive: true, isFeatured: true, sortOrder: 1 },
    { name: 'Hosting Business', slug: 'hosting-business', category: 'hosting', summary: 'Pentru site-uri de business cu trafic moderat.', cpuCores: 2, ramMb: 4096, storageGb: 100, price: 49.99, billingPeriod: 'monthly', features: ['4 GB RAM', '100 GB SSD', '5 domenii', 'Suport prioritar', 'Backup zilnic', 'SSL gratuit'], isActive: true, isFeatured: false, sortOrder: 2 },
    { name: 'Hosting Pro', slug: 'hosting-pro', category: 'hosting', summary: 'Pentru site-uri cu trafic ridicat și e-commerce.', cpuCores: 4, ramMb: 8192, storageGb: 200, price: 89.99, billingPeriod: 'monthly', features: ['8 GB RAM', '200 GB SSD', 'Domenii nelimitate', 'Suport 24/7 prioritar', 'Backup orar', 'SSL gratuit', 'CDN inclus'], isActive: true, isFeatured: true, sortOrder: 3 },
    { name: 'VPS Start', slug: 'vps-start', category: 'vps', summary: 'Server privat virtual pentru dezvoltatori.', cpuCores: 2, ramMb: 4096, storageGb: 60, price: 59.99, billingPeriod: 'monthly', features: ['2 vCPU', '4 GB RAM', '60 GB SSD', '1 IP dedicat', 'Acces root', 'Suport 24/7'], isActive: true, isFeatured: false, sortOrder: 1 },
    { name: 'VPS Pro', slug: 'vps-pro', category: 'vps', summary: 'VPS cu resurse crespute pentru aplicații de producție.', cpuCores: 4, ramMb: 8192, storageGb: 120, price: 119.99, billingPeriod: 'monthly', features: ['4 vCPU', '8 GB RAM', '120 GB SSD', '2 IP dedicat', 'Acces root', 'Backup zilnic', 'Monitoring 24/7'], isActive: true, isFeatured: true, sortOrder: 2 },
    { name: 'Domeniu .ro', slug: 'domeniu-ro', category: 'domain', summary: 'Înregistrare domeniu național .ro.', cpuCores: null, ramMb: null, storageGb: null, price: 45.0, billingPeriod: 'yearly', features: ['Domeniu .ro', 'Protection WHOIS', 'DNS management', 'Email forwarding'], isActive: true, isFeatured: false, sortOrder: 1 },
    { name: 'Email Business', slug: 'email-business', category: 'email', summary: 'Email profesional pe domeniul propriu.', cpuCores: null, ramMb: null, storageGb: null, price: 12.0, billingPeriod: 'monthly', features: ['10 GB cutie poștală', 'Antispam', 'Antivirus', 'Sincronizare mobile', 'Webmail'], isActive: true, isFeatured: false, sortOrder: 1 },
    { name: 'SSL Certificate', slug: 'ssl-certificate', category: 'ssl', summary: 'Certificate SSL pentru securitate și încredere.', cpuCores: null, ramMb: null, storageGb: null, price: 35.0, billingPeriod: 'yearly', features: ['DV SSL', 'Activare rapidă', 'Site seal', 'Garantie 10 000 $'], isActive: true, isFeatured: false, sortOrder: 1 },
    { name: 'Backup Cloud', slug: 'backup-cloud', category: 'backup', summary: 'Copii de siguranță automate în cloud.', cpuCores: null, ramMb: null, storageGb: 500, price: 25.0, billingPeriod: 'monthly', features: ['500 GB stocare', 'Backup zilnic automat', 'Restaurare un clic', 'Criptare AES-256'], isActive: true, isFeatured: false, sortOrder: 1 },
  ]
  for (const p of products) dbStore.createProduct(p)

  dbStore.servers.push(
    { id: 1, name: 'Server București-1', hostname: 'srv-b1.eloquence.local', location: 'București, RO', provider: 'OVH', status: 'active', capacityPercent: 65, createdAt: new Date(), updatedAt: new Date() },
    { id: 2, name: 'Server Cluj-1', hostname: 'srv-c1.eloquence.local', location: 'Cluj-Napoca, RO', provider: 'Hetzner', status: 'active', capacityPercent: 42, createdAt: new Date(), updatedAt: new Date() },
    { id: 3, name: 'Server Timișoara-1', hostname: 'srv-t1.eloquence.local', location: 'Timișoara, RO', provider: 'AWS', status: 'planned', capacityPercent: 0, createdAt: new Date(), updatedAt: new Date() },
  )
  dbStore.idCounters['servers'] = 3

  dbStore.suppliers.push(
    { id: 1, name: 'OVHcloud', serviceType: 'Infrastructure', contactEmail: 'sales@ovh.com', monthlyCost: 1200.0, status: 'active', createdAt: new Date(), updatedAt: new Date() },
    { id: 2, name: 'Hetzner', serviceType: 'VPS / Dedicated', contactEmail: 'support@hetzner.com', monthlyCost: 850.0, status: 'active', createdAt: new Date(), updatedAt: new Date() },
    { id: 3, name: 'AWS', serviceType: 'Cloud', contactEmail: 'aws-support@amazon.com', monthlyCost: 2400.0, status: 'active', createdAt: new Date(), updatedAt: new Date() },
  )
  dbStore.idCounters['suppliers'] = 3

  dbStore.auditLogs.push(
    { id: 1, userId: adminUser.id, action: 'Client account created', entityType: 'client', entityId: client.id, metadata: null, ipAddress: '127.0.0.1', createdAt: new Date(Date.now() - 86400000 * 5), updatedAt: new Date(Date.now() - 86400000 * 5) },
    { id: 2, userId: adminUser.id, action: 'Product created', entityType: 'product', entityId: 1, metadata: null, ipAddress: '127.0.0.1', createdAt: new Date(Date.now() - 86400000 * 3), updatedAt: new Date(Date.now() - 86400000 * 3) },
    { id: 3, userId: clientUser.id, action: 'Order placed', entityType: 'order', entityId: 1, metadata: null, ipAddress: '127.0.0.1', createdAt: new Date(Date.now() - 86400000 * 2), updatedAt: new Date(Date.now() - 86400000 * 2) },
  )
  dbStore.idCounters['audit_logs'] = 3

  dbStore.notifications.push(
    { id: 1, userId: clientUser.id, title: 'Bun venit!', body: 'Contul tău a fost creat cu succes.', url: '/client/dashboard', read: false, createdAt: new Date(Date.now() - 86400000) },
  )
  dbStore.idCounters['notifications'] = 1
}

seed()

// Backward-compatibil export: `db` este DataStore-ul (pentru acțiunile server)
export { dbStore as db }
