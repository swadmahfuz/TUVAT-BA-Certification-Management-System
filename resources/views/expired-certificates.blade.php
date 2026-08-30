@extends('layouts.admin')

@section('title', 'Expired Certificates')

@section('content')
<div class="page-heading">
    <div>
        <h1>{{ $filterLabels['title'] ?? 'Expired Certificates' }}</h1>
        <p>{{ $filterLabels['subtitle'] ?? 'Certificates with an expiry date in the past.' }}</p>
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-body">

@if(($filter ?? 'expired') === 'expired')
<div class="alert alert-danger" style="font-size: 12px;">
                This page shows BA certificates where the certificate expiry date has already passed.
                Records may still be within the 6-month grace period or beyond the grace period.
            </div>
@endif

            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <th colspan="14" style="text-align: center; font-weight: bold; font-size: 1.5em;">
                            {{ $filterLabels['title'] ?? 'Expired Certificate Records' }}
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

                                    @canMutate
                                    <a href="{{ route('certificate.edit', $certificate->id) }}" target="_blank">
                                        <i class="fa-solid fa-pen-to-square" title="Edit Certificate"></i>
                                    </a>
                                    @endcanMutate

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
                                    No matching BA certificate records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div class="p-3 border-top">{{ $certificates->links() }}</div>
</section>
@endsection

