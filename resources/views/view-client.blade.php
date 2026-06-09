<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | View BA Client</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <style>
            .container { max-width: 99%; }
            .table-container { overflow-x: auto; }

            .table-striped tbody td,
            .table-striped thead th {
                vertical-align: middle;
            }

            .table-striped thead th {
                text-align: left;
                position: sticky;
                top: 0;
                background-color: rgb(243, 243, 243);
                border-right: 1px solid #dee2e6;
            }

            .table-striped thead th:last-child {
                border-right: none;
            }

            .table-striped {
                font-size: 11px;
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

            .info-box {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 12px;
                border-radius: 8px;
                font-size: 13px;
                margin-bottom: 12px;
            }

            .info-label {
                font-weight: bold;
                color: #333;
            }

            .action-icons a {
                margin-right: 8px;
                text-decoration: none;
            }
        </style>
    </head>

    <body background="{{ asset('images/tuv-login-background1.jpg') }}">

        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">

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
                                    <h5>View BA Client</h5>
                                </center>

                                <table style="width:90%; margin: auto;">
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
                                            <a href="{{ route('client.edit', $client->id) }}" class="btn btn-warning">
                                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Client
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('certificate.addForClient', $client->id) }}" class="btn btn-success">
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

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="info-box">
                                            <h5 style="margin-bottom: 15px;">Client Information</h5>

                                            <div class="row mb-2">
                                                <div class="col-md-3 info-label">Client Name</div>
                                                <div class="col-md-9">{{ $client->client_name ?? 'N/A' }}</div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3 info-label">Client Address</div>
                                                <div class="col-md-9">{{ $client->client_address ?? 'N/A' }}</div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3 info-label">Contact Person</div>
                                                <div class="col-md-9">{{ $client->contact_person ?? 'N/A' }}</div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3 info-label">Email</div>
                                                <div class="col-md-9">
                                                    @if($client->email)
                                                        <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3 info-label">Phone</div>
                                                <div class="col-md-9">{{ $client->phone ?? 'N/A' }}</div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3 info-label">Remarks</div>
                                                <div class="col-md-9">{{ $client->remarks ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <h5 style="margin-bottom: 15px;">Record Information</h5>

                                            <div class="row mb-2">
                                                <div class="col-md-5 info-label">Client ID</div>
                                                <div class="col-md-7">{{ $client->id }}</div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-5 info-label">Created By</div>
                                                <div class="col-md-7">{{ $client->created_by ?? 'N/A' }}</div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-5 info-label">Created At</div>
                                                <div class="col-md-7">
                                                    {{ $client->created_at ? \Carbon\Carbon::parse($client->created_at)->format('d-m-Y h:i A') : 'N/A' }}
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-5 info-label">Updated By</div>
                                                <div class="col-md-7">{{ $client->updated_by ?? 'N/A' }}</div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-5 info-label">Updated At</div>
                                                <div class="col-md-7">
                                                    {{ $client->updated_at ? \Carbon\Carbon::parse($client->updated_at)->format('d-m-Y h:i A') : 'N/A' }}
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-5 info-label">Total Certificates</div>
                                                <div class="col-md-7">
                                                    <span class="badge bg-primary">
                                                        {{ $client->certificates ? $client->certificates->count() : 0 }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-container mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <th colspan="13" style="text-align: center; font-weight: bold; font-size: 1.5em;">
                                                Certificates Under This Client
                                            </th>

                                            <tr>
                                                <th>Sl.</th>
                                                <th>Standard</th>
                                                <th>Accreditation</th>
                                                <th>Certificate No.</th>
                                                <th>Issue Date</th>
                                                <th>Expiry Date</th>
                                                <th>S1 Due</th>
                                                <th>S2 Due</th>
                                                <th>Recert. Due</th>
                                                <th>Cert. Status</th>
                                                <th>Workflow</th>
                                                <th>PDF</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse($client->certificates as $certificate)
                                                <tr>
                                                    <td>{{ $loop->iteration }}.</td>

                                                    <td>{{ $certificate->standard->standard_name ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->accreditationBody->short_name ?? $certificate->accreditationBody->accreditation_body_name ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->certificate_number ?? 'Not Issued' }}</td>

                                                    <td>
                                                        {{ $certificate->certificate_issue_date ? \Carbon\Carbon::parse($certificate->certificate_issue_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $certificate->certificate_expiry_date ? \Carbon\Carbon::parse($certificate->certificate_expiry_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $certificate->surveillance_1_due_date ? \Carbon\Carbon::parse($certificate->surveillance_1_due_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $certificate->surveillance_2_due_date ? \Carbon\Carbon::parse($certificate->surveillance_2_due_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $certificate->recertification_due_date ? \Carbon\Carbon::parse($certificate->recertification_due_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>{{ $certificate->certificate_status ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->status ?? 'N/A' }}</td>

                                                    <td>
                                                        @if($certificate->certificate_pdf)
                                                            <a href="{{ route('certificate.viewPdf', $certificate->id) }}" target="_blank">
                                                                <i class="fa-solid fa-file-pdf text-danger" title="View PDF"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No PDF</span>
                                                        @endif
                                                    </td>

                                                    <td class="action-icons">
                                                        <a href="{{ route('certificate.view', $certificate->id) }}" target="_blank">
                                                            <i class="fa-solid fa-circle-info" title="View Certificate"></i>
                                                        </a>

                                                        <a href="{{ route('certificate.edit', $certificate->id) }}" target="_blank">
                                                            <i class="fa-solid fa-pen-to-square" title="Edit Certificate"></i>
                                                        </a>

                                                        <a href="{{ route('certificate.delete', $certificate->id) }}"
                                                           onclick="return confirm('Are you sure you want to delete this certificate record?')">
                                                            <i class="fa-solid fa-trash" title="Delete Certificate"></i>
                                                        </a>

                                                        @if(Auth::check() && (Auth::user()->id == $certificate->review_by_id || Auth::user()->name == $certificate->review_by) && $certificate->status == 'Pending Review')
                                                            <a href="{{ route('certificate.review', $certificate->id) }}">
                                                                <i class="fa-solid fa-thumbs-up" title="Mark as Reviewed"></i>
                                                            </a>
                                                        @endif

                                                        @if(Auth::check() && (Auth::user()->id == $certificate->approval_by_id || Auth::user()->name == $certificate->approval_by) && $certificate->status == 'Pending Approval')
                                                            <a href="{{ route('certificate.approve', $certificate->id) }}">
                                                                <i class="fa-solid fa-check" title="Mark as Approved"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="13" class="text-center">
                                                        No certificate records found for this client.
                                                    </td>
                                                </tr>
                                            @endforelse
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