@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Programs & Events</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Programs</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i> Add Program
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="feather-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="main-content">
            <div class="row">
                @forelse($programs as $program)
                    <div class="col-xxl-4 col-md-6">
                        <div class="card">
                            @if($program->image_url)
                                <img src="{{ $program->image_url }}" class="card-img-top" style="height:200px;object-fit:cover;" alt="{{ $program->title }}" />
                            @else
                                <div class="bg-soft-primary d-flex align-items-center justify-content-center" style="height:200px;">
                                    <i class="feather-calendar text-primary" style="font-size:3rem;"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0">{{ $program->title }}</h5>
                                    @if($program->is_active)
                                        <span class="badge bg-soft-success text-success">Active</span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary">Inactive</span>
                                    @endif
                                </div>
                                <p class="text-muted fs-13 mb-3">{{ Str::limit($program->description, 100) }}</p>
                                @if($program->start_date)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="feather-calendar text-primary me-2"></i>
                                        <span class="fs-13">{{ \Carbon\Carbon::parse($program->start_date)->format('M d, Y') }}</span>
                                    </div>
                                @endif
                                @if($program->location)
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="feather-map-pin text-danger me-2"></i>
                                        <span class="fs-13">{{ $program->location }}</span>
                                    </div>
                                @endif
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.programs.edit', $program->id) }}" class="btn btn-sm btn-primary">
                                        <i class="feather-edit-2 me-1"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.programs.destroy', $program->id) }}" onsubmit="return confirm('Delete this program?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="feather-trash-2"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5 text-muted">
                                <i class="feather-calendar fs-2 d-block mb-2"></i>
                                <p class="mb-0">No programs yet. <a href="{{ route('admin.programs.create') }}">Add the first one.</a></p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            @if($programs->hasPages())
                <div class="mt-3">{{ $programs->links() }}</div>
            @endif
        </div>
    </div>
</main>

@include('admin.components.footer')