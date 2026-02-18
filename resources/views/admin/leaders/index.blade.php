@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Church Leaders</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Leaders</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.leaders.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i> Add Leader
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
                @forelse($leaders as $leader)
                    <div class="col-xxl-3 col-md-4 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body py-4">
                                @if($leader->image_url)
                                    <img src="{{ $leader->image_url }}" class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;" />
                                @else
                                    <div class="avatar-text avatar-xxl bg-soft-primary text-primary mx-auto mb-3"
                                         style="width:80px;height:80px;font-size:1.5rem;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                                        {{ strtoupper(substr($leader->name, 0, 2)) }}
                                    </div>
                                @endif
                                <h6 class="fw-bold mb-1">{{ $leader->name }}</h6>
                                <p class="text-primary fs-12 mb-1">{{ $leader->title }}</p>
                                <p class="text-muted fs-12 mb-2">{{ $leader->campus->name ?? 'General' }}</p>
                                @if($leader->phone)
                                    <p class="fs-12 mb-3"><i class="feather-phone me-1"></i>{{ $leader->phone }}</p>
                                @endif
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('admin.leaders.edit', $leader->id) }}" class="btn btn-sm btn-light-brand">
                                        <i class="feather-edit-2 me-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.leaders.destroy', $leader->id) }}" onsubmit="return confirm('Delete this leader?')">
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
                                <i class="feather-star fs-2 d-block mb-2"></i>
                                No leaders yet. <a href="{{ route('admin.leaders.create') }}">Add one.</a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')