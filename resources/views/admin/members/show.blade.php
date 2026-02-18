
@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Member Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.members.index') }}">Members</a></li>
                    <li class="breadcrumb-item">{{ $member->name }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-primary">
                    <i class="feather-edit-2 me-2"></i> Edit Member
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
                <!-- Member Info -->
                <div class="col-xxl-4">
                    <div class="card mb-3">
                        <div class="card-body text-center py-5">
                            <div class="avatar-text avatar-xxl bg-soft-primary text-primary mx-auto mb-3" style="width:80px;height:80px;font-size:2rem;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                            <h4 class="fw-bold mb-1">{{ $member->name }}</h4>
                            <p class="text-muted mb-3">{{ $member->phone }}</p>
                            @if($member->channel === 'whatsapp')
                                <span class="badge bg-soft-success text-success fs-12"><i class="feather-message-circle me-1"></i>WhatsApp</span>
                            @else
                                <span class="badge bg-soft-info text-info fs-12"><i class="feather-message-square me-1"></i>SMS</span>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Member Info</h5></div>
                        <div class="card-body">
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="fs-12 text-muted d-block mb-1">Campus</span>
                                <span class="fw-semibold">{{ $member->campus->name ?? 'Not assigned' }}</span>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="fs-12 text-muted d-block mb-1">Morning Alert</span>
                                @if($member->morning_alert)
                                    <span class="badge bg-soft-success text-success"><i class="feather-bell me-1"></i>Active</span>
                                @else
                                    <span class="badge bg-soft-secondary text-secondary"><i class="feather-bell-off me-1"></i>Off</span>
                                @endif
                            </div>
                            @if($member->morning_alert && $member->alert_time)
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="fs-12 text-muted d-block mb-1">Alert Time</span>
                                <span class="fw-semibold">{{ $member->alert_time }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="fs-12 text-muted d-block mb-1">Joined</span>
                                <span class="fw-semibold">{{ $member->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversation History -->
                <div class="col-xxl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Conversation History</h5>
                            <span class="badge bg-primary">{{ $conversations->total() }} Conversations</span>
                        </div>
                        <div class="card-body p-0">
                            @forelse($conversations as $conversation)
                                <div class="border-bottom p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-soft-info text-info">{{ ucfirst($conversation->channel) }}</span>
                                        <small class="text-muted">{{ $conversation->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    @foreach($conversation->messages->take(4) as $message)
                                        <div class="mb-2 {{ $message->role === 'user' ? 'text-end' : '' }}">
                                            <div class="d-inline-block px-3 py-2 rounded-3 {{ $message->role === 'user' ? 'bg-primary text-white' : 'bg-light' }}" style="max-width:85%;">
                                                <small class="d-block fw-bold mb-1 {{ $message->role === 'user' ? 'text-white-50' : 'text-muted' }}">
                                                    {{ $message->role === 'user' ? $member->name : '🤖 Harvesters AI' }}
                                                </small>
                                                <p class="mb-0 fs-13">{{ Str::limit($message->content, 150) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($conversation->messages->count() > 4)
                                        <p class="text-muted text-center fs-12 mt-2">... {{ $conversation->messages->count() - 4 }} more messages</p>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="feather-message-circle fs-2 d-block mb-2"></i>
                                    No conversations yet
                                </div>
                            @endforelse
                        </div>
                        @if($conversations->hasPages())
                            <div class="card-footer">{{ $conversations->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')