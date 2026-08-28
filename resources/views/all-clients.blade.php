@extends('layouts.admin')

@section('title', 'All Clients')

@section('content')
<div class="page-heading">
    <div>
        <h1>All BA Clients</h1>
        <p>Manage client organizations and their certification records.</p>
    </div>
    @canMutate
    <a class="btn btn-success btn-sm" href="{{ route('client.add') }}">
        <i class="fa-solid fa-plus me-1"></i> Add Client
    </a>
    @endcanMutate
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>Client List</h2>
        <form class="toolbar" method="GET" action="{{ route('clients') }}">
            <input class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Search clients">
            <button class="btn btn-primary btn-sm" type="submit">Search</button>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('clients') }}">Reset</a>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover admin-table">
            <thead>
                <tr>
                    <th>Sl.</th>
                    <th>Client Name</th>
                    <th>Address</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Certificates</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $offset = ($clients->currentPage() - 1) * $clients->perPage();
                @endphp
                @forelse($clients as $client)
                    <tr>
                        <td>{{ $loop->iteration + $offset }}</td>
                        <td><strong>{{ $client->client_name }}</strong></td>
                        <td>{{ $client->client_address ?? 'N/A' }}</td>
                        <td>{{ $client->contact_person ?? 'N/A' }}</td>
                        <td>
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $client->phone ?? 'N/A' }}</td>
                        <td><span class="status-pill status-secondary">{{ $client->certificates_count ?? 0 }}</span></td>
                        <td>{{ $client->remarks ?? 'N/A' }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('client.view', $client->id) }}" target="_blank" title="View"><i class="fa-solid fa-circle-info"></i></a>
                                <a href="{{ route('client.edit', $client->id) }}" target="_blank" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="{{ route('certificate.addForClient', $client->id) }}" title="Add certificate"><i class="fa-solid fa-plus"></i></a>
                                <form action="{{ route('client.delete', $client->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger" title="Delete" data-confirm="Delete this client? Clients with certificates cannot be deleted.">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No BA clients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $clients->links() }}</div>
</section>
@endsection
