@extends('layouts.admin')

@section('title', 'Add Certificate')

@section('content')
<div class="page-heading">
    <div>
        <h1>Add BA Certificate</h1>
        <p>Create a new certification record for a client.</p>
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-body">
        
<form method="POST" action="{{ route('certificate.create') }}">
                @csrf

                <div class="section-title">A. Client and Certification Information</div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="certification_client_id">Client <span class="text-danger">*</span></label>
                        <select name="certification_client_id" id="certification_client_id" class="form-select" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    @if(old('certification_client_id', $selectedClient->id ?? '') == $client->id) selected @endif>
                                    {{ $client->client_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="help-note">If client is missing, add client first.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="certification_standard_id">Certification Standard</label>
                        <select name="certification_standard_id" id="certification_standard_id" class="form-select">
                            <option value="">-- Select Standard --</option>
                            @foreach($standards as $standard)
                                <option value="{{ $standard->id }}"
                                    @if(old('certification_standard_id') == $standard->id) selected @endif>
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
                                    @if(old('certification_accreditation_body_id') == $body->id) selected @endif>
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
                               value="{{ old('certificate_number') }}"
                               placeholder="Leave blank if not issued yet">
                        <div class="help-note">Certificate number is nullable but must be unique once issued.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="certification_cycle">Certification Cycle</label>
                        <input type="text"
                               name="certification_cycle"
                               id="certification_cycle"
                               class="form-control"
                               value="{{ old('certification_cycle') }}"
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
                                <option value="{{ $status }}" @if(old('certificate_status', 'Active') == $status) selected @endif>
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
                                  placeholder="Enter certificate scope">{{ old('certificate_scope') }}</textarea>
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
                               value="{{ old('certificate_issue_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="certificate_expiry_date">Certificate Expiry Date</label>
                        <input type="date"
                               name="certificate_expiry_date"
                               id="certificate_expiry_date"
                               class="form-control"
                               value="{{ old('certificate_expiry_date') }}">
                        <div class="help-note">Grace period will be calculated from this date.</div>
                    </div>

                    <div class="col-md-3">
                        <label for="initial_certification_audit_completion_date">Initial Audit Completion Date</label>
                        <input type="date"
                               name="initial_certification_audit_completion_date"
                               id="initial_certification_audit_completion_date"
                               class="form-control"
                               value="{{ old('initial_certification_audit_completion_date') }}">
                        <div class="help-note">S1, S2 and recertification due dates will be calculated from this date.</div>
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
                                <option value="{{ $status }}" @if(old('audit_status', 'Not Scheduled') == $status) selected @endif>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="section-title">C. Audit Team</div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="lead_auditor">Lead Auditor</label>
                        <input type="text"
                               name="lead_auditor"
                               id="lead_auditor"
                               class="form-control"
                               value="{{ old('lead_auditor') }}"
                               placeholder="Enter lead auditor name">
                    </div>

                    <div class="col-md-4">
                        <label for="auditor_1">Auditor 1</label>
                        <input type="text"
                               name="auditor_1"
                               id="auditor_1"
                               class="form-control"
                               value="{{ old('auditor_1') }}"
                               placeholder="Optional">
                    </div>

                    <div class="col-md-4">
                        <label for="auditor_2">Auditor 2</label>
                        <input type="text"
                               name="auditor_2"
                               id="auditor_2"
                               class="form-control"
                               value="{{ old('auditor_2') }}"
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
                               value="{{ old('auditor_3') }}"
                               placeholder="Optional">
                    </div>

                    <div class="col-md-4">
                        <label for="technical_expert">Technical Expert</label>
                        <input type="text"
                               name="technical_expert"
                               id="technical_expert"
                               class="form-control"
                               value="{{ old('technical_expert') }}"
                               placeholder="Optional">
                    </div>

                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <div class="alert alert-info py-2 mb-0" style="font-size: 12px;">
                            Auditor fields are optional. You can update them later.
                        </div>
                    </div>
                </div>

                <div class="section-title">D. Review and Approval Workflow</div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="review_by">Reviewer <span class="text-danger">*</span></label>
                        <select name="review_by" id="review_by" class="form-select" required>
                            <option value="">-- Select Reviewer --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}" @if(old('review_by') == $user->name) selected @endif>
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
                                <option value="{{ $user->name }}" @if(old('approval_by') == $user->name) selected @endif>
                                    {{ $user->name }} {{ $user->designation ? '(' . $user->designation . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="section-title">E. Remarks</div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks"
                                  id="remarks"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Enter remarks, internal notes, or certification tracking comments">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Certificate
                        </button>
                    </div>

                    <div class="col-md-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary w-100">
                            <i class="fa-solid fa-house me-1"></i> Back to Dashboard
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="{{ route('clients') }}" class="btn btn-secondary w-100">
                            <i class="fa-solid fa-list me-1"></i> Back to Clients
                        </a>
                    </div>
                </div>

            </form>
    </div>
</section>
@endsection

