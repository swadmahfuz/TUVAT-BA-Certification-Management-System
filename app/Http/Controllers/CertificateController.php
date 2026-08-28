<?php

namespace App\Http\Controllers;

use App\Exports\CertificateExport;
use App\Imports\CertificateImport;
use App\Models\CertificationAccreditationBody;
use App\Models\CertificationAuditReport;
use App\Models\CertificationCertificate;
use App\Models\CertificationClient;
use App\Models\CertificationStandard;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| TUVAT BA Certification Management System
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Head of Divison - Business Assurance & Training, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
| Latest Stable Release: v5.1.0 -  29 August 2026
|--------------------------------------------------------------------------
*/

class CertificateController extends Controller
{
    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
    }
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

        $certificates = CertificationCertificate::with(['client', 'standard', 'accreditationBody'])
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
        $email = $credentials['email'] ?? null;

        if ($email) {
            $existing = User::where('email', $email)->first();

            if ($existing && !$existing->isActive()) {
                return redirect('/admin')->with('error', 'Your account has been deactivated. Contact an administrator.');
            }
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            if ($user->mustChangePassword()) {
                return redirect()->route('account.password.edit')
                    ->with('warning', 'You must set a new password before continuing.');
            }

            return redirect('/dashboard')->with('success', 'Thank You for authorizing. Please proceed.');
        }

        return redirect('/admin')->with('error', 'You entered the wrong credentials');
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function getDashboard(DashboardService $dashboardService)
    {
        $certificates = CertificationCertificate::with(['client', 'standard', 'accreditationBody'])
            ->orderBy('created_at', 'DESC')
            ->paginate(100);

        return view('dashboard', array_merge(
            $dashboardService->data(),
            compact('certificates')
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    public function showAllUsers()
    {
        $users = User::with('departmentRelation')
            ->withCount([
                'certificationCertificatesCreated as certificates_created_count',
                'certificationCertificatesReviewed as certificates_reviewed_count',
                'certificationCertificatesApproved as certificates_approved_count',
            ])->orderBy('name')->get();

        return view('all-users', compact('users'));
    }

    /*
    |--------------------------------------------------------------------------
    | BA Clients
    |--------------------------------------------------------------------------
    */

    public function getAllClients(Request $request)
    {

        $query = CertificationClient::withCount('certificates')->orderBy('client_name');

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

        return view('add-client');
    }

    public function createClient(Request $request)
    {

        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $client = CertificationClient::create([
            'client_name' => $request->client_name,
            'client_address' => $request->client_address,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'remarks' => $request->remarks,
            'created_by' => Auth::user()->name,
            'created_by_id' => Auth::user()->id,
        ]);

        $this->activityLog->record(
            'client.created',
            'client',
            $client->id,
            'Client ' . $client->client_name . ' was created.'
        );

        return redirect('/view-client/' . $client->id)->with('success', 'Client added successfully.');
    }

    public function viewClient($id)
    {

        $client = CertificationClient::with([
            'certificates.standard',
            'certificates.accreditationBody',
            'certificates.auditReports'
        ])->withTrashed()->findOrFail($id);

        return view('view-client', compact('client'));
    }

    public function editClient($id)
    {

        $client = CertificationClient::findOrFail($id);

        return view('edit-client', compact('client'));
    }

    public function updateClient(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:certification_clients,id',
            'client_name' => 'required|string|max:255',
            'client_address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $client = CertificationClient::findOrFail($request->id);

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

        $this->activityLog->record(
            'client.updated',
            'client',
            $client->id,
            'Client ' . $client->client_name . ' was updated.'
        );

        return redirect('/view-client/' . $client->id)->with('success', 'Client updated successfully.');
    }

    public function deleteClient($id)
    {

        $client = CertificationClient::withCount('certificates')->findOrFail($id);

        if ($client->certificates_count > 0) {
            return back()->with('error', 'This client has certificate records. Please delete or transfer the certificate records first.');
        }

        $clientName = $client->client_name;

        $client->update([
            'deleted_by' => Auth::user()->name,
            'deleted_by_id' => Auth::user()->id,
        ]);

        $client->delete();

        $this->activityLog->record(
            'client.deleted',
            'client',
            $client->id,
            'Client ' . $clientName . ' was deleted.'
        );

        return redirect('/clients')->with('success', 'Client deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | BA Certificates
    |--------------------------------------------------------------------------
    */

    public function addCertificate($clientId = null)
    {

        $clients = CertificationClient::orderBy('client_name')->get();
        $standards = CertificationStandard::where('status', 'Active')->orderBy('standard_name')->get();
        $accreditationBodies = CertificationAccreditationBody::where('status', 'Active')->orderBy('accreditation_body_name')->get();
        $users = User::orderBy('name')->get();

        $selectedClient = $clientId ? CertificationClient::find($clientId) : null;

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

        $request->validate([
            'certification_client_id' => 'required|exists:certification_clients,id',
            'certification_standard_id' => 'nullable|exists:certification_standards,id',
            'certification_accreditation_body_id' => 'nullable|exists:certification_accreditation_bodies,id',

            'certificate_number' => 'nullable|string|max:255|unique:certification_certificates,certificate_number',
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

        $certificate = CertificationCertificate::create([
            'certification_client_id' => $request->certification_client_id,
            'certification_standard_id' => $request->certification_standard_id,
            'certification_accreditation_body_id' => $request->certification_accreditation_body_id,

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

        $this->activityLog->record(
            'certificate.created',
            'certificate',
            $certificate->id,
            'Certificate ' . ($certificate->certificate_number ?: '#' . $certificate->id) . ' was created for client ID ' . $certificate->certification_client_id . '.'
        );

        return redirect('/view-certificate/' . $certificate->id)->with('success', 'Certificate record added successfully.');
    }

    public function viewCertificate($id)
    {

        $certificate = CertificationCertificate::with([
            'client',
            'standard',
            'accreditationBody',
            'auditReports'
        ])->withTrashed()->findOrFail($id);

        return view('view-certificate', compact('certificate'));
    }

    public function editCertificate($id)
    {

        $certificate = CertificationCertificate::findOrFail($id);
        $clients = CertificationClient::orderBy('client_name')->get();
        $standards = CertificationStandard::where('status', 'Active')->orderBy('standard_name')->get();
        $accreditationBodies = CertificationAccreditationBody::where('status', 'Active')->orderBy('accreditation_body_name')->get();
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

        $request->validate([
            'id' => 'required|exists:certification_certificates,id',
            'certification_client_id' => 'required|exists:certification_clients,id',
            'certification_standard_id' => 'nullable|exists:certification_standards,id',
            'certification_accreditation_body_id' => 'nullable|exists:certification_accreditation_bodies,id',

            'certificate_number' => 'nullable|string|max:255|unique:certification_certificates,certificate_number,' . $request->id,
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

        $certificate = CertificationCertificate::findOrFail($request->id);

        $reviewer = User::where('name', $request->review_by)->first();
        $approver = User::where('name', $request->approval_by)->first();

        $cycleDates = $this->calculateCycleDates(
            $request->initial_certification_audit_completion_date,
            $request->certificate_expiry_date
        );

        $certificate->update([
            'certification_client_id' => $request->certification_client_id,
            'certification_standard_id' => $request->certification_standard_id,
            'certification_accreditation_body_id' => $request->certification_accreditation_body_id,

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

        $this->activityLog->record(
            'certificate.updated',
            'certificate',
            $certificate->id,
            'Certificate ' . ($certificate->certificate_number ?: '#' . $certificate->id) . ' was updated and returned for review.',
            ['status' => $certificate->status]
        );

        return redirect('/view-certificate/' . $certificate->id)->with('success', 'Certificate record updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Review and Approval
    |--------------------------------------------------------------------------
    */

    public function getPendingCertificates(Request $request)
    {
        $assignment = $request->query('assignment');
        $query = $this->pendingCertificatesQuery($assignment);

        $certificates = $query
            ->with(['client', 'standard', 'accreditationBody'])
            ->orderBy('created_at', 'DESC')
            ->paginate(100)
            ->withQueryString();

        return view('pending-certificates', compact('certificates', 'assignment'));
    }

    public function reviewCertificate($id)
    {
        $certificate = CertificationCertificate::findOrFail($id);

        if (Auth::id() != $certificate->review_by_id) {
            return back()->with('error', 'Unauthorized: You are not assigned to review this certificate.');
        }

        $certificate->update([
            'status' => 'Pending Approval',
            'reviewed_at' => Carbon::now(),
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::id(),
        ]);

        $this->activityLog->record(
            'certificate.reviewed',
            'certificate',
            $certificate->id,
            'Certificate ' . ($certificate->certificate_number ?: '#' . $certificate->id) . ' was reviewed.'
        );

        return redirect('/view-certificate/' . $certificate->id)->with('success', 'Certificate marked as reviewed.');
    }

    public function approveCertificate($id)
    {
        $certificate = CertificationCertificate::findOrFail($id);

        if (Auth::id() != $certificate->approval_by_id) {
            return back()->with('error', 'Unauthorized: You are not assigned to approve this certificate.');
        }

        if ($certificate->status !== 'Pending Approval') {
            return back()->with('error', 'Certificate must be reviewed before approval.');
        }

        $certificate->update([
            'status' => 'Approved',
            'approved_at' => Carbon::now(),
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::id(),
        ]);

        $this->activityLog->record(
            'certificate.approved',
            'certificate',
            $certificate->id,
            'Certificate ' . ($certificate->certificate_number ?: '#' . $certificate->id) . ' was approved.'
        );

        return back()->with('success', 'Certificate approved successfully.');
    }

    public function bulkReview()
    {
        $user = Auth::user();

        $updated = CertificationCertificate::where('status', 'Pending Review')
            ->where('review_by_id', $user->id)
            ->update([
                'status' => 'Pending Approval',
                'reviewed_at' => Carbon::now(),
                'updated_by' => $user->name,
                'updated_by_id' => $user->id,
                'updated_at' => Carbon::now(),
            ]);

        $this->activityLog->record(
            'certificate.bulk_reviewed',
            'certificate',
            null,
            $updated . ' certificate(s) were bulk reviewed.',
            ['count' => $updated]
        );

        return back()->with('success', $updated . ' certificate(s) marked as Reviewed.');
    }

    public function bulkApprove()
    {
        $user = Auth::user();

        $updated = CertificationCertificate::where('status', 'Pending Approval')
            ->where('approval_by_id', $user->id)
            ->update([
                'status' => 'Approved',
                'approved_at' => Carbon::now(),
                'updated_by' => $user->name,
                'updated_by_id' => $user->id,
                'updated_at' => Carbon::now(),
            ]);

        $this->activityLog->record(
            'certificate.bulk_approved',
            'certificate',
            null,
            $updated . ' certificate(s) were bulk approved.',
            ['count' => $updated]
        );

        return back()->with('success', $updated . ' certificate(s) marked as Approved.');
    }

    public function bulkReviewSelected(Request $request)
    {
        $ids = $this->validatedSelectedCertificateIds($request);
        $user = Auth::user();

        $eligibleIds = CertificationCertificate::whereIn('id', $ids)
            ->assignedForReview($user->id)
            ->pluck('id')
            ->all();

        $updated = CertificationCertificate::whereIn('id', $eligibleIds)->update([
            'status' => 'Pending Approval',
            'reviewed_at' => Carbon::now(),
            'updated_by' => $user->name,
            'updated_by_id' => $user->id,
            'updated_at' => Carbon::now(),
        ]);
        $skipped = count($ids) - $updated;

        $this->activityLog->record(
            'certificate.selected_bulk_reviewed',
            'certificate',
            null,
            $updated . ' selected certificate(s) were bulk reviewed.',
            [
                'selected_ids' => $ids,
                'updated_ids' => $eligibleIds,
                'updated_count' => $updated,
                'skipped_count' => $skipped,
            ]
        );

        return back()
            ->with('success', $updated . ' certificate(s) reviewed; ' . $skipped . ' skipped.')
            ->with('bulk_action_completed', true);
    }

    public function bulkApproveSelected(Request $request)
    {
        $ids = $this->validatedSelectedCertificateIds($request);
        $user = Auth::user();

        $eligibleIds = CertificationCertificate::whereIn('id', $ids)
            ->assignedForApproval($user->id)
            ->pluck('id')
            ->all();

        $updated = CertificationCertificate::whereIn('id', $eligibleIds)->update([
            'status' => 'Approved',
            'approved_at' => Carbon::now(),
            'updated_by' => $user->name,
            'updated_by_id' => $user->id,
            'updated_at' => Carbon::now(),
        ]);
        $skipped = count($ids) - $updated;

        $this->activityLog->record(
            'certificate.selected_bulk_approved',
            'certificate',
            null,
            $updated . ' selected certificate(s) were bulk approved.',
            [
                'selected_ids' => $ids,
                'updated_ids' => $eligibleIds,
                'updated_count' => $updated,
                'skipped_count' => $skipped,
            ]
        );

        return back()
            ->with('success', $updated . ' certificate(s) approved; ' . $skipped . ' skipped.')
            ->with('bulk_action_completed', true);
    }

    public function bulkDeleteSelected(Request $request)
    {
        $ids = $this->validatedSelectedCertificateIds($request);
        $user = Auth::user();

        $deletedIds = DB::transaction(function () use ($ids, $user) {
            $certificates = CertificationCertificate::whereIn('id', $ids)->lockForUpdate()->get();
            $deletedIds = [];

            foreach ($certificates as $certificate) {
                $this->softDeleteCertificate($certificate, $user);
                $deletedIds[] = $certificate->id;
            }

            return $deletedIds;
        });
        $skipped = count($ids) - count($deletedIds);

        $this->activityLog->record(
            'certificate.selected_bulk_deleted',
            'certificate',
            null,
            count($deletedIds) . ' selected certificate(s) were deleted.',
            [
                'selected_ids' => $ids,
                'deleted_ids' => $deletedIds,
                'deleted_count' => count($deletedIds),
                'skipped_count' => $skipped,
            ]
        );

        return back()
            ->with('success', count($deletedIds) . ' certificate(s) deleted; ' . $skipped . ' skipped.')
            ->with('bulk_action_completed', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Audit Tracking Pages
    |--------------------------------------------------------------------------
    */

    public function upcomingAudits()
    {

        $today = Carbon::today();
        $next90Days = Carbon::today()->addDays(90);

        $certificates = CertificationCertificate::with(['client', 'standard', 'accreditationBody'])
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

        $today = Carbon::today();

        $certificates = CertificationCertificate::with(['client', 'standard', 'accreditationBody'])
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
        $certificate = CertificationCertificate::findOrFail($id);
        $this->softDeleteCertificate($certificate, Auth::user());

        $this->activityLog->record(
            'certificate.deleted',
            'certificate',
            $certificate->id,
            'Certificate ' . ($certificate->certificate_number ?: '#' . $certificate->id) . ' was deleted.'
        );

        return back()->with('success', 'Certificate record deleted successfully.');
    }

    public function getDeletedCertificates()
    {

        $certificates = CertificationCertificate::onlyTrashed()
            ->with(['client', 'standard', 'accreditationBody'])
            ->orderBy('deleted_at', 'DESC')
            ->paginate(100);

        $clients = CertificationClient::onlyTrashed()
            ->orderBy('deleted_at', 'DESC')
            ->paginate(100);

        return view('deleted-certificates', compact('certificates', 'clients'));
    }

    public function restoreCertificate($id)
    {

        $certificate = CertificationCertificate::onlyTrashed()->findOrFail($id);

        $certificate->restore();

        $certificate->update([
            'status' => 'Pending Review',
            'deleted_by' => null,
            'deleted_by_id' => null,
            'updated_by' => Auth::user()->name,
            'updated_by_id' => Auth::id(),
        ]);

        $this->activityLog->record(
            'certificate.restored',
            'certificate',
            $certificate->id,
            'Certificate ' . ($certificate->certificate_number ?: '#' . $certificate->id) . ' was restored.'
        );

        return back()->with('success', 'Certificate restored successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Certificate PDF Upload / Download / View
    |--------------------------------------------------------------------------
    */

    public function uploadPdf(Request $request, $id)
    {

        $request->validate([
            'certificate_pdf' => 'required|mimes:pdf|max:20480',
        ]);

        $certificate = CertificationCertificate::with('client')->findOrFail($id);

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

        $this->activityLog->record(
            'certificate.pdf_uploaded',
            'certificate',
            $certificate->id,
            'Certificate PDF was uploaded for ' . ($certificate->certificate_number ?: '#' . $certificate->id) . '.'
        );

        return back()->with('success', 'Certificate PDF uploaded successfully.');
    }

    public function downloadPdf($id)
    {

        $certificate = CertificationCertificate::findOrFail($id);

        $filePath = public_path('BA Certificate PDFs/' . $certificate->certificate_pdf);

        if (!$certificate->certificate_pdf || !file_exists($filePath)) {
            return back()->with('error', 'PDF file not found.');
        }

        return response()->download($filePath, $certificate->certificate_pdf);
    }

    public function viewPdf($id)
    {

        $certificate = CertificationCertificate::findOrFail($id);

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

        $certificate = CertificationCertificate::findOrFail($certificateId);

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

        CertificationAuditReport::create([
            'certification_certificate_id' => $certificate->id,
            'audit_year' => $request->audit_year,
            'audit_type' => $request->audit_type,
            'audit_date' => $request->audit_date,
            'audit_report_file' => $fileName,
            'uploaded_by' => Auth::user()->name,
            'uploaded_by_id' => Auth::user()->id,
            'uploaded_at' => Carbon::now(),
            'remarks' => $request->remarks,
        ]);

        $this->activityLog->record(
            'audit_report.uploaded',
            'certificate',
            $certificate->id,
            'Audit report (' . $request->audit_year . ') uploaded for certificate #' . $certificate->id . '.'
        );

        return back()->with('success', 'Audit report uploaded successfully.');
    }

    public function downloadAuditReport($id)
    {

        $report = CertificationAuditReport::findOrFail($id);

        $filePath = public_path('BA Audit Reports/' . $report->audit_report_file);

        if (!$report->audit_report_file || !file_exists($filePath)) {
            return back()->with('error', 'Audit report file not found.');
        }

        return response()->download($filePath, $report->audit_report_file);
    }

    public function viewAuditReport($id)
    {

        $report = CertificationAuditReport::findOrFail($id);

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

        $standards = CertificationStandard::orderBy('standard_name')->get();

        return view('manage-standards', compact('standards'));
    }

    public function createStandard(Request $request)
    {

        $request->validate([
            'standard_name' => 'required|string|max:255|unique:certification_standards,standard_name',
            'standard_code' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        CertificationStandard::create([
            'standard_name' => $request->standard_name,
            'standard_code' => $request->standard_code,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Standard added successfully.');
    }

    public function updateStandard(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:certification_standards,id',
            'standard_name' => 'required|string|max:255|unique:certification_standards,standard_name,' . $request->id,
            'standard_code' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        $standard = CertificationStandard::findOrFail($request->id);

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

        $accreditationBodies = CertificationAccreditationBody::orderBy('accreditation_body_name')->get();

        return view('manage-accreditation-bodies', compact('accreditationBodies'));
    }

    public function createAccreditationBody(Request $request)
    {

        $request->validate([
            'accreditation_body_name' => 'required|string|max:255|unique:certification_accreditation_bodies,accreditation_body_name',
            'short_name' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        CertificationAccreditationBody::create([
            'accreditation_body_name' => $request->accreditation_body_name,
            'short_name' => $request->short_name,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Accreditation body added successfully.');
    }

    public function updateAccreditationBody(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:certification_accreditation_bodies,id',
            'accreditation_body_name' => 'required|string|max:255|unique:certification_accreditation_bodies,accreditation_body_name,' . $request->id,
            'short_name' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        $body = CertificationAccreditationBody::findOrFail($request->id);

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

        $perPage = 100;
        $userInput = $request->input('userInput', '');

        $query = CertificationCertificate::with(['client', 'standard', 'accreditationBody']);

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

        $perPage = 100;
        $userInput = $request->input('userInput', '');

        $query = CertificationCertificate::onlyTrashed()->with(['client', 'standard', 'accreditationBody']);

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
        $perPage = 100;
        $userInput = $request->input('userInput', '');
        $assignment = $request->input('assignment');

        $query = $this->pendingCertificatesQuery($assignment)
            ->with(['client', 'standard', 'accreditationBody']);

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

        return view('imports-exports');
    }

    public function export()
    {
        $today = Carbon::now()->format('d-m-Y');
        $fileName = 'TUV Austria BIC BA Certificate DB on ' . $today . '.xlsx';

        $this->activityLog->record(
            'export.completed',
            'export',
            null,
            'BA certificate data was exported.',
            ['file_name' => $fileName]
        );

        return Excel::download(new CertificateExport, $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        Excel::import(new CertificateImport, $request->file('file'));

        $this->activityLog->record(
            'import.completed',
            'import',
            null,
            'BA certificate data was imported.',
            ['file_name' => $request->file('file')->getClientOriginalName()]
        );

        return redirect('/dashboard')->with('success', 'BA certificate data imported successfully.');
    }
    /*
    |--------------------------------------------------------------------------
    | Date Calculation Helper
    |--------------------------------------------------------------------------
    */

    private function validatedSelectedCertificateIds(Request $request): array
    {
        $validated = $request->validate([
            'certificate_ids' => 'required|array|min:1|max:500',
            'certificate_ids.*' => 'required|integer|distinct|exists:certification_certificates,id',
        ]);

        return array_map('intval', $validated['certificate_ids']);
    }

    private function softDeleteCertificate(CertificationCertificate $certificate, User $user): void
    {
        if ($certificate->certificate_number) {
            $certificate->certificate_number .= ' (Deleted)';
        }

        $certificate->status = 'Deleted';
        $certificate->deleted_by = $user->name;
        $certificate->deleted_by_id = $user->id;
        $certificate->reviewed_at = null;
        $certificate->approved_at = null;
        $certificate->updated_by = $user->name;
        $certificate->updated_by_id = $user->id;
        $certificate->updated_at = Carbon::now();
        $certificate->save();
        $certificate->delete();
    }

    private function pendingCertificatesQuery(?string $assignment)
    {
        $userId = Auth::id();

        if ($assignment === 'review' && $userId) {
            return CertificationCertificate::assignedForReview($userId);
        }

        if ($assignment === 'approval' && $userId) {
            return CertificationCertificate::assignedForApproval($userId);
        }

        if ($assignment === 'mine' && $userId) {
            return CertificationCertificate::assignedToUser($userId);
        }

        return CertificationCertificate::where(function ($query) {
            $query->whereIn('status', ['Pending Review', 'Pending'])
                ->orWhereIn('status', ['Pending Approval', 'Reviewed']);
        });
    }

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