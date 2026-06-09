<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | Upcoming BA Audits</title>

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

            .badge-date {
                font-size: 11px;
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
                                    <h5>Upcoming Surveillance / Recertification Audits</h5>
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
                                            <a href="{{ route('pendingCertificates') }}" class="btn btn-info">
                                                <i class="fa-solid fa-clock me-1"></i> Pending
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('expiredCertificates') }}" class="btn btn-danger">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Expired
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

                                <div class="alert alert-warning" style="font-size: 12px;">
                                    This page shows BA certificate records where Surveillance 1, Surveillance 2, or Recertification due date falls within the next 90 days.
                                </div>

                                <div class="table-container">
                                    <table class="table table-striped">
                                        <thead>
                                            <th colspan="13" style="text-align: center; font-weight: bold; font-size: 1.5em;">
                                                Upcoming Audit Records
                                            </th>

                                            <tr>
                                                <th>Sl.</th>
                                                <th>Client</th>
                                                <th>Standard</th>
                                                <th>Accreditation</th>
                                                <th>Certificate No.</th>
                                                <th>Expiry Date</th>
                                                <th>S1 Due</th>
                                                <th>S2 Due</th>
                                                <th>Recert. Due</th>
                                                <th>Next Due Type</th>
                                                <th>Audit Status</th>
                                                <th>Certificate Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $currentPage = $certificates->currentPage();
                                                $perPage = $certificates->perPage();
                                                $offset = ($currentPage - 1) * $perPage;
                                                $today = \Carbon\Carbon::today();
                                                $next90Days = \Carbon\Carbon::today()->addDays(90);
                                            @endphp

                                            @forelse($certificates as $certificate)
                                                @php
                                                    $nextDueType = 'N/A';

                                                    if ($certificate->surveillance_1_due_date) {
                                                        $s1 = \Carbon\Carbon::parse($certificate->surveillance_1_due_date);
                                                        if ($s1->between($today, $next90Days)) {
                                                            $nextDueType = 'Surveillance 1';
                                                        }
                                                    }

                                                    if ($certificate->surveillance_2_due_date) {
                                                        $s2 = \Carbon\Carbon::parse($certificate->surveillance_2_due_date);
                                                        if ($s2->between($today, $next90Days)) {
                                                            $nextDueType = 'Surveillance 2';
                                                        }
                                                    }

                                                    if ($certificate->recertification_due_date) {
                                                        $recert = \Carbon\Carbon::parse($certificate->recertification_due_date);
                                                        if ($recert->between($today, $next90Days)) {
                                                            $nextDueType = 'Recertification';
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
                                                        {{ $certificate->certificate_expiry_date ? \Carbon\Carbon::parse($certificate->certificate_expiry_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        @if($certificate->surveillance_1_due_date)
                                                            <span class="badge bg-light text-dark badge-date">
                                                                {{ \Carbon\Carbon::parse($certificate->surveillance_1_due_date)->format('d-m-Y') }}
                                                            </span>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($certificate->surveillance_2_due_date)
                                                            <span class="badge bg-light text-dark badge-date">
                                                                {{ \Carbon\Carbon::parse($certificate->surveillance_2_due_date)->format('d-m-Y') }}
                                                            </span>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($certificate->recertification_due_date)
                                                            <span class="badge bg-light text-dark badge-date">
                                                                {{ \Carbon\Carbon::parse($certificate->recertification_due_date)->format('d-m-Y') }}
                                                            </span>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($nextDueType == 'Surveillance 1')
                                                            <span class="badge bg-info text-dark">Surveillance 1</span>
                                                        @elseif($nextDueType == 'Surveillance 2')
                                                            <span class="badge bg-warning text-dark">Surveillance 2</span>
                                                        @elseif($nextDueType == 'Recertification')
                                                            <span class="badge bg-danger">Recertification</span>
                                                        @else
                                                            <span class="badge bg-secondary">N/A</span>
                                                        @endif
                                                    </td>

                                                    <td>{{ $certificate->audit_status ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->certificate_status ?? 'N/A' }}</td>

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
                                                    <td colspan="13" class="text-center">
                                                        No upcoming surveillance or recertification audit records found.
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