@extends('layouts.admin')

@section('title', 'Pending Certificates')

@section('content')
<div class="page-heading">
    <div>
        <h1>Pending Certificates</h1>
        <p>
            @if(($assignment ?? null) === 'review')
                Showing certificates assigned to you for review.
            @elseif(($assignment ?? null) === 'approval')
                Showing certificates assigned to you for approval.
            @elseif(($assignment ?? null) === 'mine')
                Showing all certificates assigned to you.
            @else
                Review and approve certificates in the workflow.
            @endif
        </p>
    </div>
    @canMutate
    <div class="d-flex flex-wrap gap-2">
        <form action="{{ route('bulkReview') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-info btn-sm" data-confirm="Mark all certificates assigned to you for review as Reviewed?">
                <i class="fa-solid fa-thumbs-up me-1"></i> Mark My Assigned as Reviewed
            </button>
        </form>
        <form action="{{ route('bulkApprove') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-sm" data-confirm="Mark all certificates assigned to you for approval as Approved?">
                <i class="fa-solid fa-check-double me-1"></i> Mark My Assigned as Approved
            </button>
        </form>
    </div>
    @endcanMutate
</div>

<div class="filter-chips mb-3">
    <a class="filter-chip {{ empty($assignment) ? 'active' : '' }}" href="{{ route('pendingCertificates') }}">All pending</a>
    <a class="filter-chip {{ ($assignment ?? null) === 'mine' ? 'active' : '' }}" href="{{ route('pendingCertificates', ['assignment' => 'mine']) }}">Assigned to me</a>
    <a class="filter-chip {{ ($assignment ?? null) === 'review' ? 'active' : '' }}" href="{{ route('pendingCertificates', ['assignment' => 'review']) }}">My reviews</a>
    <a class="filter-chip {{ ($assignment ?? null) === 'approval' ? 'active' : '' }}" href="{{ route('pendingCertificates', ['assignment' => 'approval']) }}">My approvals</a>
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>Certificates Pending Review/Approval</h2>
        <div class="toolbar">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input class="form-control search-input" type="search" placeholder="Search certificates">
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
                    <th>Expiry</th>
                    <th>S1 Due</th>
                    <th>S2 Due</th>
                    <th>Recert.</th>
                    <th>Reviewer</th>
                    <th>Approver</th>
                    <th>Status</th>
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
<script>
$(function () {
    var currentUserId = {{ Auth::id() ?? 0 }};
    var csrfToken = @json(csrf_token());
    var viewBase = @json(url('/view-certificate'));
    var editBase = @json(url('/edit-certificate'));
    var deleteBase = @json(url('/delete-certificate'));
    var reviewBase = @json(url('/review-certificate'));
    var approveBase = @json(url('/approve-certificate'));
    var assignmentFilter = @json($assignment ?? null);

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
            url: @json(route('liveSearchPending')),
            data: { userInput: userInput, page: page, assignment: assignmentFilter },
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
                        '<td>' + escapeHtml(d.certificate_number || 'Not Issued') + '</td>' +
                        '<td>' + formatDate(d.certificate_expiry_date) + '</td>' +
                        '<td>' + formatDate(d.surveillance_1_due_date) + '</td>' +
                        '<td>' + formatDate(d.surveillance_2_due_date) + '</td>' +
                        '<td>' + formatDate(d.recertification_due_date) + '</td>' +
                        '<td>' + escapeHtml(d.review_by || 'N/A') + '</td>' +
                        '<td>' + escapeHtml(d.approval_by || 'N/A') + '</td>' +
                        '<td><span class="status-pill">' + escapeHtml(d.status) + '</span></td>' +
                        '<td>' + actions + '</td></tr>';
                });
                $('.search-result tbody').html(html || '<tr><td colspan="13" class="text-center text-muted py-4">No matching certificates found.</td></tr>');
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
        clearTimeout(timer);
        timer = setTimeout(function () { fetchCertificates(1, $('.search-input').val()); }, 250);
    });

    $(document).on('click', '.search-pagination .page-link', function (e) {
        e.preventDefault();
        fetchCertificates($(this).data('page'), $('.search-input').val());
    });

    fetchCertificates();
});
</script>
@endpush
