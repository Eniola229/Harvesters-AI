{{-- Location: resources/views/admin/programs/edit.blade.php --}}
@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Program</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.programs.index') }}">Programs</a></li>
                    <li class="breadcrumb-item">Edit</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <form method="POST" action="{{ route('admin.programs.update', $program->id) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-xxl-8">
                        <div class="card mb-3">
                            <div class="card-header"><h5 class="card-title">Program Details</h5></div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title', $program->title) }}" required />
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="5">{{ old('description', $program->description) }}</textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Start Date & Time</label>
                                        <input type="datetime-local" name="start_date" class="form-control"
                                               value="{{ old('start_date', $program->start_date ? \Carbon\Carbon::parse($program->start_date)->format('Y-m-d\TH:i') : '') }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">End Date & Time</label>
                                        <input type="datetime-local" name="end_date" class="form-control"
                                               value="{{ old('end_date', $program->end_date ? \Carbon\Carbon::parse($program->end_date)->format('Y-m-d\TH:i') : '') }}" />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="form-label fw-semibold">Location / Venue</label>
                                    <input type="text" name="location" class="form-control"
                                           value="{{ old('location', $program->location) }}" />
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><h5 class="card-title">Extra Details (Metadata)</h5></div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Bus Pickup Locations</label>
                                    <input type="text" name="meta_bus_locations" class="form-control"
                                           value="{{ old('meta_bus_locations', $program->metadata['bus_locations'] ?? '') }}" />
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Free Meal?</label>
                                        <select name="meta_free_meal" class="form-select">
                                            <option value="">— Select —</option>
                                            <option value="yes" {{ old('meta_free_meal', $program->metadata['free_meal'] ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('meta_free_meal', $program->metadata['free_meal'] ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Dress Code</label>
                                        <input type="text" name="meta_dress_code" class="form-control"
                                               value="{{ old('meta_dress_code', $program->metadata['dress_code'] ?? '') }}" />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Registration Required?</label>
                                        <select name="meta_registration" class="form-select">
                                            <option value="">— Select —</option>
                                            <option value="yes" {{ old('meta_registration', $program->metadata['registration'] ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('meta_registration', $program->metadata['registration'] ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Contact Person</label>
                                        <input type="text" name="meta_contact" class="form-control"
                                               value="{{ old('meta_contact', $program->metadata['contact'] ?? '') }}" />
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Additional Info</label>
                                    <textarea name="meta_extra" class="form-control" rows="3">{{ old('meta_extra', $program->metadata['extra'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4">
                        <div class="card mb-3">
                            <div class="card-header"><h5 class="card-title">Program Image</h5></div>
                            <div class="card-body">
                                @if($program->image_url)
                                    <div class="mb-3">
                                        <img src="{{ $program->image_url }}" class="img-fluid rounded" alt="Current image" />
                                        <p class="text-muted fs-12 mt-1">Current image. Upload a new one to replace it.</p>
                                    </div>
                                @endif
                                <input type="file" name="image" class="form-control" accept="image/*" id="imageInput" />
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><h5 class="card-title">Settings</h5></div>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                           {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="isActive">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i> Update Program
                            </button>
                            <a href="{{ route('admin.programs.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

@include('admin.components.footer')