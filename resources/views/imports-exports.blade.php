<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | BA Import / Export</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <style>
            .container {
                max-width: 95%;
            }

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

            .btn i {
                font-size: 16px;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            }

            label {
                font-weight: bold;
                font-size: 13px;
            }

            .form-control {
                font-size: 13px;
            }

            .section-title {
                background: #f1f3f5;
                padding: 8px 12px;
                border-left: 5px solid #212529;
                font-weight: bold;
                margin-top: 10px;
                margin-bottom: 15px;
                font-size: 14px;
            }

            .note-box {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 12px;
                border-radius: 8px;
                font-size: 13px;
                margin-bottom: 12px;
            }

            .table {
                font-size: 12px;
            }
        </style>
    </head>

    <body background="{{ asset('images/tuv-login-background1.jpg') }}">

        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">

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
                                    <h5>Import / Export BA Certificate Data</h5>
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
                                            <a href="{{ route('certificate.add') }}" class="btn btn-success">
                                                <i class="fa-solid fa-plus me-1"></i> Add Certificate
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('pendingCertificates') }}" class="btn btn-info">
                                                <i class="fa-solid fa-clock me-1"></i> Pending
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

                                <div class="section-title">A. Export BA Certificate Data</div>

                                <div class="note-box">
                                    Use this option to export BA certificate records into Excel format.
                                    The export includes client information, standard, accreditation body,
                                    certificate dates, surveillance / recertification dates, audit status,
                                    certificate status, workflow status and record metadata.
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <a href="{{ route('export') }}" class="btn btn-success w-100">
                                            <i class="fa-solid fa-file-excel me-1"></i> Export BA Certificate Data
                                        </a>
                                    </div>

                                    
                                </div>

                                <div class="section-title">B. Import BA Certificate Data</div>

                                <div class="note-box">
                                    Use this option to import BA certificate records from an Excel file.
                                    Please ensure that the Excel file follows the correct BA certificate import template.
                                    Start with one or two test rows before importing a large dataset.
                                </div>

                                <form method="POST" action="{{ route('import') }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="file">Select Excel File</label>
                                            <input type="file"
                                                   name="file"
                                                   id="file"
                                                   class="form-control"
                                                   accept=".xlsx,.xls,.csv"
                                                   required>
                                        </div>

                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fa-solid fa-upload me-1"></i> Import Data
                                            </button>
                                        </div>

                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100">
                                                <i class="fa-solid fa-house me-1"></i> Back to Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </form>

                                <div class="section-title">C. Recommended BA Import Template Columns</div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sl.</th>
                                                <th>Column Name</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr><td>1</td><td>client_name</td><td>Required</td></tr>
                                            <tr><td>2</td><td>client_address</td><td>Optional</td></tr>
                                            <tr><td>3</td><td>contact_person</td><td>Optional</td></tr>
                                            <tr><td>4</td><td>email</td><td>Optional</td></tr>
                                            <tr><td>5</td><td>phone</td><td>Optional</td></tr>
                                            <tr><td>6</td><td>client_remarks</td><td>Optional client-level remarks</td></tr>

                                            <tr><td>7</td><td>standard_name</td><td>Example: ISO 9001:2015</td></tr>
                                            <tr><td>8</td><td>standard_code</td><td>Example: QMS / EMS / OHSMS / FSMS / ISMS</td></tr>

                                            <tr><td>9</td><td>accreditation_body</td><td>Example: Egyptian Accreditation Council</td></tr>
                                            <tr><td>10</td><td>accreditation_body_short_name</td><td>Example: EGAC / UKAS / DAkkS</td></tr>

                                            <tr><td>11</td><td>certificate_number</td><td>Optional; must be unique if provided</td></tr>
                                            <tr><td>12</td><td>certificate_scope</td><td>Optional</td></tr>
                                            <tr><td>13</td><td>certification_cycle</td><td>Example: Initial Certification / Recertification / Transfer</td></tr>

                                            <tr><td>14</td><td>certificate_issue_date</td><td>Format: YYYY-MM-DD</td></tr>
                                            <tr><td>15</td><td>certificate_expiry_date</td><td>Format: YYYY-MM-DD</td></tr>
                                            <tr><td>16</td><td>initial_certification_audit_completion_date</td><td>Format: YYYY-MM-DD</td></tr>

                                            <tr><td>17</td><td>audit_status</td><td>Example: Not Scheduled / Scheduled / Completed / Report Pending / Closed</td></tr>
                                            <tr><td>18</td><td>certificate_status</td><td>Example: Active / Suspended / Withdrawn / Cancelled / Expired</td></tr>

                                            <tr><td>19</td><td>lead_auditor</td><td>Optional</td></tr>
                                            <tr><td>20</td><td>auditor_1</td><td>Optional</td></tr>
                                            <tr><td>21</td><td>auditor_2</td><td>Optional</td></tr>
                                            <tr><td>22</td><td>auditor_3</td><td>Optional</td></tr>
                                            <tr><td>23</td><td>technical_expert</td><td>Optional</td></tr>

                                            <tr><td>24</td><td>review_by</td><td>Reviewer name from users table; optional if review_by_email is provided</td></tr>
                                            <tr><td>25</td><td>review_by_email</td><td>Reviewer email from users table; preferred for accurate user ID matching</td></tr>
                                            <tr><td>26</td><td>approval_by</td><td>Approver name from users table; optional if approval_by_email is provided</td></tr>
                                            <tr><td>27</td><td>approval_by_email</td><td>Approver email from users table; preferred for accurate user ID matching</td></tr>

                                            <tr><td>28</td><td>remarks</td><td>Optional certificate-level remarks</td></tr>
                                        </tbody>
                                    </table>
                                </div>

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