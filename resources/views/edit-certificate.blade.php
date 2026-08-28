@extends('layouts.admin')

@section('title', 'Edit Certificate')

@section('content')
<div class="page-heading">
    <div>
        <h1>Edit BA Certificate</h1>
        <p>Update certification record details.</p>
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-body">
        
<div class="info-box mb-3">
                <b>Record ID:</b> {{ $certificate->id }} |
                <b>Current Workflow Status:</b> {{ $certificate->status ?? 'N/A' }} |
                <b>Created By:</b> {{ $certificate->created_by ?? 'N/A' }} |
                <b>Created At:</b>
                {{ $certificate->created_at ? \Carbon\Carbon::parse($certificate->created_at)->format('d-m-Y h:i A') : 'N/A' }}

                @if($certificate->updated_by)
                    <br>
                    <b>Last Updated By:</b> {{ $certificate->updated_by }} |
                    <b>Updated At:</b>
                    {{ $certificate->updated_at ? \Carbon\Carbon::parse($certificate->updated_at)->format('d-m-Y h:i A') : 'N/A' }}
                @endif
            </div>

            <div class="alert alert-warning" style="font-size: 12px;">
                <b>Note:</b> Updating this certificate record will reset the workflow status to <b>Pending Review</b>, as currently configured in the controller.
            </div>

            <form method="POST" action="{{ route('certificate.update') }}">
                @csrf

                <input type="hidden" name="id" value="{{ $certificate->id }}">

                <div class="section-title">A. Client and Certification Information</div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="certification_client_id">Client <span class="text-danger">*</span></label>
                        <select name="certification_client_id" id="certification_client_id" class="form-select" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    @if(old('certification_client_id', $certificate->certification_client_id) == $client->id) selected @endif>
                                    {{ $client->client_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="certification_standard_id">Certification Standard</label>
                        <select name="certification_standard_id" id="certification_standard_id" class="form-select">
                            <option value="">-- Select Standard --</option>
                            @foreach($standards as $standard)
                                <option value="{{ $standard->id }}"
                                    @if(old('certification_standard_id', $certificate->certification_standard_id) == $standard->id) selected @endif>
                                    {{ $standard->standard_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="certification_accreditation_body_id">Accreditation Body</label>
                        <select name="certification_accreditation_body_id" id="certification_accreditation_body_id" class="form-select">
                            <option value="">-- Select Accreditation Body --</option>
                            @foreach($accreditationBodies as $body)
                                <option value="{{ $body->id }}"
                                    @if(old('certification_accreditation_body_id', $certificate->certification_accreditation_body_id) == $body->id) selected @endif>
                                    {{ $body->short_name ? $body->short_name . ' - ' : '' }}{{ $body->accreditation_body_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="certificate_number">Certificate Number</label>
                        <input type="text"
                               name="certificate_number"
                               id="certificate_number"
                               class="form-control"
                               value="{{ old('certificate_number', $certificate->certificate_number) }}"
                               placeholder="Leave blank if not issued yet">
                        <div class="help-note">Certificate number is nullable but must be unique once issued.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="certification_cycle">Certification Cycle</label>
                        <input type="text"
                               name="certification_cycle"
                               id="certification_cycle"
                               class="form-control"
                               value="{{ old('certification_cycle', $certificate->certification_cycle) }}"
                               placeholder="Initial / Recertification / Transfer etc.">
                    </div>

                    <div class="col-md-4">
                        <label for="certificate_status">Certificate Status</label>
                        <select name="certificate_status" id="certificate_status" class="form-select">
                            @php
                                $certificateStatuses = [
                                    'Active',
                                    'Upcoming Surveillance',
                                    'Surveillance Due',
                                    'Recertification Due',
                                    'Expired - Within Grace Period',
                                    'Expired - Grace Period Over',
                                    'Suspended',
                                    'Withdrawn',
                                    'Cancelled'
                                ];
                            @endphp

                            @foreach($certificateStatuses as $status)
                                <option value="{{ $status }}" @if(old('certificate_status', $certificate->certificate_status) == $status) selected @endif>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="certificate_scope">Certificate Scope</label>
                        <textarea name="certificate_scope"
                                  id="certificate_scope"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Enter certificate scope">{{ old('certificate_scope', $certificate->certificate_scope) }}</textarea>
                    </div>
                </div>

                <div class="section-title">B. Certificate and Audit Cycle Dates</div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="certificate_issue_date">Certificate Issue Date</label>
                        <input type="date"
                               name="certificate_issue_date"
                               id="certificate_issue_date"
                               class="form-control"
                               value="{{ old('certificate_issue_date', $certificate->certificate_issue_date) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="certificate_expiry_date">Certificate Expiry Date</label>
                        <input type="date"
                               name="certificate_expiry_date"
                               id="certificate_expiry_date"
                               class="form-control"
                               value="{{ old('certificate_expiry_date', $certificate->certificate_expiry_date) }}">
                        <div class="help-note">Grace period will be recalculated from this date.</div>
                    </div>

                    <div class="col-md-3">
                        <label for="initial_certification_audit_completion_date">Initial Audit Completion Date</label>
                        <input type="date"
                               name="initial_certification_audit_completion_date"
                               id="initial_certification_audit_completion_date"
                               class="form-control"
                               value="{{ old('initial_certification_audit_completion_date', $certificate->initial_certification_audit_completion_date) }}">
                        <div class="help-note">S1, S2 and recertification due dates will be recalculated.</div>
                    </div>

                    <div class="col-md-3">
                        <label for="audit_status">Audit Status</label>
                        <select name="audit_status" id="audit_status" class="form-select">
                            @php
                                $auditStatuses = [
                                    'Not Scheduled',
                                    'Scheduled',
                                    'Completed',
                                    'Report Pending',
                                    'Reviewed',
                                    'Approved',
                                    'Closed',
                                    'Overdue'
                                ];
                            @endphp

                            @foreach($auditStatuses as $status)
                                <option value="{{ $status }}" @if(old('audit_status', $certificate->audit_status) == $status) selected @endif>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="section-title">C. Current Calculated Due Dates</div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Surveillance 1 Due Date</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $certificate->surveillance_1_due_date ? \Carbon\Carbon::parse($certificate->surveillance_1_due_date)->format('d-m-Y') : 'N/A' }}"
                               readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Surveillance 2 Due Date</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $certificate->surveillance_2_due_date ? \Carbon\Carbon::parse($certificate->surveillance_2_due_date)->format('d-m-Y') : 'N/A' }}"
                               readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Recertification Due Date</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $certificate->recertification_due_date ? \Carbon\Carbon::parse($certificate->recertification_due_date)->format('d-m-Y') : 'N/A' }}"
                               readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Grace Period End Date</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $certificate->grace_period_end_date ? \Carbon\Carbon::parse($certificate->grace_period_end_date)->format('d-m-Y') : 'N/A' }}"
                               readonly>
                    </div>
                </div>

                <div class="section-title">D. Audit Team</div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="lead_auditor">Lead Auditor</label>
                        <input type="text"
                               name="lead_auditor"
                               id="lead_auditor"
                               class="form-control"
                               value="{{ old('lead_auditor', $certificate->lead_auditor) }}"
                               placeholder="Enter lead auditor name">
                    </div>

                    <div class="col-md-4">
                        <label for="auditor_1">Auditor 1</label>
                        <input type="text"
                               name="auditor_1"
                               id="auditor_1"
                               class="form-control"
                               value="{{ old('auditor_1', $certificate->auditor_1) }}"
                               placeholder="Optional">
                    </div>

                    <div class="col-md-4">
                        <label for="auditor_2">Auditor 2</label>
                        <input type="text"
                               name="auditor_2"
                               id="auditor_2"
                               class="form-control"
                               value="{{ old('auditor_2', $certificate->auditor_2) }}"
                               placeholder="Optional">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="auditor_3">Auditor 3</label>
                        <input type="text"
                               name="auditor_3"
                               id="auditor_3"
                               class="form-control"
                               value="{{ old('auditor_3', $certificate->auditor_3) }}"
                               placeholder="Optional">
                    </div>

                    <div class="col-md-4">
                        <label for="technical_expert">Technical Expert</label>
                        <input type="text"
                               name="technical_expert"
                               id="technical_expert"
                               class="form-control"
                               value="{{ old('technical_expert', $certificate->technical_expert) }}"
                               placeholder="Optional">
                    </div>

                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <div class="alert alert-info py-2 mb-0" style="font-size: 12px;">
                            Auditor fields are optional and may be updated later.
                        </div>
                    </div>
                </div>

                <div class="section-title">E. Review and Approval Workflow</div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="review_by">Reviewer <span class="text-danger">*</span></label>
                        <select name="review_by" id="review_by" class="form-select" required>
                            <option value="">-- Select Reviewer --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}"
                                    @if(old('review_by', $certificate->review_by) == $user->name) selected @endif>
                                    {{ $user->name }} {{ $user->designation ? '(' . $user->designation . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="approval_by">Approver <span class="text-danger">*</span></label>
                        <select name="approval_by" id="approval_by" class="form-select" required>
                            <option value="">-- Select Approver --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}"
                                    @if(old('approval_by', $certificate->approval_by) == $user->name) selected @endif>
                                    {{ $user->name }} {{ $user->designation ? '(' . $user->designation . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="section-title">F. Remarks</div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks"
                                  id="remarks"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Enter remarks, internal notes, or certification tracking comments">{{ old('remarks', $certificate->remarks) }}</textarea>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Certificate
                        </button>
                    </div>

                    <div class="col-md-4">
                        <a href="{{ route('certificate.view', $certificate->id) }}" class="btn btn-info w-100">
                            <i class="fa-solid fa-circle-info me-1"></i> Back to Certificate View
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary w-100">
                            <i class="fa-solid fa-house me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

            </form>
    </div>
</section>
@endsection

