<?php

use App\Http\Controllers\AccountPasswordController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\NoAccessController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| TUVAT BA Certification Management System
| Business Assurance / ISO Certification Tracking App
|
| Public verification stays open. Admin management routes are protected
| with the auth middleware group below.
|
*/

// --- Public Routes ---
Route::get('/', [CertificateController::class, 'search'])->name('certificate.search');

// --- Authentication ---
Auth::routes([
    'register' => config('cvs.registration_enabled', false),
    'verify' => true,
]);

Route::get('/reset', function () {
    return view('auth.passwords.email');
});

Route::get('/admin', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('login');
});

Route::get('/login', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('login');
})->name('login');

Route::post('/login/addCredentials', [CertificateController::class, 'addCredentials'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('certificate.login');

// --- Authenticated Routes ---
Route::middleware(['auth', 'user.active'])->group(function () {
    Route::get('/account/password', [AccountPasswordController::class, 'edit'])->name('account.password.edit');
    Route::post('/account/password', [AccountPasswordController::class, 'update'])->name('account.password.update');

    Route::middleware(['verified', 'password.changed'])->group(function () {
        Route::get('/no-access', NoAccessController::class)->name('no-access');

        Route::middleware('super.admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
            Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
            Route::post('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
            Route::post('/departments/{department}/toggle', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle');

            Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/send-password-reset', [UserManagementController::class, 'sendPasswordReset'])->name('users.send-password-reset');
            Route::post('/users/{user}/resend-verification', [UserManagementController::class, 'resendVerification'])->name('users.resend-verification');
        });

        Route::middleware('super.admin')->group(function () {
            Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        });

        Route::middleware('app.access')->group(function () {
            Route::get('/dashboard', [CertificateController::class, 'getDashboard'])->name('dashboard');

            Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');

            Route::get('/clients', [CertificateController::class, 'getAllClients'])->name('clients');
            Route::get('/view-client/{id}', [CertificateController::class, 'viewClient'])->name('client.view');

            Route::get('/view-certificate/{id}', [CertificateController::class, 'viewCertificate'])->name('certificate.view');

            Route::middleware('app.mutate')->group(function () {
                Route::get('/add-client', [CertificateController::class, 'addClient'])->name('client.add');
                Route::get('/edit-client/{id}', [CertificateController::class, 'editClient'])->name('client.edit');
                Route::get('/add-certificate', [CertificateController::class, 'addCertificate'])->name('certificate.add');
                Route::get('/add-certificate/{clientId}', [CertificateController::class, 'addCertificate'])->name('certificate.addForClient');
                Route::get('/edit-certificate/{id}', [CertificateController::class, 'editCertificate'])->name('certificate.edit');
                Route::get('/imports-exports', [CertificateController::class, 'importExportView'])->name('importsExports');
            });

            Route::get('/pending-certificates', [CertificateController::class, 'getPendingCertificates'])->name('pendingCertificates');
            Route::get('/upcoming-audits', [CertificateController::class, 'upcomingAudits'])->name('upcomingAudits');
            Route::get('/expired-certificates', [CertificateController::class, 'expiredCertificates'])->name('expiredCertificates');
            Route::get('/deleted-certificates', [CertificateController::class, 'getDeletedCertificates'])->name('deletedCertificates');

            Route::get('/download-pdf/{id}', [CertificateController::class, 'downloadPdf'])->name('certificate.downloadPdf');
            Route::get('/view-pdf/{id}', [CertificateController::class, 'viewPdf'])->name('certificate.viewPdf');
            Route::get('/download-audit-report/{id}', [CertificateController::class, 'downloadAuditReport'])->name('auditReport.download');
            Route::get('/view-audit-report/{id}', [CertificateController::class, 'viewAuditReport'])->name('auditReport.view');

            Route::get('/manage-standards', [CertificateController::class, 'manageStandards'])->name('standards.manage');
            Route::get('/manage-accreditation-bodies', [CertificateController::class, 'manageAccreditationBodies'])->name('accreditationBodies.manage');

            Route::get('/live-search', [CertificateController::class, 'liveSearch'])->name('liveSearch');
            Route::get('/live-search-pending', [CertificateController::class, 'liveSearchPending'])->name('liveSearchPending');
            Route::get('/live-search-deleted', [CertificateController::class, 'liveSearchDeleted'])->name('liveSearchDeleted');

            Route::middleware('app.mutate')->group(function () {
                Route::post('/add-client', [CertificateController::class, 'createClient'])->name('client.create');
                Route::post('/update-client', [CertificateController::class, 'updateClient'])->name('client.update');
                Route::delete('/delete-client/{id}', [CertificateController::class, 'deleteClient'])->name('client.delete');

                Route::post('/add-certificate', [CertificateController::class, 'createCertificate'])->name('certificate.create');
                Route::post('/update-certificate', [CertificateController::class, 'updateCertificate'])->name('certificate.update');
                Route::delete('/delete-certificate/{id}', [CertificateController::class, 'deleteCertificate'])->name('certificate.delete');
                Route::post('/certificates/bulk-delete', [CertificateController::class, 'bulkDeleteSelected'])
                    ->name('certificates.bulkDelete');

                Route::post('/review-certificate/{id}', [CertificateController::class, 'reviewCertificate'])->name('certificate.review');
                Route::post('/approve-certificate/{id}', [CertificateController::class, 'approveCertificate'])->name('certificate.approve');
                Route::post('/bulk-review', [CertificateController::class, 'bulkReview'])->name('bulkReview');
                Route::post('/bulk-approve', [CertificateController::class, 'bulkApprove'])->name('bulkApprove');
                Route::post('/certificates/bulk-review', [CertificateController::class, 'bulkReviewSelected'])
                    ->name('certificates.bulkReviewSelected');
                Route::post('/certificates/bulk-approve', [CertificateController::class, 'bulkApproveSelected'])
                    ->name('certificates.bulkApproveSelected');

                Route::post('/restore-certificate/{id}', [CertificateController::class, 'restoreCertificate'])->name('certificate.restore');

                Route::post('/upload-pdf/{id}', [CertificateController::class, 'uploadPdf'])->name('certificate.uploadPdf');
                Route::post('/upload-audit-report/{certificateId}', [CertificateController::class, 'uploadAuditReport'])->name('auditReport.upload');

                Route::post('/add-standard', [CertificateController::class, 'createStandard'])->name('standard.create');
                Route::post('/update-standard', [CertificateController::class, 'updateStandard'])->name('standard.update');
                Route::post('/add-accreditation-body', [CertificateController::class, 'createAccreditationBody'])->name('accreditationBody.create');
                Route::post('/update-accreditation-body', [CertificateController::class, 'updateAccreditationBody'])->name('accreditationBody.update');

                Route::get('/export', [CertificateController::class, 'export'])->name('export');
                Route::post('/import', [CertificateController::class, 'import'])->name('import');
            });
        });
    });
});
