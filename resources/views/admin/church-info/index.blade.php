@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">AI Knowledge Base</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">AI Knowledge Base</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.church-info.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i> Add Entry
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
            <div class="alert alert-info mb-3">
                <i class="feather-info me-2"></i>
                <strong>How this works:</strong> These entries are injected into the AI's system prompt, so it can answer member questions accurately. Keep entries clear and factual.
            </div>

            @foreach($infos->groupBy('category') as $category => $entries)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            @php
                                $icons = ['about'=>'feather-info','values'=>'feather-heart','faq'=>'feather-help-circle','services'=>'feather-clock','giving'=>'feather-gift','contact'=>'feather-phone','nlp'=>'feather-zap'];
                            @endphp
                            <i class="{{ $icons[$category] ?? 'feather-book-open' }} me-2"></i>
                            {{ ucfirst($category) }}
                        </h5>
                        <span class="badge bg-primary">{{ $entries->count() }} entries</span>
                    </div>
                    <div class="card-body p-0">
                         <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content Preview</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entries as $info)
                                        <tr>
                                            <td class="fw-semibold">{{ $info->title }}</td>
                                            <td class="text-muted fs-13">{{ Str::limit($info->content, 100) }}</td>
                                            <td>
                                                @if($info->is_active)
                                                    <span class="badge bg-soft-success text-success">Active</span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary">Hidden</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.church-info.edit', $info->id) }}" class="btn btn-sm btn-light-brand">
                                                        <i class="feather-edit-2"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.church-info.destroy', $info->id) }}" onsubmit="return confirm('Delete this entry?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-danger"><i class="feather-trash-2"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($infos->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="feather-book-open fs-2 d-block mb-2"></i>
                        No knowledge base entries yet. <a href="{{ route('admin.church-info.create') }}">Add one.</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

@include('admin.components.footer')