@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Add Leader</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.leaders.index') }}">Leaders</a></li>
                    <li class="breadcrumb-item">Add Leader</li>
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
                        <div class="card-header"><h5 class="card-title">Leader Details</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.leaders.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           placeholder="e.g. Pastor Bolaji Idowu" value="{{ old('name') }}" required />
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Title / Role <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           placeholder="e.g. Senior Pastor, Founder" value="{{ old('title') }}" required />
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="+2348012345678" value="{{ old('phone') }}" />
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           placeholder="leader@harvesters.org" value="{{ old('email') }}" />
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Campus</label>
                                    <select name="campus_id" class="form-select @error('campus_id') is-invalid @enderror">
                                        <option value="">— General / All Campuses —</option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('campus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Bio / Description</label>
                                    <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3"
                                              placeholder="Brief description of the leader's role and ministry...">{{ old('bio') }}</textarea>
                                    @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Photo</label>
                                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*" />
                                    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Max 5MB. Will be stored on Cloudinary.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Display Order</label>
                                    {{-- name="order" matches the DB column --}}
                                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror"
                                           value="{{ old('order', 0) }}" min="0" />
                                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Lower number = displayed first</div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Save Leader
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