@extends('layouts.admin')

@section('title', 'View Certificate')

@section('content')
<div class="page-heading">
    <div>
        <h1>Certificate Details</h1>
        <p>Review certification record, PDF, and audit reports.</p>
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-body">
        
<div class="row">
                <div class="col-md-8">
                    <div class="info-box">
                        <h5>Certificate Information</h5>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Client</div>
                            <div class="col-md-9">{{ $certificate->client->client_name ?? 'N/A' }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Standard</div>
                            <div class="col-md-9">{{ $certificate->standard->standard_name ?? 'N/A' }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Accreditation Body</div>
                            <div class="col-md-9">
                                {{ $certificate->accreditationBody->short_name ?? $certificate->accreditationBody->accreditation_body_name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Certificate Number</div>
                            <div class="col-md-9">{{ $certificate->certificate_number ?? 'Not Issued' }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Certificate Scope</div>
                            <div class="col-md-9">{{ $certificate->certificate_scope ?? 'N/A' }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Certification Cycle</div>
                            <div class="col-md-9">{{ $certificate->certification_cycle ?? 'N/A' }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Certificate Status</div>
                            <div class="col-md-9">
                                <span class="badge bg-info text-dark">
                                    {{ $certificate->certificate_status ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Audit Status</div>
                            <div class="col-md-9">
                                <span class="badge bg-secondary">
                                    {{ $certificate->audit_status ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Workflow Status</div>
                            <div class="col-md-9">
                                @if($certificate->status == 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($certificate->status == 'Pending Approval')
                                    <span class="badge bg-warning text-dark">Pending Approval</span>
                                @elseif($certificate->status == 'Pending Review')
                                    <span class="badge bg-secondary">Pending Review</span>
                                @else
                                    <span class="badge bg-light text-dark">{{ $certificate->status ?? 'N/A' }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-3 info-label">Remarks</div>
                            <div class="col-md-9">{{ $certificate->remarks ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <h5>QR Code / PDF</h5>

                        @if($certificate->certificate_number)
                            @php
                                $url = url('');
                                $verification_url = $url . '?search=' . $certificate->certificate_number;
                            @endphp

                            <center>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ $verification_url }}">
                                <br>
                                <small>{{ $certificate->certificate_number }}</small>
                            </center>
                        @else
                            <div class="alert alert-warning">
                                Certificate number not issued yet. QR code will be available after certificate number is added.
                            </div>
                        @endif

                        <hr>

                        @if($certificate->certificate_pdf)
                            <a href="{{ route('certificate.viewPdf', $certificate->id) }}" target="_blank" class="btn btn-danger w-100">
                                <i class="fa-solid fa-file-pdf me-1"></i> View Certificate PDF
                            </a>

                            <a href="{{ route('certificate.downloadPdf', $certificate->id) }}" class="btn btn-secondary w-100">
                                <i class="fa-solid fa-download me-1"></i> Download Certificate PDF
                            </a>

                            <small>
                                Uploaded By: {{ $certificate->pdf_uploaded_by ?? 'N/A' }}<br>
                                Uploaded At:
                                {{ $certificate->pdf_uploaded_at ? \Carbon\Carbon::parse($certificate->pdf_uploaded_at)->format('d-m-Y h:i A') : 'N/A' }}
                            </small>
                        @else
                            <div class="alert alert-warning">
                                No certificate PDF uploaded yet.
                            </div>
                        @endif

                        @canMutate
                        <form method="POST" action="{{ route('certificate.uploadPdf', $certificate->id) }}" enctype="multipart/form-data">
                            @csrf

                            <label class="mt-2">Upload / Replace Certificate PDF</label>
                            <input type="file" name="certificate_pdf" class="form-control" accept="application/pdf" required>

                            <button type="submit" class="btn btn-success w-100 mt-2">
                                <i class="fa-solid fa-upload me-1"></i> Upload PDF
                            </button>
                        </form>
                        @endcanMutate
                    </div>
                </div>
            </div>

            <div class="section-title">Certificate Dates and Audit Cycle</div>

            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-label">Issue Date</span><br>
                        {{ $certificate->certificate_issue_date ? \Carbon\Carbon::parse($certificate->certificate_issue_date)->format('d-m-Y') : 'N/A' }}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-label">Expiry Date</span><br>
                        {{ $certificate->certificate_expiry_date ? \Carbon\Carbon::parse($certificate->certificate_expiry_date)->format('d-m-Y') : 'N/A' }}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-label">Initial Audit Completion</span><br>
                        {{ $certificate->initial_certification_audit_completion_date ? \Carbon\Carbon::parse($certificate->initial_certification_audit_completion_date)->format('d-m-Y') : 'N/A' }}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-label">Grace Period End</span><br>
                        {{ $certificate->grace_period_end_date ? \Carbon\Carbon::parse($certificate->grace_period_end_date)->format('d-m-Y') : 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-label">Surveillance 1 Due</span><br>
                        {{ $certificate->surveillance_1_due_date ? \Carbon\Carbon::parse($certificate->surveillance_1_due_date)->format('d-m-Y') : 'N/A' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-label">Surveillance 2 Due</span><br>
                        {{ $certificate->surveillance_2_due_date ? \Carbon\Carbon::parse($certificate->surveillance_2_due_date)->format('d-m-Y') : 'N/A' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-label">Recertification Due</span><br>
                        {{ $certificate->recertification_due_date ? \Carbon\Carbon::parse($certificate->recertification_due_date)->format('d-m-Y') : 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="section-title">Audit Team</div>

            <div class="row">
                <div class="col-md-4"><div class="info-box"><b>Lead Auditor:</b><br>{{ $certificate->lead_auditor ?? 'N/A' }}</div></div>
                <div class="col-md-4"><div class="info-box"><b>Auditor 1:</b><br>{{ $certificate->auditor_1 ?? 'N/A' }}</div></div>
                <div class="col-md-4"><div class="info-box"><b>Auditor 2:</b><br>{{ $certificate->auditor_2 ?? 'N/A' }}</div></div>
                <div class="col-md-4"><div class="info-box"><b>Auditor 3:</b><br>{{ $certificate->auditor_3 ?? 'N/A' }}</div></div>
                <div class="col-md-4"><div class="info-box"><b>Technical Expert:</b><br>{{ $certificate->technical_expert ?? 'N/A' }}</div></div>
            </div>

            <div class="section-title">Review / Approval Information</div>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-box">
                        <b>Reviewer:</b> {{ $certificate->review_by ?? 'N/A' }}<br>
                        <b>Reviewed At:</b>
                        {{ $certificate->reviewed_at ? \Carbon\Carbon::parse($certificate->reviewed_at)->format('d-m-Y h:i A') : 'N/A' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-box">
                        <b>Approver:</b> {{ $certificate->approval_by ?? 'N/A' }}<br>
                        <b>Approved At:</b>
                        {{ $certificate->approved_at ? \Carbon\Carbon::parse($certificate->approved_at)->format('d-m-Y h:i A') : 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="section-title">Audit Reports</div>

            @canMutate
            <form method="POST" action="{{ route('auditReport.upload', $certificate->id) }}" enctype="multipart/form-data" class="mb-3">
                @csrf

                <div class="row">
                    <div class="col-md-2">
                        <label>Audit Year</label>
                        <input type="text" name="audit_year" class="form-control" placeholder="2026" required>
                    </div>

                    <div class="col-md-3">
                        <label>Audit Type</label>
                        <select name="audit_type" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Initial Certification">Initial Certification</option>
                            <option value="Surveillance 1">Surveillance 1</option>
                            <option value="Surveillance 2">Surveillance 2</option>
                            <option value="Recertification">Recertification</option>
                            <option value="Special Audit">Special Audit</option>
                            <option value="Transfer Audit">Transfer Audit</option>
                            <option value="Follow-up Audit">Follow-up Audit</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Audit Date</label>
                        <input type="date" name="audit_date" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Audit Report PDF</label>
                        <input type="file" name="audit_report_file" class="form-control" accept="application/pdf" required>
                    </div>

                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-upload me-1"></i> Upload
                        </button>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <input type="text" name="remarks" class="form-control" placeholder="Remarks for audit report upload, if any">
                    </div>
                </div>
            </form>
            @endcanMutate

            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Audit Year</th>
                            <th>Audit Type</th>
                            <th>Audit Date</th>
                            <th>Uploaded By</th>
                            <th>Uploaded At</th>
                            <th>Remarks</th>
                            <th>Report</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($certificate->auditReports as $report)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $report->audit_year ?? 'N/A' }}</td>
                                <td>{{ $report->audit_type ?? 'N/A' }}</td>
                                <td>{{ $report->audit_date ? \Carbon\Carbon::parse($report->audit_date)->format('d-m-Y') : 'N/A' }}</td>
                                <td>{{ $report->uploaded_by ?? 'N/A' }}</td>
                                <td>{{ $report->uploaded_at ? \Carbon\Carbon::parse($report->uploaded_at)->format('d-m-Y h:i A') : 'N/A' }}</td>
                                <td>{{ $report->remarks ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('auditReport.view', $report->id) }}" target="_blank">
                                        <i class="fa-solid fa-file-pdf text-danger" title="View Audit Report"></i>
                                    </a>
                                    &nbsp;
                                    <a href="{{ route('auditReport.download', $report->id) }}">
                                        <i class="fa-solid fa-download" title="Download Audit Report"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No audit reports uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="section-title">Record Tracking</div>

            <div class="row">
                <div class="col-md-3"><div class="info-box"><b>Created By:</b><br>{{ $certificate->created_by ?? 'N/A' }}</div></div>
                <div class="col-md-3"><div class="info-box"><b>Created At:</b><br>{{ $certificate->created_at ? \Carbon\Carbon::parse($certificate->created_at)->format('d-m-Y h:i A') : 'N/A' }}</div></div>
                <div class="col-md-3"><div class="info-box"><b>Updated By:</b><br>{{ $certificate->updated_by ?? 'N/A' }}</div></div>
                <div class="col-md-3"><div class="info-box"><b>Updated At:</b><br>{{ $certificate->updated_at ? \Carbon\Carbon::parse($certificate->updated_at)->format('d-m-Y h:i A') : 'N/A' }}</div></div>
            </div>

            @canMutate
            <div class="row mt-3">
                <div class="col-md-3">
                    @if(Auth::id() == $certificate->review_by_id && $certificate->status == 'Pending Review')
                        <form action="{{ route('certificate.review', $certificate->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info w-100" data-confirm="Mark this certificate as reviewed?">
                                <i class="fa-solid fa-thumbs-up me-1"></i> Mark as Reviewed
                            </button>
                        </form>
                    @endif
                </div>

                <div class="col-md-3">
                    @if(Auth::id() == $certificate->approval_by_id && $certificate->status == 'Pending Approval')
                        <form action="{{ route('certificate.approve', $certificate->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" data-confirm="Mark this certificate as approved?">
                                <i class="fa-solid fa-check me-1"></i> Mark as Approved
                            </button>
                        </form>
                    @endif
                </div>

                <div class="col-md-3">
                    <a href="{{ route('certificate.edit', $certificate->id) }}" class="btn btn-warning w-100">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Certificate
                    </a>
                </div>

                <div class="col-md-3">
                    <form action="{{ route('certificate.delete', $certificate->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" data-confirm="Delete this certificate record?">
                            <i class="fa-solid fa-trash me-1"></i> Delete Certificate
                        </button>
                    </form>
                </div>
            </div>
            @endcanMutate

    </div>
</section>
@endsection

