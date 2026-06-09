<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | BA Certification Management System</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

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

            .summary-card {
                border-radius: 10px;
                padding: 12px;
                text-align: center;
                font-size: 12px;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                margin-bottom: 10px;
            }

            .summary-card h5 {
                font-size: 22px;
                margin-bottom: 0;
                font-weight: bold;
            }

            .summary-card small {
                color: #6c757d;
            }
        </style>
    </head>

    <body background="images/tuv-login-background1.jpg">

        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                        <div class="card">

                            <div class="card-header" style="padding-top: 20px; padding-bottom: 0px;">

                                <h6 style="text-align: right; margin-bottom: 10px;">
                                    Logged in User:
                                    <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b>
                                </h6>

                                <center>
                                    <h3 style="margin-bottom: 20px;">
                                        TÜV Austria BIC - BA Certification Management System
                                    </h3>
                                </center>

                                @php
                                    $currentDomain = request()->getHost();
                                    $baseDomain = preg_replace('/^[^.]+\./', '', $currentDomain);
                                @endphp

                                <!-- CVS Portal Switch Buttons -->
                                <table style="width:90%; margin: auto;">
                                    <tr>
                                        <td>
                                            <a href="https://training.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-graduation-cap me-1"></i> Training CVS Portal
                                            </a>
                                        </td>
                                        <td>
                                            <a href="https://inspection.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-magnifying-glass me-1"></i> Inspection CVS Portal
                                            </a>
                                        </td>
                                        <td>
                                            <a href="https://calibration.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-wrench me-1"></i> Calibration CVS Portal
                                            </a>
                                        </td>
                                        <td>
                                            <a href="https://reports.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-file-lines me-1"></i> Reports CVS Portal
                                            </a>
                                        </td>
                                        <td>
                                            <a href="https://certification.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-certificate me-1"></i> BA Certification Portal
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Main Action Buttons -->
                                <table style="width:95%; margin: auto;">
                                    <tr>
                                        <td>
                                            <a href="{{ route('client.add') }}" class="btn btn-success d-flex align-items-center">
                                                <i class="fa-solid fa-building me-1"></i> Add New Client
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('certificate.add') }}" class="btn btn-success d-flex align-items-center">
                                                <i class="fa-solid fa-plus me-1"></i> Add New Certificate
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary d-flex align-items-center">
                                                <i class="fa-solid fa-arrows-rotate me-1"></i> Refresh
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('pendingCertificates') }}" class="btn btn-info d-flex align-items-center">
                                                <i class="fa-solid fa-clock me-1"></i> Pending Certificates
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('upcomingAudits') }}" class="btn btn-warning d-flex align-items-center">
                                                <i class="fa-solid fa-calendar-days me-1"></i> Upcoming Audits
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('expiredCertificates') }}" class="btn btn-danger d-flex align-items-center">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Expired
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <table style="width:95%; margin: auto;">
                                    <tr>
                                        <td>
                                            <a href="{{ route('clients') }}" class="btn btn-secondary d-flex align-items-center">
                                                <i class="fa-solid fa-list me-1"></i> All Clients
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('standards.manage') }}" class="btn btn-secondary d-flex align-items-center">
                                                <i class="fa-solid fa-layer-group me-1"></i> Standards
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('accreditationBodies.manage') }}" class="btn btn-secondary d-flex align-items-center">
                                                <i class="fa-solid fa-building-columns me-1"></i> Accreditation Bodies
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('importsExports') }}" class="btn btn-warning d-flex align-items-center">
                                                <i class="fa-solid fa-file-import me-1"></i> Import/Export Data
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('allUsers') }}" class="btn btn-secondary d-flex align-items-center">
                                                <i class="fa-solid fa-users me-1"></i> View All Users
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('logout') }}" class="btn btn-danger d-flex align-items-center">
                                                <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Summary Cards -->
                                <div class="row mt-3 mb-2">
                                    <div class="col-md-2">
                                        <div class="summary-card">
                                            <small>Total Clients</small>
                                            <h5>{{ $totalClients ?? 0 }}</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="summary-card">
                                            <small>Total Certificates</small>
                                            <h5>{{ $totalCertificates ?? 0 }}</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="summary-card">
                                            <small>Active Certificates</small>
                                            <h5>{{ $activeCertificates ?? 0 }}</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="summary-card">
                                            <small>Pending Review</small>
                                            <h5>{{ $pendingReview ?? 0 }}</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="summary-card">
                                            <small>Pending Approval</small>
                                            <h5>{{ $pendingApproval ?? 0 }}</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="summary-card">
                                            <small>Approved</small>
                                            <h5>{{ $approvedCertificates ?? 0 }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <div class="summary-card">
                                            <small>Upcoming Surveillance 1</small>
                                            <h5>{{ $upcomingSurveillance1 ?? 0 }}</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="summary-card">
                                            <small>Upcoming Surveillance 2</small>
                                            <h5>{{ $upcomingSurveillance2 ?? 0 }}</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="summary-card">
                                            <small>Upcoming Recertification</small>
                                            <h5>{{ $upcomingRecertification ?? 0 }}</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="summary-card">
                                            <small>Expired Certificates</small>
                                            <h5>{{ $expiredCertificates ?? 0 }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <table style="width:35%; margin: auto;">
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control my-1 search-input" placeholder="Search BA Certificates / Clients / Standards"/>
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

                                <div class="table-container">
                                    <table class="table table-striped search-result">
                                        <thead>
                                            <th colspan="14" style="text-align: center; font-weight: bold; font-size: 1.5em;">
                                                All BA Certification Records
                                            </th>

                                            <tr>
                                                <th>Sl.</th>
                                                <th>Client</th>
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
                                                <th>QR Code</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $currentPage = $certificates->currentPage();
                                                $perPage = $certificates->perPage();
                                                $offset = ($currentPage - 1) * $perPage;
                                            @endphp

                                            @foreach ($certificates as $certificate)
                                                <tr>
                                                    <td>{{ $loop->iteration + $offset }}.</td>

                                                    <td>{{ $certificate->client->client_name ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->standard->standard_name ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->accreditationBody->short_name ?? $certificate->accreditationBody->accreditation_body_name ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->certificate_number ?? 'Not Issued' }}</td>

                                                    <td>
                                                        @if($certificate->certificate_issue_date)
                                                            {{ \Carbon\Carbon::parse($certificate->certificate_issue_date)->format('d-m-Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($certificate->certificate_expiry_date)
                                                            {{ \Carbon\Carbon::parse($certificate->certificate_expiry_date)->format('d-m-Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($certificate->surveillance_1_due_date)
                                                            {{ \Carbon\Carbon::parse($certificate->surveillance_1_due_date)->format('d-m-Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($certificate->surveillance_2_due_date)
                                                            {{ \Carbon\Carbon::parse($certificate->surveillance_2_due_date)->format('d-m-Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($certificate->recertification_due_date)
                                                            {{ \Carbon\Carbon::parse($certificate->recertification_due_date)->format('d-m-Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>{{ $certificate->certificate_status ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->status ?? 'N/A' }}</td>

                                                    @php
                                                        $url = url('');
                                                        $verification_url = $url . '?search=' . $certificate->certificate_number;
                                                    @endphp

                                                    <td>
                                                        @if($certificate->certificate_number)
                                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ $verification_url }}"/>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <a href="view-certificate/{{ $certificate->id }}" style="margin-bottom: 5px" target="_blank">
                                                            <i class="fa-solid fa-circle-info" title="View Certificate Details"></i>
                                                        </a>

                                                        <a href="edit-certificate/{{ $certificate->id }}" style="margin-bottom: 5px" target="_blank">
                                                            <i class="fa-solid fa-pen-to-square" title="Edit Certificate Information"></i>
                                                        </a>

                                                        <a href="delete-certificate/{{ $certificate->id }}" style="margin-bottom: 5px" onclick="return confirm('Are you sure you want to delete this certificate record?')">
                                                            <i class="fa-solid fa-trash" title="Delete Certificate"></i>
                                                        </a>

                                                        @if(Auth::check() && (Auth::user()->id == $certificate->review_by_id || Auth::user()->name == $certificate->review_by) && $certificate->status == 'Pending Review')
                                                            <a href="{{ route('certificate.review', $certificate->id) }}" style="margin-bottom: 5px">
                                                                <i class="fa-solid fa-thumbs-up" title="Mark as Reviewed"></i>
                                                            </a>
                                                        @endif

                                                        @if(Auth::check() && (Auth::user()->id == $certificate->approval_by_id || Auth::user()->name == $certificate->approval_by) && $certificate->status == 'Pending Approval')
                                                            <a href="{{ route('certificate.approve', $certificate->id) }}" style="margin-bottom: 5px">
                                                                <i class="fa-solid fa-check" title="Mark as Approved"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
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

        <script type="text/javascript">
            $(document).ready(function() {

                function fetchCertificates(page = 1, userInput = '') {
                    $.ajax({
                        url: "{{ url('live-search') }}",
                        data: {
                            userInput: userInput,
                            page: page
                        },
                        dataType: 'json',

                        beforeSend: function() {
                            $(".search-result tbody").html('<tr><td colspan="14">Searching...</td></tr>');
                        },

                        success: function(res) {
                            var _html = '';

                            $.each(res.data.data, function(index, data) {

                                var clientName = data.client ? data.client.client_name : 'N/A';
                                var standardName = data.standard ? data.standard.standard_name : 'N/A';
                                var accreditationName = 'N/A';

                                if (data.accreditation_body) {
                                    accreditationName = data.accreditation_body.short_name ?? data.accreditation_body.accreditation_body_name ?? 'N/A';
                                }

                                var certificateNumber = data.certificate_number ? data.certificate_number : 'Not Issued';
                                var verification_url = "{{ url('') }}" + "?search=" + certificateNumber;

                                _html += '<tr>';
                                _html += '<td>' + (index + 1 + (res.data.current_page - 1) * res.data.per_page) + '.</td>';
                                _html += '<td>' + clientName + '</td>';
                                _html += '<td>' + standardName + '</td>';
                                _html += '<td>' + accreditationName + '</td>';
                                _html += '<td>' + certificateNumber + '</td>';
                                _html += '<td>' + formatDate(data.certificate_issue_date) + '</td>';
                                _html += '<td>' + formatDate(data.certificate_expiry_date) + '</td>';
                                _html += '<td>' + formatDate(data.surveillance_1_due_date) + '</td>';
                                _html += '<td>' + formatDate(data.surveillance_2_due_date) + '</td>';
                                _html += '<td>' + formatDate(data.recertification_due_date) + '</td>';
                                _html += '<td>' + (data.certificate_status ?? 'N/A') + '</td>';
                                _html += '<td>' + (data.status ?? 'N/A') + '</td>';

                                if (data.certificate_number) {
                                    _html += '<td><img src="' + generateQRCode(verification_url) + '"/></td>';
                                } else {
                                    _html += '<td>N/A</td>';
                                }

                                _html += '<td>';
                                _html += '<a href="view-certificate/' + data.id + '" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-circle-info" title="View Certificate Details"></i></a> ';
                                _html += '<a href="edit-certificate/' + data.id + '" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-pen-to-square" title="Edit Certificate Information"></i></a> ';
                                _html += '<a href="delete-certificate/' + data.id + '" style="margin-bottom: 5px" onclick="return confirm(\'Are you sure you want to delete this certificate record?\')"><i class="fa-solid fa-trash" title="Delete Certificate"></i></a> ';

                                @if(Auth::check())
                                    if ({{ Auth::user()->id }} == data.review_by_id || "{{ Auth::user()->name }}" == data.review_by) {
                                        if (data.status == 'Pending Review') {
                                            _html += '<a href="' + "{{ url('') }}/review-certificate/" + data.id + '"><i class="fa-solid fa-thumbs-up" title="Mark as Reviewed"></i></a> ';
                                        }
                                    }

                                    if ({{ Auth::user()->id }} == data.approval_by_id || "{{ Auth::user()->name }}" == data.approval_by) {
                                        if (data.status == 'Pending Approval') {
                                            _html += '<a href="' + "{{ url('') }}/approve-certificate/" + data.id + '"><i class="fa-solid fa-check" title="Mark as Approved"></i></a> ';
                                        }
                                    }
                                @endif

                                _html += '</td>';
                                _html += '</tr>';
                            });

                            if (_html === '') {
                                _html = '<tr><td colspan="14" class="text-center">No matching BA certification records found.</td></tr>';
                            }

                            $(".search-result tbody").html(_html);

                            $('.pagination-container').remove();
                            $('.card-footer').html(generatePaginationLinks(res.data));
                        }
                    });
                }

                function formatDate(date) {
                    if (!date) return 'N/A';

                    var d = new Date(date);
                    var day = ('0' + d.getDate()).slice(-2);
                    var month = ('0' + (d.getMonth() + 1)).slice(-2);
                    var year = d.getFullYear();

                    return day + '-' + month + '-' + year;
                }

                function generateQRCode(url) {
                    return 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' + encodeURIComponent(url);
                }

                function generatePaginationLinks(data) {
                    var paginationLinks = '<nav class="pagination-container"><ul class="pagination">';

                    if (data.current_page > 1) {
                        paginationLinks += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">&laquo;</a></li>';
                    }

                    for (var i = 1; i <= data.last_page; i++) {
                        paginationLinks += '<li class="page-item' + (i === data.current_page ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                    }

                    if (data.current_page < data.last_page) {
                        paginationLinks += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">&raquo;</a></li>';
                    }

                    paginationLinks += '</ul></nav>';

                    return paginationLinks;
                }

                $(".search-input").on('keyup', function() {
                    var userInput = $(this).val();
                    fetchCertificates(1, userInput);
                });

                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();

                    var page = $(this).attr('data-page');
                    var userInput = $('.search-input').val();

                    fetchCertificates(page, userInput);
                });

            });
        </script>

    </body>

    <footer>
        @include('layouts.footer')
    </footer>
</html>