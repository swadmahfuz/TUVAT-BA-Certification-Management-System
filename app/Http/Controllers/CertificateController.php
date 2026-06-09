<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BaClient;
use App\Models\BaStandard;
use App\Models\BaAccreditationBody;
use App\Models\BaCertificate;
use App\Models\BaAuditReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\CertificateExport;
use App\Imports\CertificateImport;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| TUVAT BA Certification Management System
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Head of Divison - Business Assurance & Training, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
| Latest Stable Release: v3.4.2 -  10 June 2026
|--------------------------------------------------------------------------
*/


class CertificateController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Public / Unauthenticated Functions
    |--------------------------------------------------------------------------
    */

    public function search(Request $request)
    {
        if ($request->search == null) {
            return view('verify-certificate');
        }

        $certificates = BaCertificate::with(['client', 'standard', 'accreditationBody'])
            ->where('certificate_number', $request->search)
            ->where('status', 'Approved')
            ->paginate(1);

        return view('verify-certificate', compact('certificates'));
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication Functions
    |--------------------------------------------------------------------------
    */

    public function addCredentials(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect('/dashboard')->with('success', 'Thank You for authorizing. Please proceed.');
        }

        return redirect('/admin')->with('error', 'You entered the wrong credentials');
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            return redirect('/admin');
        }

        return redirect()->route('certificate.search');
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function getDashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $today = Carbon::today();
        $next90Days = Carbon::today()->addDays(90);

        $totalClients = BaClient::count();
        $totalCertificates = BaCertificate::count();
        $activeCertificates = BaCertificate::where('certificate_status', 'Active')->count();

        $pendingReview = BaCertificate::where('status', 'Pending Review')->count();
        $pendingApproval = BaCertificate::where('status', 'Pending Approval')->count();
        $approvedCertificates = BaCertificate::where('status', 'Approved')->count();

        $upcomingSurveillance1 = BaCertificate::whereBetween('surveillance_1_due_date', [$today, $next90Days])->count();
        $upcomingSurveillance2 = BaCertificate::whereBetween('surveillance_2_due_date', [$today, $next90Days])->count();
        $upcomingRecertification = BaCertificate::whereBetween('recertification_due_date', [$today, $next90Days])->count();

        $expiredCertificates = BaCertificate::whereDate('certificate_expiry_date', '<', $today)->count();

        $expiredWithinGrace = BaCertificate::whereDate('certificate_expiry_date', '<', $today)
            ->whereDate('grace_period_end_date', '>=', $today)
            ->count();

        $expiredBeyondGrace = BaCertificate::whereDate('grace_period_end_date', '<', $today)->count();

        $certificates = BaCertificate::with(['client', 'standard', 'accreditationBody'])
            ->orderBy('created_at', 'DESC')
            ->paginate(100);

        return view('dashboard', compact(
            'certificates',
            'totalClients',
            'totalCertificates',
            'activeCertificates',
            'pendingReview',
            'pendingApproval',
            'approvedCertificates',
            'upcomingSurveillance1',
            'upcomingSurveillance2',
            'upcomingRecertification',
            'expiredCertificates',
            'expiredWithinGrace',
            'expiredBeyondGrace'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    public function showAllUsers()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $users = User::orderBy('name')->get();

        return view('all-users', compact('users'));
    }

    /*
    |--------------------------------------------------------------------------
    | BA Clients
    |--------------------------------------------------------------------------
    */

    public function getAllClients(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $query = BaClient::withCount('certificates')->orderBy('client_name');

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', '%' . $search . '%')
                    ->orWhere('client_address', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $clients = $query->paginate(100);

        return view('all-clients', compact('clients'));
    }

    public function addClient()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        return view('add-client');
    }

    public function createClient(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $client = BaClient::create([
            'client_name' => $request->client_name,
            'client_address' => $request->client_address,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'remarks' => $request->remarks,
            'created_by' => Auth::user()->name,
            'created_by_id' => Auth::user()->id,
        ]);

        return redirect('/view-client/' . $client->id)->with('success', 'Client added successfully.');
    }

    public function viewClient($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $client = BaClient::with([
            'certificates.standard',
            'certificates.accreditationBody',
            'certificates.auditReports'
        ])->withTrashed()->findOrFail($id);

        return view('view-client', compact('client'));
    }

    public function editClient($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $client = BaClient::findOrFail($id);

        return view('edit-client', compact('client'));
    }

    public function updateClient(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'id' => 'required|exists:ba_clients,id',
            'client_name' => 'required|string|max:255',
            'client_address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $client = BaClient::findOrFail($request->id);

        $client->update([
            'client_name' => $request->client_name,
            'client_address' => $request->client_address,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'remarks' => $request->remarks,
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::user()->id,
        ]);

        return redirect('/view-client/' . $client->id)->with('success', 'Client updated successfully.');
    }

    public function deleteClient($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $client = BaClient::withCount('certificates')->findOrFail($id);

        if ($client->certificates_count > 0) {
            return back()->with('error', 'This client has certificate records. Please delete or transfer the certificate records first.');
        }

        $client->update([
            'deleted_by' => Auth::user()->name,
            'deleted_by_id' => Auth::user()->id,
        ]);

        $client->delete();

        return redirect('/clients')->with('success', 'Client deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | BA Certificates
    |--------------------------------------------------------------------------
    */

    public function addCertificate($clientId = null)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $clients = BaClient::orderBy('client_name')->get();
        $standards = BaStandard::where('status', 'Active')->orderBy('standard_name')->get();
        $accreditationBodies = BaAccreditationBody::where('status', 'Active')->orderBy('accreditation_body_name')->get();
        $users = User::orderBy('name')->get();

        $selectedClient = $clientId ? BaClient::find($clientId) : null;

        return view('add-certificate', compact(
            'clients',
            'standards',
            'accreditationBodies',
            'users',
            'selectedClient'
        ));
    }

    public function createCertificate(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'ba_client_id' => 'required|exists:ba_clients,id',
            'ba_standard_id' => 'nullable|exists:ba_standards,id',
            'ba_accreditation_body_id' => 'nullable|exists:ba_accreditation_bodies,id',

            'certificate_number' => 'nullable|string|max:255|unique:ba_certificates,certificate_number',
            'certificate_scope' => 'nullable|string',
            'certificate_issue_date' => 'nullable|date',
            'certificate_expiry_date' => 'nullable|date',
            'certification_cycle' => 'nullable|string|max:255',
            'initial_certification_audit_completion_date' => 'nullable|date',

            'audit_status' => 'nullable|string|max:255',
            'certificate_status' => 'nullable|string|max:255',

            'lead_auditor' => 'nullable|string|max:255',
            'auditor_1' => 'nullable|string|max:255',
            'auditor_2' => 'nullable|string|max:255',
            'auditor_3' => 'nullable|string|max:255',
            'technical_expert' => 'nullable|string|max:255',

            'review_by' => 'required|string|max:255',
            'approval_by' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $reviewer = User::where('name', $request->review_by)->first();
        $approver = User::where('name', $request->approval_by)->first();

        $cycleDates = $this->calculateCycleDates(
            $request->initial_certification_audit_completion_date,
            $request->certificate_expiry_date
        );

        $certificate = BaCertificate::create([
            'ba_client_id' => $request->ba_client_id,
            'ba_standard_id' => $request->ba_standard_id,
            'ba_accreditation_body_id' => $request->ba_accreditation_body_id,

            'certificate_number' => $request->certificate_number,
            'certificate_scope' => $request->certificate_scope,
            'certificate_issue_date' => $request->certificate_issue_date,
            'certificate_expiry_date' => $request->certificate_expiry_date,
            'certification_cycle' => $request->certification_cycle,
            'initial_certification_audit_completion_date' => $request->initial_certification_audit_completion_date,

            'surveillance_1_due_date' => $cycleDates['surveillance_1_due_date'],
            'surveillance_2_due_date' => $cycleDates['surveillance_2_due_date'],
            'recertification_due_date' => $cycleDates['recertification_due_date'],
            'grace_period_end_date' => $cycleDates['grace_period_end_date'],

            'audit_status' => $request->audit_status ?? 'Not Scheduled',
            'certificate_status' => $request->certificate_status ?? 'Active',

            'lead_auditor' => $request->lead_auditor,
            'auditor_1' => $request->auditor_1,
            'auditor_2' => $request->auditor_2,
            'auditor_3' => $request->auditor_3,
            'technical_expert' => $request->technical_expert,

            'status' => 'Pending Review',
            'created_by' => Auth::user()->name,
            'created_by_id' => Auth::user()->id,
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::user()->id,

            'review_by' => $request->review_by,
            'review_by_id' => $reviewer ? $reviewer->id : null,
            'approval_by' => $request->approval_by,
            'approval_by_id' => $approver ? $approver->id : null,

            'remarks' => $request->remarks,
        ]);

        return redirect('/view-certificate/' . $certificate->id)->with('success', 'Certificate record added successfully.');
    }

    public function viewCertificate($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::with([
            'client',
            'standard',
            'accreditationBody',
            'auditReports'
        ])->withTrashed()->findOrFail($id);

        return view('view-certificate', compact('certificate'));
    }

    public function editCertificate($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::findOrFail($id);
        $clients = BaClient::orderBy('client_name')->get();
        $standards = BaStandard::where('status', 'Active')->orderBy('standard_name')->get();
        $accreditationBodies = BaAccreditationBody::where('status', 'Active')->orderBy('accreditation_body_name')->get();
        $users = User::orderBy('name')->get();

        return view('edit-certificate', compact(
            'certificate',
            'clients',
            'standards',
            'accreditationBodies',
            'users'
        ));
    }

    public function updateCertificate(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'id' => 'required|exists:ba_certificates,id',
            'ba_client_id' => 'required|exists:ba_clients,id',
            'ba_standard_id' => 'nullable|exists:ba_standards,id',
            'ba_accreditation_body_id' => 'nullable|exists:ba_accreditation_bodies,id',

            'certificate_number' => 'nullable|string|max:255|unique:ba_certificates,certificate_number,' . $request->id,
            'certificate_scope' => 'nullable|string',
            'certificate_issue_date' => 'nullable|date',
            'certificate_expiry_date' => 'nullable|date',
            'certification_cycle' => 'nullable|string|max:255',
            'initial_certification_audit_completion_date' => 'nullable|date',

            'audit_status' => 'nullable|string|max:255',
            'certificate_status' => 'nullable|string|max:255',

            'lead_auditor' => 'nullable|string|max:255',
            'auditor_1' => 'nullable|string|max:255',
            'auditor_2' => 'nullable|string|max:255',
            'auditor_3' => 'nullable|string|max:255',
            'technical_expert' => 'nullable|string|max:255',

            'review_by' => 'required|string|max:255',
            'approval_by' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $certificate = BaCertificate::findOrFail($request->id);

        $reviewer = User::where('name', $request->review_by)->first();
        $approver = User::where('name', $request->approval_by)->first();

        $cycleDates = $this->calculateCycleDates(
            $request->initial_certification_audit_completion_date,
            $request->certificate_expiry_date
        );

        $certificate->update([
            'ba_client_id' => $request->ba_client_id,
            'ba_standard_id' => $request->ba_standard_id,
            'ba_accreditation_body_id' => $request->ba_accreditation_body_id,

            'certificate_number' => $request->certificate_number,
            'certificate_scope' => $request->certificate_scope,
            'certificate_issue_date' => $request->certificate_issue_date,
            'certificate_expiry_date' => $request->certificate_expiry_date,
            'certification_cycle' => $request->certification_cycle,
            'initial_certification_audit_completion_date' => $request->initial_certification_audit_completion_date,

            'surveillance_1_due_date' => $cycleDates['surveillance_1_due_date'],
            'surveillance_2_due_date' => $cycleDates['surveillance_2_due_date'],
            'recertification_due_date' => $cycleDates['recertification_due_date'],
            'grace_period_end_date' => $cycleDates['grace_period_end_date'],

            'audit_status' => $request->audit_status ?? 'Not Scheduled',
            'certificate_status' => $request->certificate_status ?? 'Active',

            'lead_auditor' => $request->lead_auditor,
            'auditor_1' => $request->auditor_1,
            'auditor_2' => $request->auditor_2,
            'auditor_3' => $request->auditor_3,
            'technical_expert' => $request->technical_expert,

            'review_by' => $request->review_by,
            'review_by_id' => $reviewer ? $reviewer->id : null,
            'reviewed_at' => null,

            'approval_by' => $request->approval_by,
            'approval_by_id' => $approver ? $approver->id : null,
            'approved_at' => null,

            'status' => 'Pending Review',
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::user()->id,

            'remarks' => $request->remarks,
        ]);

        return redirect('/view-certificate/' . $certificate->id)->with('success', 'Certificate record updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Review and Approval
    |--------------------------------------------------------------------------
    */

    public function getPendingCertificates()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $userId = Auth::user()->id;
        $userName = Auth::user()->name;

        $certificates = BaCertificate::with(['client', 'standard', 'accreditationBody'])
            ->where(function ($query) use ($userId, $userName) {
                $query->where(function ($q) use ($userId, $userName) {
                    $q->where('status', 'Pending Review')
                        ->where(function ($subQuery) use ($userId, $userName) {
                            $subQuery->where('review_by_id', $userId)
                                ->orWhere('review_by', $userName);
                        });
                })
                ->orWhere(function ($q) use ($userId, $userName) {
                    $q->where('status', 'Pending Approval')
                        ->where(function ($subQuery) use ($userId, $userName) {
                            $subQuery->where('approval_by_id', $userId)
                                ->orWhere('approval_by', $userName);
                        });
                });
            })
            ->whereNotIn('status', ['Approved', 'approved', ' APPROVED'])
            ->orderBy('created_at', 'DESC')
            ->paginate(100);

        return view('pending-certificates', compact('certificates'));
    }

    public function reviewCertificate($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::findOrFail($id);

        if (Auth::user()->id != $certificate->review_by_id && Auth::user()->name != $certificate->review_by) {
            return back()->with('error', 'Unauthorized: You are not assigned to review this certificate.');
        }

        $certificate->update([
            'status' => 'Pending Approval',
            'reviewed_at' => Carbon::now(),
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::user()->id,
        ]);

        return redirect('/view-certificate/' . $certificate->id)->with('success', 'Certificate marked as reviewed.');
    }

    public function approveCertificate($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::findOrFail($id);

        if (Auth::user()->id != $certificate->approval_by_id && Auth::user()->name != $certificate->approval_by) {
            return back()->with('error', 'Unauthorized: You are not assigned to approve this certificate.');
        }

        if ($certificate->status !== 'Pending Approval') {
            return back()->with('error', 'Certificate must be reviewed before approval.');
        }

        $certificate->update([
            'status' => 'Approved',
            'approved_at' => Carbon::now(),
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::user()->id,
        ]);

        return back()->with('success', 'Certificate approved successfully.');
    }

    public function bulkReview()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $user = Auth::user();

        $updated = DB::table('ba_certificates')
            ->where('status', 'Pending Review')
            ->where(function ($query) use ($user) {
                $query->where('review_by_id', $user->id)
                    ->orWhere('review_by', $user->name);
            })
            ->update([
                'status' => 'Pending Approval',
                'updated_by' => $user->name,
                'updated_by_id' => $user->id,
                'updated_at' => Carbon::now(),
                'reviewed_at' => Carbon::now(),
            ]);

        return redirect()->back()->with('success', "$updated certificate(s) marked as Reviewed.");
    }

    public function bulkApprove()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $user = Auth::user();

        $updated = DB::table('ba_certificates')
            ->where('status', 'Pending Approval')
            ->where(function ($query) use ($user) {
                $query->where('approval_by_id', $user->id)
                    ->orWhere('approval_by', $user->name);
            })
            ->update([
                'status' => 'Approved',
                'updated_by' => $user->name,
                'updated_by_id' => $user->id,
                'updated_at' => Carbon::now(),
                'approved_at' => Carbon::now(),
            ]);

        return redirect()->back()->with('success', "$updated certificate(s) marked as Approved.");
    }

    /*
    |--------------------------------------------------------------------------
    | Audit Tracking Pages
    |--------------------------------------------------------------------------
    */

    public function upcomingAudits()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $today = Carbon::today();
        $next90Days = Carbon::today()->addDays(90);

        $certificates = BaCertificate::with(['client', 'standard', 'accreditationBody'])
            ->where(function ($query) use ($today, $next90Days) {
                $query->whereBetween('surveillance_1_due_date', [$today, $next90Days])
                    ->orWhereBetween('surveillance_2_due_date', [$today, $next90Days])
                    ->orWhereBetween('recertification_due_date', [$today, $next90Days]);
            })
            ->orderBy('surveillance_1_due_date', 'ASC')
            ->paginate(100);

        return view('upcoming-audits', compact('certificates'));
    }

    public function expiredCertificates()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $today = Carbon::today();

        $certificates = BaCertificate::with(['client', 'standard', 'accreditationBody'])
            ->whereDate('certificate_expiry_date', '<', $today)
            ->orderBy('certificate_expiry_date', 'ASC')
            ->paginate(100);

        return view('expired-certificates', compact('certificates'));
    }

    /*
    |--------------------------------------------------------------------------
    | Delete / Restore
    |--------------------------------------------------------------------------
    */

    public function deleteCertificate($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::findOrFail($id);

        $certificate->update([
            'status' => 'Deleted',
            'deleted_by' => Auth::user()->name,
            'deleted_by_id' => Auth::user()->id,
            'reviewed_at' => null,
            'approved_at' => null,
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::user()->id,
        ]);

        $certificate->delete();

        return back()->with('success', 'Certificate record deleted successfully.');
    }

    public function getDeletedCertificates()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificates = BaCertificate::onlyTrashed()
            ->with(['client', 'standard', 'accreditationBody'])
            ->orderBy('deleted_at', 'DESC')
            ->paginate(100);

        $clients = BaClient::onlyTrashed()
            ->orderBy('deleted_at', 'DESC')
            ->paginate(100);

        return view('deleted-certificates', compact('certificates', 'clients'));
    }

    public function restoreCertificate($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::onlyTrashed()->findOrFail($id);

        $certificate->restore();

        $certificate->update([
            'status' => 'Pending Review',
            'deleted_by' => null,
            'deleted_by_id' => null,
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::user()->id,
        ]);

        return back()->with('success', 'Certificate restored successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Certificate PDF Upload / Download / View
    |--------------------------------------------------------------------------
    */

    public function uploadPdf(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'certificate_pdf' => 'required|mimes:pdf|max:20480',
        ]);

        $certificate = BaCertificate::with('client')->findOrFail($id);

        $user = Auth::user();

        $isAuthorized = (
            $user->id == $certificate->review_by_id ||
            $user->id == $certificate->approval_by_id ||
            $user->id == $certificate->created_by_id ||
            $user->name == $certificate->review_by ||
            $user->name == $certificate->approval_by ||
            $user->name == $certificate->created_by
        );

        if (!$isAuthorized) {
            return back()->with('error', 'You are not authorized to upload this certificate PDF.');
        }

        $destinationPath = public_path('BA Certificate PDFs');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $pdfFile = $request->file('certificate_pdf');
        $timestamp = Carbon::now()->format('YmdHi');
        $clientName = $certificate->client ? $certificate->client->client_name : 'Client';
        $safeClientName = preg_replace('/[^A-Za-z0-9\- ]/', '', $clientName);

        $fileName = 'TUVAT BA Cert - ' . $safeClientName . ' - ' . $timestamp . '.' . $pdfFile->getClientOriginalExtension();

        $pdfFile->move($destinationPath, $fileName);

        $certificate->update([
            'certificate_pdf' => $fileName,
            'pdf_uploaded_by' => $user->name,
            'pdf_uploaded_by_id' => $user->id,
            'pdf_uploaded_at' => Carbon::now(),
            'updated_by' => $user->name,
            'updated_by_id' => $user->id,
        ]);

        return back()->with('success', 'Certificate PDF uploaded successfully.');
    }

    public function downloadPdf($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::findOrFail($id);

        $filePath = public_path('BA Certificate PDFs/' . $certificate->certificate_pdf);

        if (!$certificate->certificate_pdf || !file_exists($filePath)) {
            return back()->with('error', 'PDF file not found.');
        }

        return response()->download($filePath, $certificate->certificate_pdf);
    }

    public function viewPdf($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::findOrFail($id);

        $filePath = public_path('BA Certificate PDFs/' . $certificate->certificate_pdf);

        if (!$certificate->certificate_pdf || !file_exists($filePath)) {
            abort(404, 'PDF not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $certificate->certificate_pdf . '"',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Audit Report Upload / Download / View
    |--------------------------------------------------------------------------
    */

    public function uploadAuditReport(Request $request, $certificateId)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $certificate = BaCertificate::findOrFail($certificateId);

        $request->validate([
            'audit_year' => 'required|string|max:20',
            'audit_type' => 'required|string|max:255',
            'audit_date' => 'nullable|date',
            'audit_report_file' => 'required|mimes:pdf|max:20480',
            'remarks' => 'nullable|string',
        ]);

        $destinationPath = public_path('BA Audit Reports');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file = $request->file('audit_report_file');
        $timestamp = Carbon::now()->format('YmdHi');

        $fileName = 'TUVAT BA Audit Report - Cert ID ' . $certificate->id . ' - ' . $request->audit_year . ' - ' . $timestamp . '.' . $file->getClientOriginalExtension();

        $file->move($destinationPath, $fileName);

        BaAuditReport::create([
            'ba_certificate_id' => $certificate->id,
            'audit_year' => $request->audit_year,
            'audit_type' => $request->audit_type,
            'audit_date' => $request->audit_date,
            'audit_report_file' => $fileName,
            'uploaded_by' => Auth::user()->name,
            'uploaded_by_id' => Auth::user()->id,
            'uploaded_at' => Carbon::now(),
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Audit report uploaded successfully.');
    }

    public function downloadAuditReport($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $report = BaAuditReport::findOrFail($id);

        $filePath = public_path('BA Audit Reports/' . $report->audit_report_file);

        if (!$report->audit_report_file || !file_exists($filePath)) {
            return back()->with('error', 'Audit report file not found.');
        }

        return response()->download($filePath, $report->audit_report_file);
    }

    public function viewAuditReport($id)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $report = BaAuditReport::findOrFail($id);

        $filePath = public_path('BA Audit Reports/' . $report->audit_report_file);

        if (!$report->audit_report_file || !file_exists($filePath)) {
            abort(404, 'Audit report not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $report->audit_report_file . '"',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Standards
    |--------------------------------------------------------------------------
    */

    public function manageStandards()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $standards = BaStandard::orderBy('standard_name')->get();

        return view('manage-standards', compact('standards'));
    }

    public function createStandard(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'standard_name' => 'required|string|max:255|unique:ba_standards,standard_name',
            'standard_code' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        BaStandard::create([
            'standard_name' => $request->standard_name,
            'standard_code' => $request->standard_code,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Standard added successfully.');
    }

    public function updateStandard(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'id' => 'required|exists:ba_standards,id',
            'standard_name' => 'required|string|max:255|unique:ba_standards,standard_name,' . $request->id,
            'standard_code' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        $standard = BaStandard::findOrFail($request->id);

        $standard->update([
            'standard_name' => $request->standard_name,
            'standard_code' => $request->standard_code,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Standard updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Accreditation Bodies
    |--------------------------------------------------------------------------
    */

    public function manageAccreditationBodies()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $accreditationBodies = BaAccreditationBody::orderBy('accreditation_body_name')->get();

        return view('manage-accreditation-bodies', compact('accreditationBodies'));
    }

    public function createAccreditationBody(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'accreditation_body_name' => 'required|string|max:255|unique:ba_accreditation_bodies,accreditation_body_name',
            'short_name' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        BaAccreditationBody::create([
            'accreditation_body_name' => $request->accreditation_body_name,
            'short_name' => $request->short_name,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Accreditation body added successfully.');
    }

    public function updateAccreditationBody(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $request->validate([
            'id' => 'required|exists:ba_accreditation_bodies,id',
            'accreditation_body_name' => 'required|string|max:255|unique:ba_accreditation_bodies,accreditation_body_name,' . $request->id,
            'short_name' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        $body = BaAccreditationBody::findOrFail($request->id);

        $body->update([
            'accreditation_body_name' => $request->accreditation_body_name,
            'short_name' => $request->short_name,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Accreditation body updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Live Search
    |--------------------------------------------------------------------------
    */

    public function liveSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $perPage = 100;
        $userInput = $request->input('userInput', '');

        $query = BaCertificate::with(['client', 'standard', 'accreditationBody']);

        if (!empty($userInput)) {
            $query->where(function ($q) use ($userInput) {
                $q->whereRaw('LOWER(certificate_number) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(certificate_scope) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(lead_auditor) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(auditor_1) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(auditor_2) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(auditor_3) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(technical_expert) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(certificate_status) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(audit_status) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereHas('client', function ($clientQuery) use ($userInput) {
                        $clientQuery->whereRaw('LOWER(client_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                            ->orWhereRaw('LOWER(contact_person) LIKE ?', ['%' . strtolower($userInput) . '%'])
                            ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($userInput) . '%'])
                            ->orWhereRaw('LOWER(phone) LIKE ?', ['%' . strtolower($userInput) . '%']);
                    })
                    ->orWhereHas('standard', function ($standardQuery) use ($userInput) {
                        $standardQuery->whereRaw('LOWER(standard_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                            ->orWhereRaw('LOWER(standard_code) LIKE ?', ['%' . strtolower($userInput) . '%']);
                    })
                    ->orWhereHas('accreditationBody', function ($bodyQuery) use ($userInput) {
                        $bodyQuery->whereRaw('LOWER(accreditation_body_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                            ->orWhereRaw('LOWER(short_name) LIKE ?', ['%' . strtolower($userInput) . '%']);
                    });
            });
        }

        $result = $query->orderBy('created_at', 'DESC')->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    public function liveSearchDeleted(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $perPage = 100;
        $userInput = $request->input('userInput', '');

        $query = BaCertificate::onlyTrashed()->with(['client', 'standard', 'accreditationBody']);

        if (!empty($userInput)) {
            $query->where(function ($q) use ($userInput) {
                $q->whereRaw('LOWER(certificate_number) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(certificate_scope) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereHas('client', function ($clientQuery) use ($userInput) {
                        $clientQuery->whereRaw('LOWER(client_name) LIKE ?', ['%' . strtolower($userInput) . '%']);
                    })
                    ->orWhereHas('standard', function ($standardQuery) use ($userInput) {
                        $standardQuery->whereRaw('LOWER(standard_name) LIKE ?', ['%' . strtolower($userInput) . '%']);
                    });
            });
        }

        $result = $query->orderBy('deleted_at', 'DESC')->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    public function liveSearchPending(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        $perPage = 100;
        $userInput = $request->input('userInput', '');
        $userId = Auth::user()->id;
        $userName = Auth::user()->name;

        $query = BaCertificate::with(['client', 'standard', 'accreditationBody'])
            ->where(function ($query) use ($userId, $userName) {
                $query->where(function ($q) use ($userId, $userName) {
                    $q->where('status', 'Pending Review')
                        ->where(function ($subQuery) use ($userId, $userName) {
                            $subQuery->where('review_by_id', $userId)
                                ->orWhere('review_by', $userName);
                        });
                })
                ->orWhere(function ($q) use ($userId, $userName) {
                    $q->where('status', 'Pending Approval')
                        ->where(function ($subQuery) use ($userId, $userName) {
                            $subQuery->where('approval_by_id', $userId)
                                ->orWhere('approval_by', $userName);
                        });
                });
            });

        if (!empty($userInput)) {
            $query->where(function ($q) use ($userInput) {
                $q->whereRaw('LOWER(certificate_number) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereRaw('LOWER(certificate_scope) LIKE ?', ['%' . strtolower($userInput) . '%'])
                    ->orWhereHas('client', function ($clientQuery) use ($userInput) {
                        $clientQuery->whereRaw('LOWER(client_name) LIKE ?', ['%' . strtolower($userInput) . '%']);
                    })
                    ->orWhereHas('standard', function ($standardQuery) use ($userInput) {
                        $standardQuery->whereRaw('LOWER(standard_name) LIKE ?', ['%' . strtolower($userInput) . '%']);
                    });
            });
        }

        $result = $query->orderBy('created_at', 'DESC')->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    /*
    |--------------------------------------------------------------------------
    | Import / Export Placeholder
    |--------------------------------------------------------------------------
    | BA import/export will be added after you finalize the import/export columns.
    |--------------------------------------------------------------------------
    */

    public function importExportView()
    {
        if (!Auth::check()) {
            return redirect()->route('certificate.search');
        }

        return view('imports-exports');
    }

    public function export()
    {
        if (Auth::check()) {
            $today = Carbon::now()->format('d-m-Y');
            $fileName = 'TUV Austria BIC BA Certificate DB on ' . $today . '.xlsx';

            return Excel::download(new CertificateExport, $fileName);
        }

        return redirect()->route('certificate.search');
    }

    public function import()
    {
        if (Auth::check()) {
            Excel::import(new CertificateImport, request()->file('file'));
            return redirect('/dashboard')->with('success', 'BA certificate data imported successfully.');
        }

        return redirect()->route('certificate.search');
    }
    /*
    |--------------------------------------------------------------------------
    | Date Calculation Helper
    |--------------------------------------------------------------------------
    */

    private function calculateCycleDates($initialAuditCompletionDate, $certificateExpiryDate)
    {
        $surveillance1 = null;
        $surveillance2 = null;
        $recertification = null;
        $gracePeriodEnd = null;

        if ($initialAuditCompletionDate) {
            $baseDate = Carbon::parse($initialAuditCompletionDate);

            $surveillance1 = $baseDate->copy()->addMonths(12)->format('Y-m-d');
            $surveillance2 = $baseDate->copy()->addMonths(24)->format('Y-m-d');
            $recertification = $baseDate->copy()->addMonths(36)->format('Y-m-d');
        }

        if ($certificateExpiryDate) {
            $gracePeriodEnd = Carbon::parse($certificateExpiryDate)->addMonths(6)->format('Y-m-d');
        }

        return [
            'surveillance_1_due_date' => $surveillance1,
            'surveillance_2_due_date' => $surveillance2,
            'recertification_due_date' => $recertification,
            'grace_period_end_date' => $gracePeriodEnd,
        ];
    }
}