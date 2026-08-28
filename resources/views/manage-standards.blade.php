@extends('layouts.admin')

@section('title', 'Standards')

@section('content')
<div class="page-heading"><div><h1>Standards</h1></div></div>

<section class="admin-card">
    <div class="admin-card-body">

<div class="section-title">Add New Standard</div>

            <form method="POST" action="{{ route('standard.create') }}">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="standard_name">Standard Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="standard_name"
                               id="standard_name"
                               class="form-control"
                               value="{{ old('standard_name') }}"
                               placeholder="Example: ISO 9001:2015"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label for="standard_code">Standard Code</label>
                        <input type="text"
                               name="standard_code"
                               id="standard_code"
                               class="form-control"
                               value="{{ old('standard_code') }}"
                               placeholder="Example: QMS / EMS / OHSMS">
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

            <div class="section-title">Existing Standards</div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sl.</th>
                        <th>Standard Name</th>
                        <th>Standard Code</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Update</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($standards as $standard)
                        <tr>
                            <form method="POST" action="{{ route('standard.update') }}">
                                @csrf

                                <input type="hidden" name="id" value="{{ $standard->id }}">

                                <td>{{ $loop->iteration }}.</td>

                                <td>
                                    <input type="text"
                                           name="standard_name"
                                           class="form-control"
                                           value="{{ $standard->standard_name }}"
                                           required>
                                </td>

                                <td>
                                    <input type="text"
                                           name="standard_code"
                                           class="form-control"
                                           value="{{ $standard->standard_code }}">
                                </td>

                                <td>
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ $standard->status == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ $standard->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>

                                <td>
                                    {{ $standard->created_at ? \Carbon\Carbon::parse($standard->created_at)->format('d-m-Y h:i A') : 'N/A' }}
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
                                No standards found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</section>
@endsection

