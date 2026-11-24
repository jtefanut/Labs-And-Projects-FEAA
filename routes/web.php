<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\HostingInquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['ro', 'en'], true), 404);
    session(['locale' => $locale]);

    return redirect()->back();
})->name('language.switch');
Route::get('/hosting', fn () => app(PageController::class)->catalog('hosting'))->name('hosting');
Route::get('/vps', fn () => app(PageController::class)->catalog('vps'))->name('vps');
Route::get('/domenii', fn () => app(PageController::class)->catalog('domain'))->name('domains');
Route::get('/email', fn () => app(PageController::class)->catalog('email'))->name('email');
Route::get('/cloud', fn () => app(PageController::class)->catalog('cloud'))->name('cloud');
Route::get('/wordpress', fn () => app(PageController::class)->catalog('wordpress'))->name('wordpress');
Route::get('/backup', fn () => app(PageController::class)->catalog('backup'))->name('backup');
Route::get('/ssl', fn () => app(PageController::class)->catalog('ssl'))->name('ssl');
Route::get('/servicii/{category}', fn (string $category) => app(PageController::class)->catalog($category))->where('category', '[a-z-]+')->name('catalog.category');
Route::get('/services/{category}', fn (string $category) => app(PageController::class)->catalog($category))->where('category', '[a-z-]+')->name('catalog.category.en');
Route::get('/contact', fn () => view('public.contact'))->name('contact');
Route::post('/contact', HostingInquiryController::class)->name('contact.store');
Route::get('/despre', [PageController::class, 'about'])->name('about');
Route::get('/termeni', fn () => app(PageController::class)->legal('terms'))->name('legal.terms');
Route::get('/confidentialitate', fn () => app(PageController::class)->legal('privacy'))->name('legal.privacy');
Route::get('/cookies', fn () => app(PageController::class)->legal('cookies'))->name('legal.cookies');
Route::get('/retragere', fn () => app(PageController::class)->legal('withdrawal'))->name('legal.withdrawal');
Route::get('/reclamatii', fn () => app(PageController::class)->legal('complaints'))->name('legal.complaints');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('operator')->name('operator.')->middleware(['auth', 'payment-operator'])->group(function () {
    Route::get('/plati', [AdminController::class, 'payments'])->name('payments');
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments.en');
});

Route::get('/about', [PageController::class, 'about'])->name('about.en');

Route::prefix('client')->name('client.')->middleware(['auth', 'client'])->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/servicii', [ClientPortalController::class, 'services'])->name('services');
    Route::get('/comenzi', [ClientPortalController::class, 'orders'])->name('orders');
    Route::post('/comenzi', [ClientPortalController::class, 'createOrder'])->name('orders.store');
    Route::get('/facturi', [ClientPortalController::class, 'invoices'])->name('invoices');
    Route::post('/facturi/{invoiceId}/plata-demonstrativa', [ClientPortalController::class, 'simulatePayment'])->whereNumber('invoiceId')->name('payments.store');
    Route::get('/tichete', [ClientPortalController::class, 'tickets'])->name('tickets');
    Route::post('/tichete', [ClientPortalController::class, 'createTicket'])->name('tickets.store');
    Route::post('/tichete/{ticketId}/mesaje', [ClientPortalController::class, 'replyToTicket'])->whereNumber('ticketId')->name('tickets.reply');
    Route::get('/profil', [ClientPortalController::class, 'profile'])->name('profile');
    Route::put('/profil', [ClientPortalController::class, 'updateProfile'])->name('profile.update');
});

Route::middleware(['auth', 'client'])->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard'])->name('dashboard.en');
    Route::get('/services', [ClientPortalController::class, 'services'])->name('services.en');
    Route::get('/orders', [ClientPortalController::class, 'orders'])->name('orders.en');
    Route::post('/orders', [ClientPortalController::class, 'createOrder'])->name('orders.store.en');
    Route::get('/invoices', [ClientPortalController::class, 'invoices'])->name('invoices.en');
    Route::post('/invoices/{invoiceId}/simulate-payment', [ClientPortalController::class, 'simulatePayment'])->whereNumber('invoiceId')->name('payments.store.en');
    Route::get('/tickets', [ClientPortalController::class, 'tickets'])->name('tickets.en');
    Route::post('/tickets', [ClientPortalController::class, 'createTicket'])->name('tickets.store.en');
    Route::post('/tickets/{ticketId}/messages', [ClientPortalController::class, 'replyToTicket'])->whereNumber('ticketId')->name('tickets.reply.en');
    Route::get('/profile', [ClientPortalController::class, 'profile'])->name('profile.en');
    Route::put('/profile', [ClientPortalController::class, 'updateProfile'])->name('profile.update.en');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('', [AdminController::class, 'dashboard'])->name('root');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [AdminController::class, 'dashboard'])->name('home');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/messages', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    Route::get('/rapoarte/export.csv', [AdminController::class, 'exportReports'])->name('reports.export');
    Route::get('/rapoarte', [AdminController::class, 'reports'])->name('reports');
    Route::get('/products', fn () => app(AdminController::class)->index('produse'))->name('products.index');
    Route::post('/products', [AdminController::class, 'createProduct'])->name('products.store.en');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update.en');
    Route::get('/clients', fn () => app(AdminController::class)->index('clienti'))->name('clients.index');
    Route::get('/clients/{client}/edit', [AdminController::class, 'editClient'])->name('clients.edit');
    Route::put('/clients/{client}', [AdminController::class, 'updateClient'])->name('clients.update');
    Route::get('/orders', fn () => app(AdminController::class)->index('comenzi'))->name('orders.index');
    Route::get('/services', fn () => app(AdminController::class)->index('servicii'))->name('services.index');
    Route::get('/invoices', fn () => app(AdminController::class)->index('facturi'))->name('invoices.index');
    Route::get('/tickets', fn () => app(AdminController::class)->index('tichete'))->name('tickets.index');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.en');
    Route::get('/audit-logs', fn () => app(AdminController::class)->index('audit'))->name('audit.index');
    Route::get('/{section}', [AdminController::class, 'index'])->whereIn('section', ['clienti', 'produse', 'comenzi', 'servicii', 'facturi', 'tichete', 'servere', 'furnizori', 'rapoarte', 'utilizatori', 'audit'])->name('section');
    Route::post('/{section}/{id}/status', [AdminController::class, 'updateStatus'])->whereIn('section', ['produse', 'comenzi', 'servicii', 'facturi', 'tichete'])->name('status.update');
    Route::post('/produse', [AdminController::class, 'createProduct'])->name('products.store');
    Route::put('/produse/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
});
