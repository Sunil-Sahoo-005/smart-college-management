@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $salaryStructure->exists ? 'Edit Salary Structure' : 'Create Salary Structure' }}</h5>
                        <small class="text-muted">Define a reusable salary template for staff.</small>
                    </div>
                    <a href="{{ route('hr.salary-structures.index', $tenantKey) }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="POST" action="{{ $salaryStructure->exists ? route('hr.salary-structures.update', [$tenantKey, $salaryStructure]) : route('hr.salary-structures.store', $tenantKey) }}">
                            @csrf
                            @if($salaryStructure->exists)
                                @method('PUT')
                            @endif

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $salaryStructure->name) }}" required>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $salaryStructure->exists ? $salaryStructure->is_active : true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_active">Active</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Optional description">{{ old('description', $salaryStructure->description) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Components JSON</label>
                                    @php
                                        $componentsValue = old('components_json');
                                        if ($componentsValue === null) {
                                            $componentsValue = $salaryStructure->components ? json_encode($salaryStructure->components, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
                                        }
                                    @endphp
                                    <textarea name="components_json" class="form-control font-monospace" rows="10" placeholder='[{"key":"basic","label":"Basic","type":"earning","calc":"fixed","value":50}]'>{{ $componentsValue }}</textarea>
                                    <div class="form-text">
                                        Example: <code>[{"key":"basic","label":"Basic","type":"earning","calc":"fixed","value":50}]</code>
                                    </div>
                                    @error('components_json')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="{{ route('hr.salary-structures.index', $tenantKey) }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
