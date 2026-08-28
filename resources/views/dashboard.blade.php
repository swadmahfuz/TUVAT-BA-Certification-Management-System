@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, {{ auth()->user()->name }}.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-success btn-sm" href="{{ route('client.add') }}">
            <i class="fa-solid fa-building me-1"></i> Add Client
        </a>
        <a class="btn btn-primary btn-sm" href="{{ route('certificate.add') }}">
            <i class="fa-solid fa-plus me-1"></i> Add Certificate
        </a>
    </div>
</div>

@if(($myAssignments['total'] ?? 0) > 0)
    <div class="assignment-banner">
        <div>
            <strong>You have {{ $myAssignments['total'] }} certificate{{ $myAssignments['total'] === 1 ? '' : 's' }} waiting on you.</strong>
            <span>{{ $myAssignments['review'] }} for review · {{ $myAssignments['approval'] }} for approval</span>
        </div>
        <a class="btn btn-sm btn-primary" href="{{ route('pendingCertificates', ['assignment' => 'mine']) }}">
            Open my assignments
        </a>
    </div>
@endif

<div class="stats-grid">
    <x-admin.stat-card label="Total Clients" :value="$stats['total_clients']" icon="fa-building" color="cyan" meta="Registered clients" :href="route('clients')" />
    <x-admin.stat-card label="Total Certificates" :value="$stats['total']" icon="fa-file-circle-check" color="blue" meta="All records" />
    <x-admin.stat-card label="Active Certificates" :value="$stats['active_certificates']" icon="fa-check" color="green" meta="Certificate status active" />
    <x-admin.stat-card label="Pending Review" :value="$stats['pending_review']" icon="fa-clock" color="orange" :meta="$percentages['Pending Review'].'% of total'" :href="route('pendingCertificates', ['assignment' => 'review'])" />
    <x-admin.stat-card label="Pending Approval" :value="$stats['pending_approval']" icon="fa-pen" color="purple" :meta="$percentages['Pending Approval'].'% of total'" :href="route('pendingCertificates', ['assignment' => 'approval'])" />
    <x-admin.stat-card
        label="Pending my review"
        :value="$myAssignments['review']"
        icon="fa-user-clock"
        color="orange"
        meta="Assigned to you"
        :href="route('pendingCertificates', ['assignment' => 'review'])"
    />
    <x-admin.stat-card
        label="Pending my approval"
        :value="$myAssignments['approval']"
        icon="fa-user-check"
        color="purple"
        meta="Assigned to you"
        :href="route('pendingCertificates', ['assignment' => 'approval'])"
    />
    <x-admin.stat-card label="Upcoming Surveillance 1" :value="$stats['upcoming_surveillance_1']" icon="fa-calendar-day" color="cyan" meta="Next 90 days" :href="route('upcomingAudits')" />
    <x-admin.stat-card label="Upcoming Surveillance 2" :value="$stats['upcoming_surveillance_2']" icon="fa-calendar-week" color="blue" meta="Next 90 days" :href="route('upcomingAudits')" />
    <x-admin.stat-card label="Upcoming Recertification" :value="$stats['upcoming_recertification']" icon="fa-arrows-rotate" color="orange" meta="Next 90 days" :href="route('upcomingAudits')" />
    <x-admin.stat-card label="Expired Certificates" :value="$stats['expired']" icon="fa-circle-xmark" color="red" :meta="$percentages['Expired'].'% of total'" :href="route('expiredCertificates')" />
    <x-admin.stat-card label="Within Grace Period" :value="$stats['expired_within_grace']" icon="fa-hourglass-half" color="orange" meta="Expired but in grace" />
    <x-admin.stat-card label="Beyond Grace Period" :value="$stats['expired_beyond_grace']" icon="fa-ban" color="red" meta="Grace period ended" />
</div>

<div class="dashboard-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Certificates by Status</h2>
        </div>
        <div class="admin-card-body chart-wrap">
            <canvas id="statusChart"></canvas>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Certificates Issued Over Time</h2>
            <span class="small text-muted">Last 12 months</span>
        </div>
        <div class="admin-card-body chart-wrap">
            <canvas id="monthlyChart"></canvas>
        </div>
    </section>
</div>

<div class="dashboard-bottom">
    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Recent Certificates</h2>
        </div>
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Certificate No.</th>
                        <th>Client</th>
                        <th>Standard</th>
                        <th>Status</th>
                        <th>Issue Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCertificates as $certificate)
                        <tr>
                            <td><a href="{{ route('certificate.view', $certificate->id) }}">{{ $certificate->certificate_number ?: 'Not Issued' }}</a></td>
                            <td>{{ $certificate->client->client_name ?? 'N/A' }}</td>
                            <td>{{ $certificate->standard->standard_name ?? 'N/A' }}</td>
                            <td><x-admin.status-badge :status="$certificate->status" /></td>
                            <td>{{ $certificate->certificate_issue_date ? \Carbon\Carbon::parse($certificate->certificate_issue_date)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No certificates available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Recent Activities</h2>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('activity-log.index') }}">View all</a>
        </div>
        <div class="admin-card-body">
            <ul class="activity-list">
                @forelse($recentActivities as $activity)
                    <li class="activity-item">
                        <span class="activity-dot"><i class="fa-solid fa-clock-rotate-left"></i></span>
                        <div class="activity-text">
                            <p>{{ $activity->description }}</p>
                            <time>{{ $activity->created_at->diffForHumans() }}</time>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-muted py-4">Activity will appear here as actions are recorded.</li>
                @endforelse
            </ul>
        </div>
    </section>
</div>

<section class="admin-card mt-3">
    <div class="admin-card-header">
        <h2>All BA Certification Records</h2>
        <div class="toolbar">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input class="form-control search-input" type="search" placeholder="Search certificates, clients, standards…">
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover admin-table search-result">
            <thead>
                <tr>
                    <th>Sl.</th>
                    <th>Client</th>
                    <th>Standard</th>
                    <th>Accreditation</th>
                    <th>Certificate No.</th>
                    <th>Issue</th>
                    <th>Expiry</th>
                    <th>S1 Due</th>
                    <th>S2 Due</th>
                    <th>Recert.</th>
                    <th>Cert. Status</th>
                    <th>Workflow</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="p-3 border-top search-pagination">{{ $certificates->links() }}</div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json($statusChart['labels']),
            datasets: [{
                data: @json($statusChart['values']),
                backgroundColor: ['#19aa71', '#ff9800', '#7449bd', '#e83b4f'],
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '66%',
            plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } } }
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: @json($monthlyChart['labels']),
            datasets: [{
                label: 'Certificates',
                data: @json($monthlyChart['values']),
                borderColor: '#1976d2',
                backgroundColor: 'rgba(25, 118, 210, .10)',
                fill: true,
                tension: .3,
                pointRadius: 3
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
        }
    });
});

$(function () {
    var currentUserId = {{ Auth::id() ?? 0 }};
    var csrfToken = @json(csrf_token());
    var viewBase = @json(url('/view-certificate'));
    var editBase = @json(url('/edit-certificate'));
    var deleteBase = @json(url('/delete-certificate'));
    var reviewBase = @json(url('/review-certificate'));
    var approveBase = @json(url('/approve-certificate'));

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function formatDate(date) {
        if (!date) return 'N/A';
        var d = new Date(date);
        if (isNaN(d.getTime())) return 'N/A';
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
    }

    function postButton(url, title, iconClass, confirmMsg, danger, method) {
        return '<form action="' + url + '" method="POST" class="d-inline">' +
            '<input type="hidden" name="_token" value="' + csrfToken + '">' +
            (method ? '<input type="hidden" name="_method" value="' + method + '">' : '') +
            '<button type="submit" class="' + (danger ? 'danger' : '') + '" title="' + title + '" data-confirm="' + confirmMsg + '">' +
            '<i class="' + iconClass + '"></i></button></form>';
    }

    function fetchCertificates(page, userInput) {
        page = page || 1;
        userInput = userInput || '';
        $.ajax({
            url: @json(route('liveSearch')),
            data: { userInput: userInput, page: page },
            dataType: 'json',
            beforeSend: function () {
                $('.search-result tbody').html('<tr><td colspan="13" class="text-center text-muted py-4">Searching...</td></tr>');
            },
            success: function (res) {
                var html = '';
                $.each(res.data.data, function (i, d) {
                    var clientName = d.client ? d.client.client_name : 'N/A';
                    var standardName = d.standard ? d.standard.standard_name : 'N/A';
                    var accreditationName = d.accreditation_body ? (d.accreditation_body.short_name || d.accreditation_body.accreditation_body_name || 'N/A') : 'N/A';
                    var certNo = d.certificate_number || 'Not Issued';
                    var canReview = d.status === 'Pending Review' && Number(d.review_by_id) === Number(currentUserId);
                    var canApprove = d.status === 'Pending Approval' && Number(d.approval_by_id) === Number(currentUserId);
                    var actions = '<div class="table-actions">' +
                        '<a href="' + viewBase + '/' + d.id + '" target="_blank" title="View"><i class="fa-solid fa-circle-info"></i></a>' +
                        '<a href="' + editBase + '/' + d.id + '" target="_blank" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>' +
                        postButton(deleteBase + '/' + d.id, 'Delete', 'fa-solid fa-trash', 'Delete this certificate?', true, 'DELETE') +
                        (canReview ? postButton(reviewBase + '/' + d.id, 'Review', 'fa-solid fa-thumbs-up', 'Mark as reviewed?') : '') +
                        (canApprove ? postButton(approveBase + '/' + d.id, 'Approve', 'fa-solid fa-check', 'Mark as approved?') : '') +
                        '</div>';

                    html += '<tr>' +
                        '<td>' + (i + 1 + (res.data.current_page - 1) * res.data.per_page) + '</td>' +
                        '<td>' + escapeHtml(clientName) + '</td>' +
                        '<td>' + escapeHtml(standardName) + '</td>' +
                        '<td>' + escapeHtml(accreditationName) + '</td>' +
                        '<td>' + escapeHtml(certNo) + '</td>' +
                        '<td>' + formatDate(d.certificate_issue_date) + '</td>' +
                        '<td>' + formatDate(d.certificate_expiry_date) + '</td>' +
                        '<td>' + formatDate(d.surveillance_1_due_date) + '</td>' +
                        '<td>' + formatDate(d.surveillance_2_due_date) + '</td>' +
                        '<td>' + formatDate(d.recertification_due_date) + '</td>' +
                        '<td>' + escapeHtml(d.certificate_status || 'N/A') + '</td>' +
                        '<td><span class="status-pill">' + escapeHtml(d.status) + '</span></td>' +
                        '<td>' + actions + '</td></tr>';
                });
                $('.search-result tbody').html(html || '<tr><td colspan="13" class="text-center text-muted py-4">No matching records found.</td></tr>');
                $('.search-pagination').html(generatePaginationLinks(res.data));
            }
        });
    }

    function generatePaginationLinks(data) {
        var links = '<nav><ul class="pagination mb-0">';
        if (data.current_page > 1) {
            links += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">&laquo;</a></li>';
        }
        for (var i = 1; i <= data.last_page; i++) {
            links += '<li class="page-item' + (i === data.current_page ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }
        if (data.current_page < data.last_page) {
            links += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">&raquo;</a></li>';
        }
        return links + '</ul></nav>';
    }

    var timer;
    $('.search-input').on('input', function () {
        var query = this.value;
        clearTimeout(timer);
        timer = setTimeout(function () { fetchCertificates(1, query); }, 250);
    });

    $(document).on('click', '.search-pagination .page-link', function (e) {
        e.preventDefault();
        fetchCertificates($(this).data('page'), $('.search-input').val());
    });

    fetchCertificates();
});
</script>
@endpush
