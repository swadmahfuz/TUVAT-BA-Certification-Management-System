<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TÜV Austria BIC CVS | Business Assurance (ISO) Certificate Verification System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <style>
        body {
            background-color: #f8f9fa;
            font-size: 13px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding-top: 40px;
        }

        .form-control {
            font-size: 14px;
            padding: 10px;
        }

        .btn {
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 15px;
        }

        h1, h3, h4 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px;
        }

        .verification-note {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="text-center mb-4">
            <img src="{{ asset('images/TUV Austria Logo.png') }}" alt="TUV Logo" width="250">

            <h1>Verify Business Assurance Certificates</h1>

            <p>
                Enter the Certificate Number and click the "Verify" button.
            </p>

            <p class="verification-note">
                This page verifies ISO / Business Assurance certificates issued and approved in the TÜV Austria BIC BA Certification Management System.
                <br>
                <br>
                For global ISO certification database verification, please visit
                <a href="https://www.iafcertsearch.org/" target="_blank">
                    IAF CertSearch
                </a>.
            </p>
        </div>

        <form id="s-form" method="GET" action="{{ route('certificate.search') }}" class="mb-4">
            <div class="input-group">
                <input type="text"
                       name="search"
                       id="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="Ex: TUV/BA/CERT/2026/001"
                       required>

                <button class="btn btn-primary" type="submit">
                    VERIFY
                </button>
            </div>
        </form>

        @if(request('search'))

            @if(isset($certificate) && $certificate)

                <div class="mb-4">

                    @if($certificate->certificate_status == 'Active')
                        <h3 class="text-success">Certificate Authentic and Valid! ✅</h3>
                        <h6>
                            <center>Please verify the details below:</center>
                        </h6>
                    @elseif(str_contains($certificate->certificate_status ?? '', 'Expired'))
                        <h3 class="text-warning">Certificate Authentic but Expired! ⚠️</h3>
                    @elseif(in_array($certificate->certificate_status, ['Suspended', 'Withdrawn', 'Cancelled']))
                        <h3 class="text-danger">Certificate Authentic but Not Currently Active! ⚠️</h3>
                    @else
                        <h3 class="text-success">Certificate Record Found! ✅</h3>
                        <h6>
                            <center>Please verify the details below:</center>
                        </h6>
                    @endif

                    <table class="table table-bordered mt-3">
                        <tr>
                            <td><strong>Certificate Number</strong></td>
                            <td>{{ $certificate->certificate_number ?? 'N/A' }}</td>
                        </tr>

                        <tr>
                            <td><strong>Client Name</strong></td>
                            <td>{{ $certificate->client->client_name ?? 'N/A' }}</td>
                        </tr>

                        <tr>
                            <td><strong>Client Address</strong></td>
                            <td>{{ $certificate->client->client_address ?? 'N/A' }}</td>
                        </tr>

                        <tr>
                            <td><strong>Certification Standard</strong></td>
                            <td>{{ $certificate->standard->standard_name ?? 'N/A' }}</td>
                        </tr>

                        <tr>
                            <td><strong>Accreditation Body</strong></td>
                            <td>
                                {{ $certificate->accreditationBody->short_name ?? $certificate->accreditationBody->accreditation_body_name ?? 'N/A' }}
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Certificate Scope</strong></td>
                            <td>{{ $certificate->certificate_scope ?? 'N/A' }}</td>
                        </tr>

                        <tr>
                            <td><strong>Certificate Issue Date</strong></td>
                            <td>
                                @if($certificate->certificate_issue_date)
                                    {{ \Carbon\Carbon::parse($certificate->certificate_issue_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Certificate Expiry Date</strong></td>
                            <td>
                                @if($certificate->certificate_expiry_date)
                                    {{ \Carbon\Carbon::parse($certificate->certificate_expiry_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Initial Certification Audit Completion Date</strong></td>
                            <td>
                                @if($certificate->initial_certification_audit_completion_date)
                                    {{ \Carbon\Carbon::parse($certificate->initial_certification_audit_completion_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Surveillance 1 Due Date</strong></td>
                            <td>
                                @if($certificate->surveillance_1_due_date)
                                    {{ \Carbon\Carbon::parse($certificate->surveillance_1_due_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Surveillance 2 Due Date</strong></td>
                            <td>
                                @if($certificate->surveillance_2_due_date)
                                    {{ \Carbon\Carbon::parse($certificate->surveillance_2_due_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Recertification Due Date</strong></td>
                            <td>
                                @if($certificate->recertification_due_date)
                                    {{ \Carbon\Carbon::parse($certificate->recertification_due_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Certificate Status</strong></td>
                            <td>
                                @if($certificate->certificate_status == 'Active')
                                    <span class="badge bg-success">Active</span>
                                @elseif(str_contains($certificate->certificate_status ?? '', 'Expired'))
                                    <span class="badge bg-warning text-dark">{{ $certificate->certificate_status }}</span>
                                @elseif(in_array($certificate->certificate_status, ['Suspended', 'Withdrawn', 'Cancelled']))
                                    <span class="badge bg-danger">{{ $certificate->certificate_status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $certificate->certificate_status ?? 'N/A' }}</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Verification Status</strong></td>
                            <td>
                                <span class="badge bg-success">Approved Record</span>
                            </td>
                        </tr>
                    </table>

                    @if($certificate->certificate_pdf)

                        <div class="text-center mt-3 mb-4">
                            <a href="{{ route('certificate.downloadPdf', $certificate->id) }}" class="btn btn-secondary" target="_blank">
                                <i class="fa-solid fa-file-pdf me-1"></i> Download Certificate PDF
                            </a>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fa-solid fa-file-pdf me-2"></i>
                                    Certificate PDF Preview
                                </span>

                                <small class="text-muted">
                                    If it does not load,
                                    <a href="{{ route('certificate.downloadPdf', $certificate->id) }}" target="_blank">
                                        download
                                    </a>.
                                </small>
                            </div>

                            <div class="card-body p-0" style="height: 75vh;">
                                <iframe
                                    src="{{ route('certificate.viewPdf', $certificate->id) }}"
                                    title="Certificate PDF"
                                    style="width:100%; height:100%; border:0;"
                                    allow="fullscreen"
                                    loading="lazy">
                                </iframe>
                            </div>
                        </div>

                    @else

                        <div class="alert alert-warning text-center">
                            Certificate PDF is not available for download.
                        </div>

                        <div class="alert alert-warning mt-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            No certificate PDF uploaded yet.
                        </div>

                    @endif

                </div>

            @else

                <div class="alert alert-warning text-center">
                    ⚠️ No record of the certificate number you entered can be found in our database. ⚠️
                    <br>
                    Please contact us for further inquiry or clarification.
                    <br>
                    Tel: +88 02 8836403 ; Email: info@tuvat.com.bd
                </div>

            @endif

        @endif

        @include('layouts.footer')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>