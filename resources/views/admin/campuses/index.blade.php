@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Campuses</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Campuses</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.campuses.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i> Add Campus
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
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Campus Name</th>
                                    <th>Address</th>
                                    <th>Service Times</th>
                                    <th>Pastor</th>
                                    <th>Phone</th>
                                    <th>Members</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campuses as $campus)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($campus->image_url)
                                                    <img src="{{ $campus->image_url }}" class="rounded" style="width:40px;height:40px;object-fit:cover;" />
                                                @else
                                                    <div class="avatar-text avatar-md bg-soft-primary text-primary" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                                                        <i class="feather-map-pin"></i>
                                                    </div>
                                                @endif
                                                <span class="fw-bold">{{ $campus->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($campus->address, 40) }}</td>
                                        <td>{{ Str::limit($campus->service_times, 40) }}</td>
                                        <td>{{ $campus->pastor_name ?? '—' }}</td>
                                        <td>{{ $campus->pastor_phone ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary">{{ $campus->members_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if($campus->is_active)
                                                <span class="badge bg-soft-success text-success">Active</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.campuses.edit', $campus->id) }}" class="btn btn-sm btn-light-brand">
                                                    <i class="feather-edit-2"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.campuses.destroy', $campus->id) }}" onsubmit="return confirm('Delete this campus?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger"><i class="feather-trash-2"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="feather-map-pin fs-2 d-block mb-2"></i>
                                            No campuses yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')