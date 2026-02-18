@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Leader</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.leaders.index') }}">Leaders</a></li>
                    <li class="breadcrumb-item">Edit {{ $leader->name }}</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8">

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

                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Edit — {{ $leader->name }}</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.leaders.update', $leader->id) }}" enctype="multipart/form-data">
                                @csrf @method('PUT')

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $leader->name) }}" required />
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Title / Role <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title', $leader->title) }}" required />
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $leader->phone) }}" />
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $leader->email) }}" />
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Campus</label>
                                    <select name="campus_id" class="form-select @error('campus_id') is-invalid @enderror">
                                        <option value="">— General / All Campuses —</option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('campus_id', $leader->campus_id) == $campus->id ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('campus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Bio / Description</label>
                                    <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3">{{ old('bio', $leader->bio) }}</textarea>
                                    @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Photo</label>
                                    @if($leader->photo_url)
                                        <div class="mb-2 d-flex align-items-center gap-3">
                                            <img src="{{ $leader->photo_url }}" class="rounded-circle" style="width:60px;height:60px;object-fit:cover;" />
                                            <p class="text-muted fs-12 mb-0">Upload a new photo to replace the current one</p>
                                        </div>
                                    @endif
                                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*" />
                                    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Max 5MB. Will be stored on Cloudinary.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Display Order</label>
                                    {{-- name="order" matches the DB column --}}
                                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror"
                                           value="{{ old('order', $leader->order) }}" min="0" />
                                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Lower number = displayed first</div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Update Leader
                                    </button>
                                    <a href="{{ route('admin.leaders.index') }}" class="btn btn-light">Cancel</a>
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