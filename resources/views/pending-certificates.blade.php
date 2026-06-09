<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | Pending BA Certificates</title>

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

                            <div class="card-header" style="padding-top: 20px; padding-bottom: 0px;">

                                <h6 style="text-align: right; margin-bottom: 10px;">
                                    Logged in User:
                                    <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b>
                                </h6>

                                <center>
                                    <h3 style="margin-bottom: 10px;">
                                        TÜV Austria BIC - BA Certification Management System
                                    </h3>
                                    <h5 style="margin-bottom: 20px;">Pending Review / Pending Approval</h5>
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
                                            <a href="{{ route('bulkReview') }}" class="btn btn-info">
                                                <i class="fa-solid fa-thumbs-up me-1"></i> Bulk Review
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('bulkApprove') }}" class="btn btn-success">
                                                <i class="fa-solid fa-check me-1"></i> Bulk Approve
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('logout') }}" class="btn btn-danger">
                                                <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <table style="width:35%; margin: auto;">
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control my-2 search-input" placeholder="Search pending certificates"/>
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

                                <div class="alert alert-info" style="font-size: 12px;">
                                    This page shows only records assigned to you as <b>Reviewer</b> or <b>Approver</b>.
                                </div>

                                <div class="table-container">
                                    <table class="table table-striped search-result">
                                        <thead>
                                            <th colspan="13" style="text-align: center; font-weight: bold; font-size: 1.5em;">
                                                Pending BA Certification Records
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
                                                <th>Reviewer</th>
                                                <th>Approver</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $currentPage = $certificates->currentPage();
                                                $perPage = $certificates->perPage();
                                                $offset = ($currentPage - 1) * $perPage;
                                            @endphp

                                            @forelse($certificates as $certificate)
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
                                                        {{ $certificate->surveillance_1_due_date ? \Carbon\Carbon::parse($certificate->surveillance_1_due_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $certificate->surveillance_2_due_date ? \Carbon\Carbon::parse($certificate->surveillance_2_due_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $certificate->recertification_due_date ? \Carbon\Carbon::parse($certificate->recertification_due_date)->format('d-m-Y') : 'N/A' }}
                                                    </td>

                                                    <td>{{ $certificate->review_by ?? 'N/A' }}</td>

                                                    <td>{{ $certificate->approval_by ?? 'N/A' }}</td>

                                                    <td>
                                                        @if($certificate->status == 'Pending Review')
                                                            <span class="badge bg-secondary">Pending Review</span>
                                                        @elseif($certificate->status == 'Pending Approval')
                                                            <span class="badge bg-warning text-dark">Pending Approval</span>
                                                        @else
                                                            <span class="badge bg-light text-dark">{{ $certificate->status ?? 'N/A' }}</span>
                                                        @endif
                                                    </td>

                                                    <td class="action-icons">
                                                        <a href="{{ route('certificate.view', $certificate->id) }}" target="_blank">
                                                            <i class="fa-solid fa-circle-info" title="View Certificate"></i>
                                                        </a>

                                                        <a href="{{ route('certificate.edit', $certificate->id) }}" target="_blank">
                                                            <i class="fa-solid fa-pen-to-square" title="Edit Certificate"></i>
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
                                                        No pending records assigned to you.
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

        <script type="text/javascript">
            $(document).ready(function() {

                function fetchPendingCertificates(page = 1, userInput = '') {
                    $.ajax({
                        url: "{{ url('live-search-pending') }}",
                        data: {
                            userInput: userInput,
                            page: page
                        },
                        dataType: 'json',

                        beforeSend: function() {
                            $(".search-result tbody").html('<tr><td colspan="13">Searching...</td></tr>');
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

                                _html += '<tr>';
                                _html += '<td>' + (index + 1 + (res.data.current_page - 1) * res.data.per_page) + '.</td>';
                                _html += '<td>' + clientName + '</td>';
                                _html += '<td>' + standardName + '</td>';
                                _html += '<td>' + accreditationName + '</td>';
                                _html += '<td>' + (data.certificate_number ?? 'Not Issued') + '</td>';
                                _html += '<td>' + formatDate(data.certificate_expiry_date) + '</td>';
                                _html += '<td>' + formatDate(data.surveillance_1_due_date) + '</td>';
                                _html += '<td>' + formatDate(data.surveillance_2_due_date) + '</td>';
                                _html += '<td>' + formatDate(data.recertification_due_date) + '</td>';
                                _html += '<td>' + (data.review_by ?? 'N/A') + '</td>';
                                _html += '<td>' + (data.approval_by ?? 'N/A') + '</td>';
                                _html += '<td>' + statusBadge(data.status) + '</td>';

                                _html += '<td>';
                                _html += '<a href="view-certificate/' + data.id + '" target="_blank"><i class="fa-solid fa-circle-info" title="View Certificate"></i></a> ';
                                _html += '<a href="edit-certificate/' + data.id + '" target="_blank"><i class="fa-solid fa-pen-to-square" title="Edit Certificate"></i></a> ';

                                @if(Auth::check())
                                    if (({{ Auth::user()->id }} == data.review_by_id || "{{ Auth::user()->name }}" == data.review_by) && data.status == 'Pending Review') {
                                        _html += '<a href="' + "{{ url('') }}/review-certificate/" + data.id + '"><i class="fa-solid fa-thumbs-up" title="Mark as Reviewed"></i></a> ';
                                    }

                                    if (({{ Auth::user()->id }} == data.approval_by_id || "{{ Auth::user()->name }}" == data.approval_by) && data.status == 'Pending Approval') {
                                        _html += '<a href="' + "{{ url('') }}/approve-certificate/" + data.id + '"><i class="fa-solid fa-check" title="Mark as Approved"></i></a> ';
                                    }
                                @endif

                                _html += '</td>';
                                _html += '</tr>';
                            });

                            if (_html === '') {
                                _html = '<tr><td colspan="13" class="text-center">No matching pending records found.</td></tr>';
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

                function statusBadge(status) {
                    if (status === 'Pending Review') {
                        return '<span class="badge bg-secondary">Pending Review</span>';
                    }

                    if (status === 'Pending Approval') {
                        return '<span class="badge bg-warning text-dark">Pending Approval</span>';
                    }

                    return '<span class="badge bg-light text-dark">' + (status ?? 'N/A') + '</span>';
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
                    fetchPendingCertificates(1, userInput);
                });

                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();

                    var page = $(this).attr('data-page');
                    var userInput = $('.search-input').val();

                    fetchPendingCertificates(page, userInput);
                });

            });
        </script>

    </body>

    <footer>
        @include('layouts.footer')
    </footer>
</html>