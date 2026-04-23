<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\BranchController as AdminBranchController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\FinanceOperationsController as AdminFinanceOperationsController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\HomeSectionController as AdminHomeSectionController;
use App\Http\Controllers\Admin\MarketingSectionController as AdminMarketingSectionController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SubscriberController as AdminSubscriberController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WorkingHourController as AdminWorkingHourController;
use App\Http\Controllers\Admin\CareerApplicationController as AdminCareerApplicationController;
use App\Http\Controllers\Admin\CareerPositionController as AdminCareerPositionController;
use App\Http\Controllers\Admin\PatientController as AdminPatientController;
use App\Http\Controllers\Admin\PatientDocumentController as AdminPatientDocumentController;
use App\Http\Controllers\Admin\PatientVisitController as AdminPatientVisitController;
use App\Http\Controllers\Front\AppointmentController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\BranchController;
use App\Http\Controllers\Front\CareerController;
use App\Http\Controllers\Front\ChatController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\DoctorController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\NewsletterController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\ServiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/ar');
Route::get('/dashboard', function () {
    $locale = session('locale', config('app.locale', 'ar'));

    if (auth()->check() && auth()->user()->can('access-admin-dashboard')) {
        return redirect("/{$locale}/admin/dashboard");
    }

    return redirect("/{$locale}/profile");
})->middleware('auth')->name('dashboard');

Route::prefix('{locale}')
    ->whereIn('locale', ['ar', 'en'])
    ->middleware(['locale'])
    ->group(function (): void {
        Route::get('/', [HomeController::class, 'index'])->name('front.home');
        Route::get('/services', [ServiceController::class, 'index'])->name('front.services.index');
        Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('front.services.show');
        Route::get('/doctors', [DoctorController::class, 'index'])->name('front.doctors.index');
        Route::get('/doctors/{id}', [DoctorController::class, 'show'])->name('front.doctors.show');
        Route::get('/blog', [BlogController::class, 'index'])->name('front.blog.index');
        Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('front.blog.show');
        Route::get('/branches', [BranchController::class, 'index'])->name('front.branches.index');
        Route::get('/branches/{id}', [BranchController::class, 'show'])->name('front.branches.show');
        Route::get('/contact', [ContactController::class, 'index'])->name('front.contact.index');
        Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact-form')->name('front.contact.store');
        Route::get('/chat', [ChatController::class, 'index'])->name('front.chat.index');
        Route::post('/chat/messages', [ChatController::class, 'store'])->middleware('throttle:contact-form')->name('front.chat.store');
        Route::get('/chat/feed', [ChatController::class, 'feed'])->name('front.chat.feed');
        Route::get('/chat/stream', [ChatController::class, 'stream'])->name('front.chat.stream');
        Route::get('/careers', [CareerController::class, 'index'])->name('front.careers.index');
        Route::post('/careers/apply', [CareerController::class, 'store'])->middleware('throttle:contact-form')->name('front.careers.store');
        Route::get('/appointment', [AppointmentController::class, 'create'])->name('front.appointments.create');
        Route::get('/appointment/availability', [AppointmentController::class, 'availability'])->name('front.appointments.availability');
        Route::post('/appointment', [AppointmentController::class, 'store'])->middleware('throttle:appointment-form')->name('front.appointments.store');
        Route::get('/appointment/tracking', [AppointmentController::class, 'tracking'])->name('front.appointments.tracking');
        Route::post('/appointment/tracking', [AppointmentController::class, 'trackingSearch'])
            ->middleware('throttle:appointment-form')
            ->name('front.appointments.tracking.search');
        Route::get('/appointment/confirmation/{appointment}', [AppointmentController::class, 'confirmation'])
            ->middleware('signed')
            ->name('front.appointments.confirmation');
        Route::get('/appointment/confirmation/{appointment}/pdf', [AppointmentController::class, 'confirmationPdf'])
            ->middleware('signed')
            ->name('front.appointments.confirmation.pdf');
        Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:newsletter-form')->name('front.newsletter.store');
        Route::get('/page/{slug}', [PageController::class, 'show'])->name('front.pages.show');

        Route::middleware('auth')->group(function (): void {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            Route::prefix('admin')->name('admin.')->middleware('permission:access-admin-dashboard')->group(function (): void {
                Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
                Route::get('/charts', [DashboardController::class, 'charts'])->name('charts');
                Route::get('/dashboard/daily-report-pdf', [DashboardController::class, 'dailyReportPdf'])->name('dashboard.daily-report-pdf');

                Route::middleware('role:Super Admin|Content Manager')->group(function (): void {
                    Route::resource('branches', AdminBranchController::class)->middleware('permission:manage-content|manage-branches');
                    Route::resource('working-hours', AdminWorkingHourController::class)
                        ->parameters(['working-hours' => 'workingHour'])
                        ->except('show')
                        ->middleware('permission:manage-content|manage-working-hours');
                    Route::resource('services', AdminServiceController::class)->middleware('permission:manage-content|manage-services');
                    Route::resource('doctors', AdminDoctorController::class)->middleware('permission:manage-content|manage-doctors');
                    Route::resource('blog-posts', AdminBlogPostController::class)
                        ->parameters(['blog-posts' => 'blogPost'])
                        ->middleware('permission:manage-content|manage-blog');
                    Route::resource('pages', AdminPageController::class)->middleware('permission:manage-content|manage-pages');
                    Route::resource('career-positions', AdminCareerPositionController::class)
                        ->parameters(['career-positions' => 'careerPosition'])
                        ->middleware('permission:manage-content|manage-careers');
                    Route::resource('home-sections', AdminHomeSectionController::class)
                        ->parameters(['home-sections' => 'homeSection'])
                        ->middleware('permission:manage-content|manage-home-sections');
                    Route::get('marketing-sections', [AdminMarketingSectionController::class, 'edit'])
                        ->middleware('permission:manage-content|manage-marketing-sections')
                        ->name('marketing-sections.edit');
                    Route::put('marketing-sections', [AdminMarketingSectionController::class, 'update'])
                        ->middleware('permission:manage-content|manage-marketing-sections')
                        ->name('marketing-sections.update');
                    Route::resource('testimonials', AdminTestimonialController::class)->except('show')->middleware('permission:manage-content|manage-testimonials');
                    Route::resource('faqs', AdminFaqController::class)->except('show')->middleware('permission:manage-content|manage-faqs');
                });

                Route::middleware('role:Super Admin|Receptionist')->group(function (): void {
                    Route::middleware('permission:manage-patient-records')->group(function (): void {
                        Route::resource('patients', AdminPatientController::class);
                        Route::get('patients/{patient}/report', [AdminPatientController::class, 'report'])
                            ->whereNumber('patient')
                            ->name('patients.report');
                        Route::get('patients/{patient}/attachments-zip', [AdminPatientController::class, 'downloadAttachmentsZip'])
                            ->whereNumber('patient')
                            ->name('patients.attachments.zip');
                        Route::post('patients/{patient}/documents', [AdminPatientDocumentController::class, 'store'])
                            ->whereNumber('patient')
                            ->name('patients.documents.store');
                        Route::delete('patients/{patient}/documents/{document}', [AdminPatientDocumentController::class, 'destroy'])
                            ->whereNumber('patient')
                            ->whereNumber('document')
                            ->name('patients.documents.destroy');
                        Route::get('patients/{patient}/documents-medical-zip', [AdminPatientController::class, 'downloadMedicalDocumentsZip'])
                            ->whereNumber('patient')
                            ->name('patients.documents.medical-zip');
                        Route::get('patients/{patient}/visits/create', [AdminPatientVisitController::class, 'create'])->whereNumber('patient')->name('patients.visits.create');
                        Route::post('patients/{patient}/visits', [AdminPatientVisitController::class, 'store'])->whereNumber('patient')->name('patients.visits.store');
                        Route::get('patients/{patient}/visits/{visit}/edit', [AdminPatientVisitController::class, 'edit'])
                            ->whereNumber('patient')
                            ->whereNumber('visit')
                            ->name('patients.visits.edit');
                        Route::put('patients/{patient}/visits/{visit}', [AdminPatientVisitController::class, 'update'])
                            ->whereNumber('patient')
                            ->whereNumber('visit')
                            ->name('patients.visits.update');
                        Route::delete('patients/{patient}/visits/{visit}', [AdminPatientVisitController::class, 'destroy'])
                            ->whereNumber('patient')
                            ->whereNumber('visit')
                            ->name('patients.visits.destroy');
                        Route::delete('patients/{patient}/visits/{visit}/attachments/{attachment}', [AdminPatientVisitController::class, 'destroyAttachment'])
                            ->whereNumber('patient')
                            ->whereNumber('visit')
                            ->whereNumber('attachment')
                            ->name('patients.visits.attachments.destroy');
                    });

                    Route::post('appointments/{appointment}/quick-status', [AdminAppointmentController::class, 'quickUpdateStatus'])
                        ->name('appointments.quick-status');
                    Route::get('appointments/{appointment}/report', [AdminAppointmentController::class, 'report'])
                        ->name('appointments.report');
                    Route::get('appointments/{appointment}/report-pdf', [AdminAppointmentController::class, 'reportPdf'])
                        ->name('appointments.report-pdf');
                    Route::resource('appointments', AdminAppointmentController::class)->middleware('permission:manage-appointments');
                    Route::resource('finance', AdminFinanceController::class)->middleware('permission:manage-finance');
                    Route::post('finance-sync-appointments', [AdminFinanceController::class, 'syncAppointments'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.sync-appointments');
                    Route::get('finance-export', [AdminFinanceController::class, 'exportCsv'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.export');
                    Route::get('finance-export-xlsx', [AdminFinanceController::class, 'exportXlsx'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.export-xlsx');
                    Route::get('finance-report-pdf', [AdminFinanceController::class, 'reportPdf'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.report-pdf');
                    Route::get('accounting', [AdminFinanceController::class, 'accounting'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.accounting');
                    Route::post('accounting-sync', [AdminFinanceController::class, 'syncAccounting'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.accounting.sync');
                    Route::get('cashbox', [AdminFinanceController::class, 'cashbox'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.cashbox');
                    Route::post('cashbox', [AdminFinanceController::class, 'storeCashboxMovement'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.cashbox.store');
                    Route::get('finance-master-data', [AdminFinanceOperationsController::class, 'masterData'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.master-data');
                    Route::post('finance-master-data/cost-centers', [AdminFinanceOperationsController::class, 'storeCostCenter'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.cost-centers.store');
                    Route::post('finance-master-data/parties', [AdminFinanceOperationsController::class, 'storeParty'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.parties.store');
                    Route::get('finance-vouchers', [AdminFinanceOperationsController::class, 'vouchers'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.vouchers');
                    Route::post('finance-vouchers', [AdminFinanceOperationsController::class, 'storeVoucher'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.vouchers.store');
                    Route::get('finance-invoices', [AdminFinanceOperationsController::class, 'invoices'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices');
                    Route::get('finance-invoices/create', [AdminFinanceOperationsController::class, 'createInvoice'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices.create');
                    Route::get('finance-invoices/{invoice}', [AdminFinanceOperationsController::class, 'showInvoice'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices.show');
                    Route::get('finance-invoices/{invoice}/pdf', [AdminFinanceOperationsController::class, 'downloadInvoicePdf'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices.pdf');
                    Route::post('finance-invoices/{invoice}/zatca/generate', [AdminFinanceOperationsController::class, 'generateZatca'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices.zatca.generate');
                    Route::post('finance-invoices/{invoice}/zatca/validate', [AdminFinanceOperationsController::class, 'validateZatca'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices.zatca.validate');
                    Route::post('finance-invoices/{invoice}/zatca/submit', [AdminFinanceOperationsController::class, 'submitZatca'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices.zatca.submit');
                    Route::get('finance-invoices/{invoice}/zatca/xml', [AdminFinanceOperationsController::class, 'downloadZatcaXml'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices.zatca.xml');
                    Route::post('finance-invoices', [AdminFinanceOperationsController::class, 'storeInvoice'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.invoices.store');
                    Route::get('finance-reports', [AdminFinanceOperationsController::class, 'reports'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.reports');
                    Route::post('finance-reports/closing', [AdminFinanceOperationsController::class, 'storeClosing'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.reports.closing.store');
                    Route::get('finance-inventory', [AdminFinanceOperationsController::class, 'inventory'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.inventory');
                    Route::post('finance-inventory/warehouses', [AdminFinanceOperationsController::class, 'storeWarehouse'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.inventory.warehouses.store');
                    Route::post('finance-inventory/items', [AdminFinanceOperationsController::class, 'storeItem'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.inventory.items.store');
                    Route::post('finance-inventory/movements', [AdminFinanceOperationsController::class, 'storeMovement'])
                        ->middleware('permission:manage-finance')
                        ->name('finance.inventory.movements.store');
                    Route::post('appointments/{appointment}/convert-to-visit', [AdminAppointmentController::class, 'convertToVisit'])->name('appointments.convert-to-visit');
                    Route::get('messages/{message}/feed', [AdminContactMessageController::class, 'feed'])
                        ->middleware('permission:manage-messages')
                        ->name('messages.feed');
                    Route::get('messages/{message}/stream', [AdminContactMessageController::class, 'stream'])
                        ->middleware('permission:manage-messages')
                        ->name('messages.stream');
                    Route::resource('messages', AdminContactMessageController::class)
                        ->parameters(['messages' => 'message'])
                        ->only(['index', 'show', 'update', 'destroy'])
                        ->middleware('permission:manage-messages');
                    Route::resource('career-applications', AdminCareerApplicationController::class)
                        ->parameters(['career-applications' => 'careerApplication'])
                        ->only(['index', 'show', 'update', 'destroy'])
                        ->middleware('permission:manage-content|manage-career-applications');
                    Route::resource('subscribers', AdminSubscriberController::class)->only(['index', 'destroy'])->middleware('permission:manage-subscribers');
                    Route::get('subscribers-export', [AdminSubscriberController::class, 'exportCsv'])->middleware('permission:manage-subscribers')->name('subscribers.export');
                });

                Route::middleware('role:Super Admin')->group(function (): void {
                    Route::resource('users', AdminUserController::class)->except('show')->middleware('permission:manage-users');
                    Route::get('users/{user}/permissions', [AdminUserController::class, 'editPermissions'])->middleware('permission:manage-users')->name('users.permissions.edit');
                    Route::put('users/{user}/permissions', [AdminUserController::class, 'updatePermissions'])->middleware('permission:manage-users')->name('users.permissions.update');
                    Route::get('users-permissions-matrix', [AdminUserController::class, 'permissionsMatrix'])
                        ->middleware('permission:manage-users')
                        ->name('users.permissions.matrix');
                    Route::put('users-permissions-matrix', [AdminUserController::class, 'updatePermissionsMatrix'])
                        ->middleware('permission:manage-users')
                        ->name('users.permissions.matrix.update');
                    Route::resource('settings', AdminSettingController::class)->only(['index', 'store', 'update'])->middleware('permission:manage-settings');
                });
            });
        });
    });

require __DIR__ . '/auth.php';
