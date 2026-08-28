@extends('layouts.admin')

@section('title', 'Upcoming Audits')

@section('content')
<div class="page-heading"><div><h1>Upcoming Audits</h1></div></div>

<section class="admin-card">
    <div class="admin-card-body">

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

        <div class="p-3 border-top">{{ $certificates->links() }}</div>
</section>
@endsection

