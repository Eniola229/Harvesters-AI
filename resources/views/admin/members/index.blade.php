@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Church Members</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Members</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i> Add Member
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
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.members.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Name or phone..." value="{{ request('search') }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Campus</label>
                                <select name="campus" class="form-select">
                                    <option value="">All Campuses</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}" {{ request('campus') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Morning Alert</label>
                                <select name="alert" class="form-select">
                                    <option value="">All</option>
                                    <option value="1" {{ request('alert') == '1' ? 'selected' : '' }}>Alert On</option>
                                    <option value="0" {{ request('alert') == '0' ? 'selected' : '' }}>Alert Off</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="feather-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Members <span class="badge bg-primary ms-2">{{ $members->total() }}</span></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Channel</th>
                                    <th>Campus</th>
                                    <th>Morning Alert</th>
                                    <th>Alert Time</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $member)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.members.show', $member->id) }}" class="fw-bold">
                                                {{ $member->name }}
                                            </a>
                                        </td>
                                        <td>{{ $member->phone }}</td>
                                        <td>
                                            @if($member->channel === 'whatsapp')
                                                <span class="badge bg-soft-success text-success"><i class="feather-message-circle me-1"></i>WhatsApp</span>
                                            @else
                                                <span class="badge bg-soft-info text-info"><i class="feather-message-square me-1"></i>SMS</span>
                                            @endif
                                        </td>
                                        <td>{{ $member->campus->name ?? '—' }}</td>
                                        <td>
                                            @if($member->morning_alert)
                                                <span class="badge bg-soft-success text-success"><i class="feather-bell me-1"></i>On</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary"><i class="feather-bell-off me-1"></i>Off</span>
                                            @endif
                                        </td>
                                        <td>{{ $member->alert_time ?? '—' }}</td>
                                        <td>{{ $member->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-sm btn-light-brand" title="View">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-sm btn-light-brand" title="Edit">
                                                    <i class="feather-edit-2"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.members.destroy', $member->id) }}" onsubmit="return confirm('Delete this member?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" title="Delete"><i class="feather-trash-2"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="feather-users fs-2 d-block mb-2"></i>
                                            No members found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($members->hasPages())
                    <div class="card-footer">{{ $members->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')