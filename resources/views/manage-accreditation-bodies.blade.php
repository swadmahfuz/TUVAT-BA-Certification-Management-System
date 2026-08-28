@extends('layouts.admin')

@section('title', 'Accreditation Bodies')

@section('content')
<div class="page-heading"><div><h1>Accreditation Bodies</h1></div></div>

<section class="admin-card">
    <div class="admin-card-body">

<div class="section-title">Add New Accreditation Body</div>

            <form method="POST" action="{{ route('accreditationBody.create') }}">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="accreditation_body_name">Accreditation Body Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="accreditation_body_name"
                               id="accreditation_body_name"
                               class="form-control"
                               value="{{ old('accreditation_body_name') }}"
                               placeholder="Example: United Kingdom Accreditation Service"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label for="short_name">Short Name</label>
                        <input type="text"
                               name="short_name"
                               id="short_name"
                               class="form-control"
                               value="{{ old('short_name') }}"
                               placeholder="Example: EGAC / UKAS / DAkkS">
                    </div>

                    <div class="col-md-2">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-plus me-1"></i> Add
                        </button>
                    </div>
                </div>
            </form>

            <div class="section-title">Existing Accreditation Bodies</div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sl.</th>
                        <th>Accreditation Body Name</th>
                        <th>Short Name</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Update</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($accreditationBodies as $body)
                        <tr>
                            <form method="POST" action="{{ route('accreditationBody.update') }}">
                                @csrf

                                <input type="hidden" name="id" value="{{ $body->id }}">

                                <td>{{ $loop->iteration }}.</td>

                                <td>
                                    <input type="text"
                                           name="accreditation_body_name"
                                           class="form-control"
                                           value="{{ $body->accreditation_body_name }}"
                                           required>
                                </td>

                                <td>
                                    <input type="text"
                                           name="short_name"
                                           class="form-control"
                                           value="{{ $body->short_name }}">
                                </td>

                                <td>
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ $body->status == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ $body->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>

                                <td>
                                    {{ $body->created_at ? \Carbon\Carbon::parse($body->created_at)->format('d-m-Y h:i A') : 'N/A' }}
                                </td>

                                <td>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No accreditation bodies found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</section>
@endsection

