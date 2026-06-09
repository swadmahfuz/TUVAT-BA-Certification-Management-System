<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TÜV Austria BIC | Edit BA Client</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <style>
            .container { max-width: 95%; }

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

            .info-box {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 10px;
                border-radius: 8px;
                font-size: 12px;
            }
        </style>
    </head>

    <body background="{{ asset('images/tuv-login-background1.jpg') }}">

        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-9">

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
                                    <h5>Edit BA Client</h5>
                                </center>

                                <table style="width:70%; margin: auto;">
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
                                            <a href="{{ route('client.view', $client->id) }}" class="btn btn-info">
                                                <i class="fa-solid fa-circle-info me-1"></i> View Client
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('certificate.addForClient', $client->id) }}" class="btn btn-success">
                                                <i class="fa-solid fa-plus me-1"></i> Add Certificate
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

                                <div class="info-box mb-3">
                                    <b>Client ID:</b> {{ $client->id }} |
                                    <b>Created By:</b> {{ $client->created_by ?? 'N/A' }} |
                                    <b>Created At:</b>
                                    {{ $client->created_at ? \Carbon\Carbon::parse($client->created_at)->format('d-m-Y h:i A') : 'N/A' }}
                                    @if($client->updated_by)
                                        <br>
                                        <b>Last Updated By:</b> {{ $client->updated_by }} |
                                        <b>Updated At:</b>
                                        {{ $client->updated_at ? \Carbon\Carbon::parse($client->updated_at)->format('d-m-Y h:i A') : 'N/A' }}
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('client.update') }}">
                                    @csrf

                                    <input type="hidden" name="id" value="{{ $client->id }}">

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="client_name">Client Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="client_name"
                                                   id="client_name"
                                                   class="form-control"
                                                   value="{{ old('client_name', $client->client_name) }}"
                                                   placeholder="Enter client/company name"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="client_address">Client Address</label>
                                            <textarea name="client_address"
                                                      id="client_address"
                                                      class="form-control"
                                                      rows="3"
                                                      placeholder="Enter client address">{{ old('client_address', $client->client_address) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="contact_person">Contact Person</label>
                                            <input type="text"
                                                   name="contact_person"
                                                   id="contact_person"
                                                   class="form-control"
                                                   value="{{ old('contact_person', $client->contact_person) }}"
                                                   placeholder="Enter contact person name">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="email">Email</label>
                                            <input type="email"
                                                   name="email"
                                                   id="email"
                                                   class="form-control"
                                                   value="{{ old('email', $client->email) }}"
                                                   placeholder="Enter email address">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="phone">Phone</label>
                                            <input type="text"
                                                   name="phone"
                                                   id="phone"
                                                   class="form-control"
                                                   value="{{ old('phone', $client->phone) }}"
                                                   placeholder="Enter phone number">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="remarks">Remarks</label>
                                            <textarea name="remarks"
                                                      id="remarks"
                                                      class="form-control"
                                                      rows="3"
                                                      placeholder="Enter remarks, if any">{{ old('remarks', $client->remarks) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Client
                                            </button>
                                        </div>

                                        <div class="col-md-4">
                                            <a href="{{ route('client.view', $client->id) }}" class="btn btn-info w-100">
                                                <i class="fa-solid fa-circle-info me-1"></i> Back to Client View
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