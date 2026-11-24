import initSqlJs from 'sql.js'
import fs from 'fs'
import path from 'path'

const DB_PATH = process.env.DATABASE_URL?.replace(/^file:/, '') ||
  path.resolve(process.cwd(), 'database.sqlite')

const SQL = await initSqlJs({
  locateFile: (file: string) => `sql-wasm.wasm`,
})

let db: any

try {
  const data = fs.readFileSync(DB_PATH)
  db = new SQL.Database(data)
} catch {
  db = new SQL.Database()
  // Schema (identică cu migrațiile Laravel)
  db.exec(`
    CREATE TABLE IF NOT EXISTS roles (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT UNIQUE NOT NULL, label TEXT NOT NULL, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')));
    CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL UNIQUE, password TEXT NOT NULL, role_id INTEGER, is_active INTEGER DEFAULT 1, email_verified_at TEXT, remember_token TEXT, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL);
    CREATE TABLE IF NOT EXISTS clients (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL UNIQUE, company TEXT, phone TEXT, address TEXT, city TEXT, country TEXT DEFAULT 'RO', tax_id TEXT, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE);
    CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, slug TEXT UNIQUE NOT NULL, category TEXT NOT NULL, summary TEXT NOT NULL, cpu_cores INTEGER, ram_mb INTEGER, storage_gb INTEGER, price REAL NOT NULL, billing_period TEXT DEFAULT 'monthly', features TEXT, is_active INTEGER DEFAULT 1, is_featured INTEGER DEFAULT 0, sort_order INTEGER DEFAULT 0, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')));
    CREATE TABLE IF NOT EXISTS orders (id INTEGER PRIMARY KEY AUTOINCREMENT, order_number TEXT UNIQUE NOT NULL, client_id INTEGER NOT NULL, status TEXT DEFAULT 'pending', subtotal REAL NOT NULL, tax REAL DEFAULT 0, total REAL NOT NULL, currency TEXT DEFAULT 'RON', created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE);
    CREATE TABLE IF NOT EXISTS order_items (id INTEGER PRIMARY KEY AUTOINCREMENT, order_id INTEGER NOT NULL, product_id INTEGER NOT NULL, quantity INTEGER DEFAULT 1, unit_price REAL NOT NULL, line_total REAL NOT NULL, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE, FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT);
    CREATE TABLE IF NOT EXISTS services (id INTEGER PRIMARY KEY AUTOINCREMENT, client_id INTEGER NOT NULL, product_id INTEGER NOT NULL, order_id INTEGER, service_number TEXT UNIQUE NOT NULL, hostname TEXT UNIQUE, status TEXT DEFAULT 'pending', starts_at TEXT, renews_at TEXT, expires_at TEXT, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE, FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT, FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL);
    CREATE TABLE IF NOT EXISTS invoices (id INTEGER PRIMARY KEY AUTOINCREMENT, invoice_number TEXT UNIQUE NOT NULL, client_id INTEGER NOT NULL, order_id INTEGER, subtotal REAL NOT NULL, tax REAL DEFAULT 0, total REAL NOT NULL, currency TEXT DEFAULT 'RON', status TEXT DEFAULT 'unpaid', due_at TEXT, paid_at TEXT, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE, FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL);
    CREATE TABLE IF NOT EXISTS invoice_items (id INTEGER PRIMARY KEY AUTOINCREMENT, invoice_id INTEGER NOT NULL, product_id INTEGER, description TEXT NOT NULL, quantity INTEGER DEFAULT 1, unit_price REAL NOT NULL, line_total REAL NOT NULL, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE, FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL);
    CREATE TABLE IF NOT EXISTS payments (id INTEGER PRIMARY KEY AUTOINCREMENT, invoice_id INTEGER NOT NULL, reference TEXT UNIQUE, amount REAL NOT NULL, method TEXT, status TEXT DEFAULT 'pending', paid_at TEXT, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE);
    CREATE TABLE IF NOT EXISTS support_tickets (id INTEGER PRIMARY KEY AUTOINCREMENT, ticket_number TEXT UNIQUE NOT NULL, client_id INTEGER NOT NULL, service_id INTEGER, subject TEXT NOT NULL, priority TEXT DEFAULT 'normal', category TEXT DEFAULT 'general', status TEXT DEFAULT 'open', created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE, FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL);
    CREATE TABLE IF NOT EXISTS ticket_messages (id INTEGER PRIMARY KEY AUTOINCREMENT, ticket_id INTEGER NOT NULL, user_id INTEGER NOT NULL, body TEXT NOT NULL, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE);
    CREATE TABLE IF NOT EXISTS servers (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, hostname TEXT UNIQUE NOT NULL, location TEXT, provider TEXT, status TEXT DEFAULT 'planned', capacity_percent INTEGER DEFAULT 0, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')));
    CREATE TABLE IF NOT EXISTS suppliers (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, service_type TEXT NOT NULL, contact_email TEXT, monthly_cost REAL DEFAULT 0, status TEXT DEFAULT 'active', created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')));
    CREATE TABLE IF NOT EXISTS audit_logs (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, action TEXT NOT NULL, entity_type TEXT, entity_id INTEGER, metadata TEXT, ip_address TEXT, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL);
    CREATE TABLE IF NOT EXISTS hosting_inquiries (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL, company TEXT, message TEXT NOT NULL, created_at TEXT DEFAULT (datetime('now')), updated_at TEXT DEFAULT (datetime('now')));
    CREATE TABLE IF NOT EXISTS notifications (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, title TEXT NOT NULL, body TEXT NOT NULL, url TEXT, read INTEGER DEFAULT 0, created_at TEXT DEFAULT (datetime('now')), FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE);
  `)
  // Seed
  const now = new Date().toISOString()
  db.run("INSERT INTO roles (name, label) VALUES ('admin', 'Administrator')")
  db.run("INSERT INTO roles (name, label) VALUES ('client', 'Client')")
  db.run("INSERT INTO roles (name, label) VALUES ('payment_operator', 'Operator plăți')")
  db.run("INSERT INTO users (name, email, password, role_id, is_active) VALUES ('Administrator', 'admin@eloquence.local', 'admin123', 1, 1)")
  db.run("INSERT INTO users (name, email, password, role_id, is_active) VALUES ('Ion Popescu', 'client@eloquence.local', 'client123', 2, 1)")
  db.run("INSERT INTO users (name, email, password, role_id, is_active) VALUES ('Operator Plăți', 'operator@eloquence.local', 'operator123', 3, 1)")
  db.run("INSERT INTO clients (user_id, company, phone, address, city, country, tax_id) VALUES (2, 'SC Popescu SRL', '+40722123456', 'Str. Victoriei 12', 'București', 'RO', 'RO12345678')")
  // Products
  const products = [
    ['Hosting Start', 'hosting-start', 'hosting', 'Ideal pentru site-uri personale și micro-afaceri.', 1, 1024, 20, 19.99, 'monthly', JSON.stringify(['1 GB RAM', '20 GB SSD', '1 domeniu', 'Suport 24/7', 'Backup zilnic']), 1, 1, 1],
    ['Hosting Business', 'hosting-business', 'hosting', 'Pentru site-uri de business cu trafic moderat.', 2, 4096, 100, 49.99, 'monthly', JSON.stringify(['4 GB RAM', '100 GB SSD', '5 domenii', 'Suport prioritar', 'Backup zilnic', 'SSL gratuit']), 1, 0, 2],
    ['Hosting Pro', 'hosting-pro', 'hosting', 'Pentru site-uri cu trafic ridicat și e-commerce.', 4, 8192, 200, 89.99, 'monthly', JSON.stringify(['8 GB RAM', '200 GB SSD', 'Domenii nelimitate', 'Suport 24/7 prioritar', 'Backup orar', 'SSL gratuit', 'CDN inclus']), 1, 1, 3],
    ['VPS Start', 'vps-start', 'vps', 'Server privat virtual pentru dezvoltatori.', 2, 4096, 60, 59.99, 'monthly', JSON.stringify(['2 vCPU', '4 GB RAM', '60 GB SSD', '1 IP dedicat', 'Acces root', 'Suport 24/7']), 1, 0, 1],
    ['VPS Pro', 'vps-pro', 'vps', 'VPS cu resurse crespute pentru aplicații de producție.', 4, 8192, 120, 119.99, 'monthly', JSON.stringify(['4 vCPU', '8 GB RAM', '120 GB SSD', '2 IP dedicat', 'Acces root', 'Backup zilnic', 'Monitoring 24/7']), 1, 1, 2],
    ['Domeniu .ro', 'domeniu-ro', 'domain', 'Înregistrare domeniu național .ro.', null, null, null, 45.0, 'yearly', JSON.stringify(['Domeniu .ro', 'Protection WHOIS', 'DNS management', 'Email forwarding']), 1, 0, 1],
    ['Email Business', 'email-business', 'email', 'Email profesional pe domeniul propriu.', null, null, null, 12.0, 'monthly', JSON.stringify(['10 GB cutie poștală', 'Antispam', 'Antivirus', 'Sincronizare mobile', 'Webmail']), 1, 0, 1],
    ['SSL Certificate', 'ssl-certificate', 'ssl', 'Certificate SSL pentru securitate și încredere.', null, null, null, 35.0, 'yearly', JSON.stringify(['DV SSL', 'Activare rapidă', 'Site seal', 'Garantie 10 000 $']), 1, 0, 1],
    ['Backup Cloud', 'backup-cloud', 'backup', 'Copii de siguranță automate în cloud.', null, null, 500, 25.0, 'monthly', JSON.stringify(['500 GB stocare', 'Backup zilnic automat', 'Restaurare un clic', 'Criptare AES-256']), 1, 0, 1],
  ]
  for (const p of products) {
    db.run('INSERT INTO products (name, slug, category, summary, cpu_cores, ram_mb, storage_gb, price, billing_period, features, is_active, is_featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', p)
  }
  db.run("INSERT INTO servers (name, hostname, location, provider, status, capacity_percent) VALUES ('Server București-1', 'srv-b1.eloquence.local', 'București, RO', 'OVH', 'active', 65)")
  db.run("INSERT INTO servers (name, hostname, location, provider, status, capacity_percent) VALUES ('Server Cluj-1', 'srv-c1.eloquence.local', 'Cluj-Napoca, RO', 'Hetzner', 'active', 42)")
  db.run("INSERT INTO servers (name, hostname, location, provider, status, capacity_percent) VALUES ('Server Timișoara-1', 'srv-t1.eloquence.local', 'Timișoara, RO', 'AWS', 'planned', 0)")
  db.run("INSERT INTO suppliers (name, service_type, contact_email, monthly_cost, status) VALUES ('OVHcloud', 'Infrastructure', 'sales@ovh.com', 1200.0, 'active')")
  db.run("INSERT INTO suppliers (name, service_type, contact_email, monthly_cost, status) VALUES ('Hetzner', 'VPS / Dedicated', 'support@hetzner.com', 850.0, 'active')")
  db.run("INSERT INTO suppliers (name, service_type, contact_email, monthly_cost, status) VALUES ('AWS', 'Cloud', 'aws-support@amazon.com', 2400.0, 'active')")
  db.run("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address) VALUES (1, 'Client account created', 'client', 1, '127.0.0.1')")
  db.run("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address) VALUES (1, 'Product created', 'product', 1, '127.0.0.1')")
  db.run("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address) VALUES (2, 'Order placed', 'order', 1, '127.0.0.1')")
  db.run("INSERT INTO notifications (user_id, title, body, url, read) VALUES (2, 'Bun venit!', 'Contul tău a fost creat cu succes.', '/client/dashboard', 0)")
}

// Persistă pe disk
function saveDb() {
  try {
    const data = db.export()
    fs.writeFileSync(DB_PATH, Buffer.from(data))
  } catch {
    // ignore
  }
}

export { db, saveDb }
export default db
