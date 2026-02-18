@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">New Newsletter</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.newsletters.index') }}">Newsletters</a></li>
                    <li class="breadcrumb-item">New Newsletter</li>
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
                <div class="col-xxl-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Compose Newsletter</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.newsletters.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           placeholder="Newsletter title (for your reference)" value="{{ old('title') }}" required />
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="8"
                                              placeholder="Type your message here... This will be sent via WhatsApp/SMS. Use *bold* for emphasis." required>{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">WhatsApp formatting: *bold*, _italic_</div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Media (Optional)</label>
                                    <input type="file" name="media" class="form-control" accept="image/*,video/*" />
                                    <div class="form-text">Attach an image or video to send with the message. Will be stored on Cloudinary.</div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Send To</label>
                                    <select name="campus_id" class="form-select">
                                        <option value="">🌍 All Members ({{ $totalMembers }} members)</option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                                                📍 {{ $campus->name }} Campus only
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="alert alert-info">
                                    <i class="feather-info me-2"></i>
                                    The newsletter will be saved as a <strong>draft</strong>. You can then click <strong>Send</strong> from the newsletters list to dispatch it.
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Save Draft
                                    </button>
                                    <a href="{{ route('admin.newsletters.index') }}" class="btn btn-light">Cancel</a>
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