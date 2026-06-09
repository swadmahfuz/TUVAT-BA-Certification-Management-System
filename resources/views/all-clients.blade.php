<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | All BA Clients</title>

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
                font-size: 12px;
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
                                    <h5 style="margin-bottom: 20px;">
                                        All BA Clients
                                    </h5>
                                </center>

                                <table style="width:90%; margin: auto;">
                                    <tr>
                                        <td>
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary d-flex align-items-center">
                                                <i class="fa-solid fa-house me-1"></i> Dashboard
                                            </a>
                                        </td>
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
                                            <a href="{{ route('logout') }}" class="btn btn-danger d-flex align-items-center">
                                                <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <form method="GET" action="{{ route('clients') }}">
                                    <table style="width:40%; margin: auto;">
                                        <tr>
                                            <td>
                                                <input type="text" name="search" class="form-control my-2"
                                                       placeholder="Search clients by name, contact, email or phone"
                                                       value="{{ request('search') }}">
                                            </td>
                                            <td style="width:90px;">
                                                <button type="submit" class="btn btn-dark">
                                                    <i class="fa-solid fa-search me-1"></i> Search
                                                </button>
                                            </td>
                                            <td style="width:90px;">
                                                <a href="{{ route('clients') }}" class="btn btn-secondary">
                                                    <i class="fa-solid fa-rotate me-1"></i> Reset
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </form>

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
                                    <table class="table table-striped">
                                        <thead>
                                            <th colspan="9" style="text-align: center; font-weight: bold; font-size: 1.5em;">
                                                Client List
                                            </th>

                                            <tr>
                                                <th>Sl.</th>
                                                <th>Client Name</th>
                                                <th>Address</th>
                                                <th>Contact Person</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Total Certificates</th>
                                                <th>Remarks</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $currentPage = $clients->currentPage();
                                                $perPage = $clients->perPage();
                                                $offset = ($currentPage - 1) * $perPage;
                                            @endphp

                                            @forelse($clients as $client)
                                                <tr>
                                                    <td>{{ $loop->iteration + $offset }}.</td>

                                                    <td>
                                                        <b>{{ $client->client_name }}</b>
                                                    </td>

                                                    <td>
                                                        {{ $client->client_address ?? 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{ $client->contact_person ?? 'N/A' }}
                                                    </td>

                                                    <td>
                                                        @if($client->email)
                                                            <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{ $client->phone ?? 'N/A' }}
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-primary">
                                                            {{ $client->certificates_count ?? 0 }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        {{ $client->remarks ?? 'N/A' }}
                                                    </td>

                                                    <td class="action-icons">
                                                        <a href="{{ route('client.view', $client->id) }}" target="_blank">
                                                            <i class="fa-solid fa-circle-info" title="View Client Details"></i>
                                                        </a>

                                                        <a href="{{ route('client.edit', $client->id) }}" target="_blank">
                                                            <i class="fa-solid fa-pen-to-square" title="Edit Client"></i>
                                                        </a>

                                                        <a href="{{ route('certificate.addForClient', $client->id) }}">
                                                            <i class="fa-solid fa-plus" title="Add Certificate for this Client"></i>
                                                        </a>

                                                        <a href="{{ route('client.delete', $client->id) }}"
                                                           onclick="return confirm('Are you sure you want to delete this client? Client cannot be deleted if certificates exist.')">
                                                            <i class="fa-solid fa-trash" title="Delete Client"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        No BA clients found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <div class="card-footer">
                                {{ $clients->links() }}
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