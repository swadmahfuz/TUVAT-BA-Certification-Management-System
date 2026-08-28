@extends('layouts.admin')

@section('title', 'Import / Export')

@section('content')
<div class="page-heading"><div><h1>Import / Export</h1></div></div>

<section class="admin-card">
    <div class="admin-card-body">

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
</section>
@endsection

