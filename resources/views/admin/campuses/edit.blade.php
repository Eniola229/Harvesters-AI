@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Campus</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.campuses.index') }}">Campuses</a></li>
                    <li class="breadcrumb-item">Edit {{ $campus->name }}</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="feather-alert-circle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
            <div class="row justify-content-center">
                <div class="col-xxl-7 col-xl-9">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Edit — {{ $campus->name }}</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.campuses.update', $campus->id) }}" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Campus Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $campus->name) }}" required />
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Address</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $campus->address) }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Service Times</label>
                                    <input type="text" name="service_times" class="form-control"
                                           value="{{ old('service_times', $campus->service_times) }}" />
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Campus Pastor</label>
                                        <input type="text" name="pastor_name" class="form-control"
                                               value="{{ old('pastor_name', $campus->pastor_name) }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Pastor Phone</label>
                                        <input type="text" name="pastor_phone" class="form-control"
                                               value="{{ old('pastor_phone', $campus->pastor_phone) }}" />
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Campus Image</label>
                                    @if($campus->image_url)
                                        <div class="mb-2">
                                            <img src="{{ $campus->image_url }}" class="img-fluid rounded" style="max-height:120px;" />
                                            <p class="text-muted fs-12 mt-1">Upload a new image to replace</p>
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control" accept="image/*" />
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                               {{ old('is_active', $campus->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="isActive">Active</label>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Update Campus
                                    </button>
                                    <a href="{{ route('admin.campuses.index') }}" class="btn btn-light">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')