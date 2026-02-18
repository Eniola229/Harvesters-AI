@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Knowledge Entry</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.church-info.index') }}">AI Knowledge Base</a></li>
                    <li class="breadcrumb-item">Edit</li>
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
                        <div class="card-header"><h5 class="card-title">Edit — {{ $churchInfo->title }}</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.church-info.update', $churchInfo->id) }}">
                                @csrf @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                        <option value="about" {{ old('category', $churchInfo->category) == 'about' ? 'selected' : '' }}>About the Church</option>
                                        <option value="values" {{ old('category', $churchInfo->category) == 'values' ? 'selected' : '' }}>Church Values</option>
                                        <option value="faq" {{ old('category', $churchInfo->category) == 'faq' ? 'selected' : '' }}>FAQs</option>
                                        <option value="services" {{ old('category', $churchInfo->category) == 'services' ? 'selected' : '' }}>Services & Programs</option>
                                        <option value="giving" {{ old('category', $churchInfo->category) == 'giving' ? 'selected' : '' }}>Giving & Tithing</option>
                                        <option value="contact" {{ old('category', $churchInfo->category) == 'contact' ? 'selected' : '' }}>Contact Info</option>
                                        <option value="nlp" {{ old('category', $churchInfo->category) == 'nlp' ? 'selected' : '' }}>NLP (Next Level Prayers)</option>
                                    </select>
                                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title', $churchInfo->title) }}" required />
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="10" required>{{ old('content', $churchInfo->content) }}</textarea>
                                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                               {{ old('is_active', $churchInfo->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="isActive">Active (AI will use this)</label>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Update Entry
                                    </button>
                                    <a href="{{ route('admin.church-info.index') }}" class="btn btn-light">Cancel</a>
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