<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | Expired BA Certificates</title>

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
                                    <h5>Expired BA Certificates</h5>
                                </center>

                                <table style="width:85%; margin: auto;">
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
                                            <a href="{{ route('upcomingAudits') }}" class="btn btn-warning">
                                                <i class="fa-solid fa-calendar-days me-1"></i> Upcoming Audits
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

                                <div class="alert alert-danger" style="font-size: 12px;">
                                    This page shows BA certificates where the certificate expiry date has already passed.
                                    Records may still be within the 6-month grace period or beyond the grace period.
                                </div>

                                <div class="table-container">
                                    <table class="table table-striped">
                                        <thead>
                                            <th colspan="14" style="text-align: center; font-weight: bold; font-size: 1.5em;">
                                                Expired Certificate Records
                                            </th>

                                            <tr>
                                                <th>Sl.</th>
                                                <th>Client</th>
                                                <th>Standard</th>
                                                <th>Accreditation</th>
                                                <th>Certificate No.</th>
                                                <th>Issue Date</th>
                                                <th>Expiry Date</th>
                                                <th>Grace Period End</th>
                                                <th>Grace Status</th>
                                                <th>Recert. Due</th>
                                                <th>Audit Status</th>
                                                <th>Certificate Status</th>
                                                <th>Workflow</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $currentPage = $certificates->currentPage();
                                                $perPage = $certificates->perPage();
                                                $offset = ($currentPage - 1) * $perPage;
                                                $today = \Carbon\Carbon::today();
                                            @endphp

                                            @forelse($certificates as $certificate)
                                                @php
                                                    $graceStatus = 'N/A';

                                                    if ($certificate->grace_period_end_date) {
                                                        $graceEnd = \Carbon\Carbon::parse($certificate->grace_period_end_date);

                                                        if ($graceEnd->gte($today)) {
                                                            $graceStatus = 'Within Grace Period';
                                                        } else {
                                                            $graceStatus = 'Grace Period Over';
                                                        }
                                                    }
                                                @endphp

                                                <tr>
                                                    <td>{{ $loop->iteration + $offset }}.</td>

                                                    <td>{{ $certificate->client->client_name ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->standard->standard_name ?? 'N/A' }}</td>

                                                    <td>
                                                        {{ $certificate->accreditationBody->short_name ?? $certificate->accreditationBody->accreditation_body_name ?? 'N/A' }}
                                                    </td>

                                                    <td>{{ $certificate->certificate_number ?? 'Not Issued' }}</td>

                                                    <td>
                                                        {{ $certificate->certificate_issue_date ? \Carbon\Carbon::parse($certificate->certificate_issue_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $certificate->certificate_expiry_date ? \Carbon\Carbon::parse($certificate->certificate_expiry_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $certificate->grace_period_end_date ? \Carbon\Carbon::parse($certificate->grace_period_end_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        @if($graceStatus == 'Within Grace Period')
                                                            <span class="badge bg-warning text-dark">Within Grace Period</span>
                                                        @elseif($graceStatus == 'Grace Period Over')
                                                            <span class="badge bg-danger">Grace Period Over</span>
                                                        @else
                                                            <span class="badge bg-secondary">N/A</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{ $certificate->recertification_due_date ? \Carbon\Carbon::parse($certificate->recertification_due_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>{{ $certificate->audit_status ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->certificate_status ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->status ?? 'N/A' }}</td>

                                                    <td class="action-icons">
                                                        <a href="{{ route('certificate.view', $certificate->id) }}" target="_blank">
                                                            <i class="fa-solid fa-circle-info" title="View Certificate"></i>
                                                        </a>

                                                        <a href="{{ route('certificate.edit', $certificate->id) }}" target="_blank">
                                                            <i class="fa-solid fa-pen-to-square" title="Edit Certificate"></i>
                                                        </a>

                                                        @if($certificate->client)
                                                            <a href="{{ route('client.view', $certificate->client->id) }}" target="_blank">
                                                                <i class="fa-solid fa-building" title="View Client"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="14" class="text-center">
                                                        No expired BA certificate records found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <div class="card-footer">
                                {{ $certificates->links() }}
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