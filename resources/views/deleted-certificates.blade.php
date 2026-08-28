@extends('layouts.admin')

@section('title', 'Deleted Certificates')

@section('content')
<div class="page-heading"><div><h1>Deleted Certificates</h1></div></div>

<section class="admin-card">
    <div class="admin-card-body">

<div class="alert alert-warning" style="font-size: 12px;">
                This page shows soft-deleted BA certificate records. These records can be restored if required.
            </div>

            <div class="table-container">
                <table class="table table-striped search-result">
                    <thead>
                        <th colspan="14" style="text-align: center; font-weight: bold; font-size: 1.5em;">
                            Deleted Certificate Records
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
                            <th>Status</th>
                            <th>Deleted By</th>
                            <th>Deleted At</th>
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

                                <td>{{ $certificate->status ?? 'N/A' }}</td>

                                <td>{{ $certificate->deleted_by ?? 'N/A' }}</td>

                                <td>
                                    {{ $certificate->deleted_at ? \Carbon\Carbon::parse($certificate->deleted_at)->format('d-m-Y h:i A') : 'N/A' }}
                                </td>

                                <td class="action-icons">
                                    <form action="{{ route('certificate.restore', $certificate->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="border-0 bg-transparent p-0" data-confirm="Restore this certificate record?">
                                            <i class="fa-solid fa-rotate-left" title="Restore Certificate"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center">
                                    No deleted BA certificate records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <div class="p-3 border-top">{{ $certificates->links() }}</div>
    </div>
</section>
@endsection

