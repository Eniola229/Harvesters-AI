{{-- Location: resources/views/admin/programs/create.blade.php --}}
@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Add Program</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.programs.index') }}">Programs</a></li>
                    <li class="breadcrumb-item">Add Program</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <form method="POST" action="{{ route('admin.programs.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-xxl-8">
                        <div class="card mb-3">
                            <div class="card-header"><h5 class="card-title">Program Details</h5></div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           placeholder="e.g. Next Level Prayers 2025" value="{{ old('title') }}" required />
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="5" placeholder="Describe the program...">{{ old('description') }}</textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Start Date & Time</label>
                                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">End Date & Time</label>
                                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}" />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="form-label fw-semibold">Location / Venue</label>
                                    <input type="text" name="location" class="form-control"
                                           placeholder="e.g. Lekki Campus Main Auditorium" value="{{ old('location') }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Program Metadata -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Extra Details (Metadata)</h5>
                                <small class="text-muted">The AI will use this info when members ask questions</small>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Bus Pickup Locations</label>
                                    <input type="text" name="meta_bus_locations" class="form-control"
                                           placeholder="e.g. CMS Bus Stop, Gbagada Phase 2 junction, Ojota" value="{{ old('metadata.bus_locations') }}" />
                                    <div class="form-text">Separate multiple locations with commas</div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Free Meal Available?</label>
                                        <select name="meta_free_meal" class="form-select">
                                            <option value="">— Select —</option>
                                            <option value="yes" {{ old('metadata.free_meal') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('metadata.free_meal') == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Dress Code</label>
                                        <input type="text" name="meta_dress_code" class="form-control"
                                               placeholder="e.g. All white, Smart casual" value="{{ old('metadata.dress_code') }}" />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Registration Required?</label>
                                        <select name="meta_registration" class="form-select">
                                            <option value="">— Select —</option>
                                            <option value="yes" {{ old('metadata.registration') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('metadata.registration') == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Contact Person</label>
                                        <input type="text" name="meta_contact" class="form-control"
                                               placeholder="e.g. Pastor Mike — 08012345678" value="{{ old('metadata.contact') }}" />
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Additional Info</label>
                                    <textarea name="meta_extra" class="form-control" rows="3"
                                              placeholder="Any other important details...">{{ old('metadata.extra') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4">
                        <div class="card mb-3">
                            <div class="card-header"><h5 class="card-title">Program Image</h5></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Upload Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" id="imageInput" />
                                    <div class="form-text">Will be stored on Cloudinary. Recommended: 1200×628px</div>
                                </div>
                                <div id="imagePreview" class="d-none">
                                    <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" />
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><h5 class="card-title">Settings</h5></div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="isActive">Active (visible to AI)</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i> Save Program
                            </button>
                            <a href="{{ route('admin.programs.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

@push('scripts')
<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush

@include('admin.components.footer')