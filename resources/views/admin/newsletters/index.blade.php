@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Newsletters</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Newsletters</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.newsletters.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i> New Newsletter
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
                                    <th>Title</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                    <th>Sent To</th>
                                    <th>Media</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($newsletters as $newsletter)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $newsletter->title }}</div>
                                            <small class="text-muted">{{ Str::limit($newsletter->message, 60) }}</small>
                                        </td>
                                        <td>
                                            @if($newsletter->campus_id)
                                                <span class="badge bg-soft-info text-info">
                                                    <i class="feather-map-pin me-1"></i>{{ $newsletter->campus->name ?? 'Campus' }}
                                                </span>
                                            @else
                                                <span class="badge bg-soft-primary text-primary">
                                                    <i class="feather-users me-1"></i>All Members
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($newsletter->status === 'sent')
                                                <span class="badge bg-soft-success text-success"><i class="feather-check me-1"></i>Sent</span>
                                            @elseif($newsletter->status === 'sending')
                                                <span class="badge bg-soft-warning text-warning"><i class="feather-loader me-1"></i>Sending...</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary"><i class="feather-clock me-1"></i>Draft</span>
                                            @endif
                                        </td>
                                        <td>{{ $newsletter->sent_count ?? 0 }} members</td>
                                        <td>
                                            @if($newsletter->media_url)
                                                <span class="badge bg-soft-info text-info"><i class="feather-image me-1"></i>Yes</span>
                                            @else
                                                <span class="text-muted">Text only</span>
                                            @endif
                                        </td>
                                        <td>{{ $newsletter->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @if($newsletter->status === 'draft')
                                                    <form method="POST" action="{{ route('admin.newsletters.send', $newsletter->id) }}"
                                                          onsubmit="return confirm('Send this newsletter to {{ $newsletter->campus_id ? ($newsletter->campus->name ?? "campus") : "all members" }}?')">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success" title="Send Now">
                                                            <i class="feather-send me-1"></i> Send
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.newsletters.destroy', $newsletter->id) }}"
                                                      onsubmit="return confirm('Delete this newsletter?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="feather-send fs-2 d-block mb-2"></i>
                                            No newsletters yet. <a href="{{ route('admin.newsletters.create') }}">Create one.</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($newsletters->hasPages())
                    <div class="card-footer">{{ $newsletters->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')