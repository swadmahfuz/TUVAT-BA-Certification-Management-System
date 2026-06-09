<?php

use App\Http\Controllers\CertificateController;
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
| This app is hosted under its own subdomain and shares the following with the other CVS apps:
| - common database
| - common users table
| - common session/login system
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [CertificateController::class, 'search'])->name('certificate.search');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Auth::routes(['register' => false]);

Route::get('/reset', function () {
    return view('auth.passwords.email');
});

Route::get('/admin', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('/login');
});

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('/login');
});

Route::post('/login/addCredentials', [CertificateController::class, 'addCredentials'])->name('certificate.login');
Route::get('/logout', [CertificateController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [CertificateController::class, 'getDashboard'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| BA Clients
|--------------------------------------------------------------------------
*/

Route::get('/clients', [CertificateController::class, 'getAllClients'])->name('clients');
Route::get('/add-client', [CertificateController::class, 'addClient'])->name('client.add');
Route::post('/add-client', [CertificateController::class, 'createClient'])->name('client.create');
Route::get('/view-client/{id}', [CertificateController::class, 'viewClient'])->name('client.view');
Route::get('/edit-client/{id}', [CertificateController::class, 'editClient'])->name('client.edit');
Route::post('/update-client', [CertificateController::class, 'updateClient'])->name('client.update');
Route::get('/delete-client/{id}', [CertificateController::class, 'deleteClient'])->name('client.delete');

/*
|--------------------------------------------------------------------------
| BA Certificates
|--------------------------------------------------------------------------
*/

Route::get('/add-certificate', [CertificateController::class, 'addCertificate'])->name('certificate.add');
Route::get('/add-certificate/{clientId}', [CertificateController::class, 'addCertificate'])->name('certificate.addForClient');
Route::post('/add-certificate', [CertificateController::class, 'createCertificate'])->name('certificate.create');

Route::get('/view-certificate/{id}', [CertificateController::class, 'viewCertificate'])->name('certificate.view');
Route::get('/edit-certificate/{id}', [CertificateController::class, 'editCertificate'])->name('certificate.edit');
Route::post('/update-certificate', [CertificateController::class, 'updateCertificate'])->name('certificate.update');
Route::get('/delete-certificate/{id}', [CertificateController::class, 'deleteCertificate'])->name('certificate.delete');

/*
|--------------------------------------------------------------------------
| Review and Approval
|--------------------------------------------------------------------------
*/

Route::get('/pending-certificates', [CertificateController::class, 'getPendingCertificates'])->name('pendingCertificates');
Route::get('/review-certificate/{id}', [CertificateController::class, 'reviewCertificate'])->name('certificate.review');
Route::get('/approve-certificate/{id}', [CertificateController::class, 'approveCertificate'])->name('certificate.approve');
Route::get('/bulk-review', [CertificateController::class, 'bulkReview'])->name('bulkReview');
Route::get('/bulk-approve', [CertificateController::class, 'bulkApprove'])->name('bulkApprove');

/*
|--------------------------------------------------------------------------
| Audit Tracking
|--------------------------------------------------------------------------
*/

Route::get('/upcoming-audits', [CertificateController::class, 'upcomingAudits'])->name('upcomingAudits');
Route::get('/expired-certificates', [CertificateController::class, 'expiredCertificates'])->name('expiredCertificates');

/*
|--------------------------------------------------------------------------
| Deleted Records
|--------------------------------------------------------------------------
*/

Route::get('/deleted-certificates', [CertificateController::class, 'getDeletedCertificates'])->name('deletedCertificates');
Route::get('/restore-certificate/{id}', [CertificateController::class, 'restoreCertificate'])->name('certificate.restore');

/*
|--------------------------------------------------------------------------
| Certificate PDF Handling
|--------------------------------------------------------------------------
*/

Route::post('/upload-pdf/{id}', [CertificateController::class, 'uploadPdf'])->name('certificate.uploadPdf');
Route::get('/download-pdf/{id}', [CertificateController::class, 'downloadPdf'])->name('certificate.downloadPdf');
Route::get('/view-pdf/{id}', [CertificateController::class, 'viewPdf'])->name('certificate.viewPdf');

/*
|--------------------------------------------------------------------------
| Audit Report Handling
|--------------------------------------------------------------------------
*/

Route::post('/upload-audit-report/{certificateId}', [CertificateController::class, 'uploadAuditReport'])->name('auditReport.upload');
Route::get('/download-audit-report/{id}', [CertificateController::class, 'downloadAuditReport'])->name('auditReport.download');
Route::get('/view-audit-report/{id}', [CertificateController::class, 'viewAuditReport'])->name('auditReport.view');

/*
|--------------------------------------------------------------------------
| Standards
|--------------------------------------------------------------------------
*/

Route::get('/manage-standards', [CertificateController::class, 'manageStandards'])->name('standards.manage');
Route::post('/add-standard', [CertificateController::class, 'createStandard'])->name('standard.create');
Route::post('/update-standard', [CertificateController::class, 'updateStandard'])->name('standard.update');

/*
|--------------------------------------------------------------------------
| Accreditation Bodies
|--------------------------------------------------------------------------
*/

Route::get('/manage-accreditation-bodies', [CertificateController::class, 'manageAccreditationBodies'])->name('accreditationBodies.manage');
Route::post('/add-accreditation-body', [CertificateController::class, 'createAccreditationBody'])->name('accreditationBody.create');
Route::post('/update-accreditation-body', [CertificateController::class, 'updateAccreditationBody'])->name('accreditationBody.update');

/*
|--------------------------------------------------------------------------
| Import / Export
|--------------------------------------------------------------------------
*/

Route::get('/imports-exports', [CertificateController::class, 'importExportView'])->name('importsExports');
Route::get('/export', [CertificateController::class, 'export'])->name('export');
Route::post('/import', [CertificateController::class, 'import'])->name('import');

/*
|--------------------------------------------------------------------------
| Live Search
|--------------------------------------------------------------------------
*/

Route::get('/live-search', [CertificateController::class, 'liveSearch'])->name('liveSearch');
Route::get('/live-search-pending', [CertificateController::class, 'liveSearchPending'])->name('liveSearchPending');
Route::get('/live-search-deleted', [CertificateController::class, 'liveSearchDeleted'])->name('liveSearchDeleted');

/*
|--------------------------------------------------------------------------
| User List
|--------------------------------------------------------------------------
*/

Route::get('/all-users', [CertificateController::class, 'showAllUsers'])->name('allUsers');