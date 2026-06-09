<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | Add BA Certificate</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <style>
            .container { max-width: 97%; }

            .btn {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 10px 15px;
                border-radius: 8px;
                font-size: 10px;
                font-weight: bold;
                transition: all 0.3s ease;
                margin: 3px;
            }

            .btn i { font-size: 16px; }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            }

            label {
                font-weight: bold;
                font-size: 13px;
            }

            .form-control, .form-select {
                font-size: 13px;
            }

            .section-title {
                background: #f1f3f5;
                padding: 8px 12px;
                border-left: 5px solid #212529;
                font-weight: bold;
                margin-top: 18px;
                margin-bottom: 15px;
                font-size: 14px;
            }

            .help-note {
                font-size: 11px;
                color: #6c757d;
            }
        </style>
    </head>

    <body background="{{ asset('images/tuv-login-background1.jpg') }}">

        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-11">

                        <div class="card">

                            <div class="card-header" style="padding-top: 20px; padding-bottom: 10px;">

                                <h6 style="text-align: right; margin-bottom: 10px;">
                                    Logged in User:
                                    <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b>
                                </h6>

                                <center>
                                    <h3 style="margin-bottom: 10px;">
                                        TÜV Austria BIC - BA Certification Management System
                                    </h3>
                                    <h5>Add New BA Certificate Record</h5>
                                </center>

                                <table style="width:80%; margin: auto;">
                                    <tr>
                                        <td>
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                                <i class="fa-solid fa-house me-1"></i> Dashboard
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('clients') }}" class="btn btn-secondary">
                                                <i class="fa-solid fa-list me-1"></i> All Clients
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('client.add') }}" class="btn btn-success">
                                                <i class="fa-solid fa-building me-1"></i> Add Client
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('standards.manage') }}" class="btn btn-secondary">
                                                <i class="fa-solid fa-layer-group me-1"></i> Standards
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('accreditationBodies.manage') }}" class="btn btn-secondary">
                                                <i class="fa-solid fa-building-columns me-1"></i> Accreditation Bodies
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('logout') }}" class="btn btn-danger">
                                                <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                            </div>

                            <div class="card-body">

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <b>Please correct the following errors:</b>
                                        <ul style="margin-bottom: 0;">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('certificate.create') }}">
                                    @csrf

                                    <div class="section-title">A. Client and Certification Information</div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="ba_client_id">Client <span class="text-danger">*</span></label>
                                            <select name="ba_client_id" id="ba_client_id" class="form-select" required>
                                                <option value="">-- Select Client --</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}"
                                                        @if(old('ba_client_id', $selectedClient->id ?? '') == $client->id) selected @endif>
                                                        {{ $client->client_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="help-note">If client is missing, add client first.</div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="ba_standard_id">Certification Standard</label>
                                            <select name="ba_standard_id" id="ba_standard_id" class="form-select">
                                                <option value="">-- Select Standard --</option>
                                                @foreach($standards as $standard)
                                                    <option value="{{ $standard->id }}"
                                                        @if(old('ba_standard_id') == $standard->id) selected @endif>
                                                        {{ $standard->standard_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="ba_accreditation_body_id">Accreditation Body</label>
                                            <select name="ba_accreditation_body_id" id="ba_accreditation_body_id" class="form-select">
                                                <option value="">-- Select Accreditation Body --</option>
                                                @foreach($accreditationBodies as $body)
                                                    <option value="{{ $body->id }}"
                                                        @if(old('ba_accreditation_body_id') == $body->id) selected @endif>
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

                        </div>

                    </div>
                </div>
            </div>
        </section>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    </body>

    <footer>
        @include('layouts.footer')
    </footer>
</html>