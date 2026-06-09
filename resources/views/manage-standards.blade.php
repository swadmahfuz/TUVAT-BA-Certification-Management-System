<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | Manage BA Standards</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <style>
            .container { max-width: 95%; }

            .table-striped tbody td,
            .table-striped thead th {
                vertical-align: middle;
            }

            .table-striped thead th {
                text-align: left;
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
                margin-top: 10px;
                margin-bottom: 15px;
                font-size: 14px;
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
                                    <h5>Manage Certification Standards</h5>
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

                                <div class="section-title">Add New Standard</div>

                                <form method="POST" action="{{ route('standard.create') }}">
                                    @csrf

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label for="standard_name">Standard Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="standard_name"
                                                   id="standard_name"
                                                   class="form-control"
                                                   value="{{ old('standard_name') }}"
                                                   placeholder="Example: ISO 9001:2015"
                                                   required>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="standard_code">Standard Code</label>
                                            <input type="text"
                                                   name="standard_code"
                                                   id="standard_code"
                                                   class="form-control"
                                                   value="{{ old('standard_code') }}"
                                                   placeholder="Example: QMS / EMS / OHSMS">
                                        </div>

                                        <div class="col-md-2">
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-select" required>
                                                <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fa-solid fa-plus me-1"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="section-title">Existing Standards</div>

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sl.</th>
                                            <th>Standard Name</th>
                                            <th>Standard Code</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Update</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($standards as $standard)
                                            <tr>
                                                <form method="POST" action="{{ route('standard.update') }}">
                                                    @csrf

                                                    <input type="hidden" name="id" value="{{ $standard->id }}">

                                                    <td>{{ $loop->iteration }}.</td>

                                                    <td>
                                                        <input type="text"
                                                               name="standard_name"
                                                               class="form-control"
                                                               value="{{ $standard->standard_name }}"
                                                               required>
                                                    </td>

                                                    <td>
                                                        <input type="text"
                                                               name="standard_code"
                                                               class="form-control"
                                                               value="{{ $standard->standard_code }}">
                                                    </td>

                                                    <td>
                                                        <select name="status" class="form-select" required>
                                                            <option value="Active" {{ $standard->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                            <option value="Inactive" {{ $standard->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </td>

                                                    <td>
                                                        {{ $standard->created_at ? \Carbon\Carbon::parse($standard->created_at)->format('d-m-Y h:i A') : 'N/A' }}
                                                    </td>

                                                    <td>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                                        </button>
                                                    </td>
                                                </form>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    No standards found.
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
        </section>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    </body>

    <footer>
        @include('layouts.footer')
    </footer>
</html>