@extends('layouts.admin')

@section('title', 'View Client')

@section('content')
<div class="page-heading"><div><h1>View Client</h1></div></div>

<section class="admin-card">
    <div class="admin-card-body">

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
</section>
@endsection

