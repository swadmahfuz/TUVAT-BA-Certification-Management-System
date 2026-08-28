@extends('layouts.admin')

@section('title', 'Add Client')

@section('content')
<div class="page-heading">
    <div>
        <h1>Add BA Client</h1>
        <p>Register a new client organization.</p>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('clients') }}">
        <i class="fa-solid fa-list me-1"></i> All Clients
    </a>
</div>

<section class="admin-card">
    <div class="admin-card-header"><h2>Client Details</h2></div>
    <div class="admin-card-body">
        <form method="POST" action="{{ route('client.create') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="client_name">Client Name <span class="text-danger">*</span></label>
                    <input type="text" name="client_name" id="client_name" class="form-control" value="{{ old('client_name') }}" placeholder="Enter client/company name" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="client_address">Client Address</label>
                    <textarea name="client_address" id="client_address" class="form-control" rows="3" placeholder="Enter client address">{{ old('client_address') }}</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="contact_person">Contact Person</label>
                    <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person') }}">
                </div>
                <div class="col-md-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-4">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="remarks">Remarks</label>
                    <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk me-1"></i> Save Client</button>
                <a href="{{ route('clients') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
