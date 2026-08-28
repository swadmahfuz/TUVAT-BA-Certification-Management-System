@extends('layouts.admin')

@section('title', 'Edit Client')

@section('content')
<div class="page-heading"><div><h1>Edit Client</h1></div></div>

<section class="admin-card">
    <div class="admin-card-body">

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
</section>
@endsection

