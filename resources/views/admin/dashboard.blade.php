@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Dashboard</li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="feather-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="main-content">
            <div class="row">

                <!-- STAT CARDS -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-primary-subtle">
                                        <i class="feather-users text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_members']) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Total Members</h3>
                                    </div>
                                </div>
                                <a href="{{ route('admin.members.create') }}">
                                    <i class="feather-plus-circle fs-4 text-primary"></i>
                                </a>
                            </div>
                            <div class="pt-4 border-top">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-12 fw-medium text-muted">Active Members</span>
                                    <span class="fs-12 text-success fw-bold">{{ $stats['active_members'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-warning-subtle">
                                        <i class="feather-bell text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['alert_members']) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">NLP Alert Subscribers</h3>
                                    </div>
                                </div>
                                <a href="{{ route('admin.members.index', ['alert' => 1]) }}">
                                    <i class="feather-more-vertical"></i>
                                </a>
                            </div>
                            <div class="pt-4 border-top">
                                @php $pct = $stats['total_members'] > 0 ? round($stats['alert_members']/$stats['total_members']*100) : 0; @endphp
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fs-12 fw-medium text-muted">Opt-in Rate</span>
                                    <span class="fs-12 text-warning fw-bold">{{ $pct }}%</span>
                                </div>
                                <div class="progress mt-2 ht-3">
                                    <div class="progress-bar bg-warning" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-success-subtle">
                                        <i class="feather-calendar text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ $stats['upcoming_programs'] }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Upcoming Programs</h3>
                                    </div>
                                </div>
                                <a href="{{ route('admin.programs.index') }}">
                                    <i class="feather-more-vertical"></i>
                                </a>
                            </div>
                            <div class="pt-4 border-top">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-12 fw-medium text-muted">Total Programs</span>
                                    <span class="fs-12 text-dark fw-bold">{{ $stats['total_programs'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-info-subtle">
                                        <i class="feather-message-square text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['today_messages']) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Conversations Today</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-top">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-12 fw-medium text-muted">All Time</span>
                                    <span class="fs-12 text-dark fw-bold">{{ number_format($stats['total_messages']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Members -->
                <div class="col-xxl-8">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Recent Members</h5>
                            <a href="{{ route('admin.members.create') }}" class="btn btn-sm btn-primary">
                                <i class="feather-plus me-1"></i> Add Member
                            </a>
                        </div>
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Channel</th>
                                            <th>Morning Alert</th>
                                            <th>Last Seen</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentMembers as $member)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.members.show', $member) }}" class="fw-bold text-primary">
                                                    {{ $member->name }}
                                                </a>
                                            </td>
                                            <td>{{ $member->phone }}</td>
                                            <td><span class="badge bg-soft-info text-info text-uppercase">{{ $member->channel }}</span></td>
                                            <td>
                                                @if($member->morning_alert)
                                                    <span class="badge bg-soft-warning text-warning">
                                                        <i class="feather-bell me-1"></i> {{ \Carbon\Carbon::parse($member->alert_time)->format('g:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted fs-12">—</span>
                                                @endif
                                            </td>
                                            <td class="text-muted fs-12">
                                                {{ $member->last_interaction_at ? $member->last_interaction_at->diffForHumans() : 'Never' }}
                                            </td>
                                            <td class="text-muted fs-12">{{ $member->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No members yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions + Upcoming Events -->
                <div class="col-xxl-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Quick Actions</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                                    <i class="feather-user-plus me-2"></i> Add Member
                                </a>
                                <a href="{{ route('admin.programs.create') }}" class="btn btn-success">
                                    <i class="feather-calendar me-2"></i> Add Program
                                </a>
                                <a href="{{ route('admin.newsletters.create') }}" class="btn btn-warning">
                                    <i class="feather-send me-2"></i> Send Newsletter
                                </a>
                                <a href="{{ route('admin.church-info.create') }}" class="btn btn-info">
                                    <i class="feather-book-open me-2"></i> Add AI Knowledge
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Upcoming Events</h5>
                            <a href="{{ route('admin.programs.index') }}" class="btn btn-sm btn-light-brand">View All</a>
                        </div>
                        <div class="card-body p-0">
                            @forelse($upcomingEvents as $event)
                            <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                                @if($event->image_url)
                                    <img src="{{ $event->image_url }}" class="rounded" width="46" height="46" style="object-fit:cover;flex-shrink:0;">
                                @else
                                    <div class="avatar-text avatar-sm bg-primary-subtle text-primary" style="flex-shrink:0;">
                                        <i class="feather-calendar"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="fw-semibold mb-0 fs-13">{{ $event->title }}</p>
                                    <span class="text-muted fs-12">{{ $event->start_date->format('M d, Y') }}</span>
                                    @if($event->venue)
                                        <br><span class="text-muted fs-12"><i class="feather-map-pin me-1"></i>{{ Str::limit($event->venue, 25) }}</span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">No upcoming programs</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Newsletters -->
                <div class="col-12 mt-3">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Recent Newsletters</h5>
                            <a href="{{ route('admin.newsletters.create') }}" class="btn btn-sm btn-primary">
                                <i class="feather-plus me-1"></i> New Newsletter
                            </a>
                        </div>
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Title</th>
                                            <th>Target</th>
                                            <th>Media</th>
                                            <th>Status</th>
                                            <th>Sent To</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentNewsletters as $nl)
                                        <tr>
                                            <td class="fw-semibold">{{ $nl->title }}</td>
                                            <td><span class="badge bg-soft-info text-info">{{ $nl->target_campus === 'all' ? 'All Campuses' : $nl->target_campus }}</span></td>
                                            <td>
                                                @if($nl->media_url)
                                                    <span class="badge bg-soft-success text-success">{{ ucfirst($nl->media_type) }}</span>
                                                @else
                                                    <span class="text-muted fs-12">Text only</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($nl->status === 'sent')
                                                    <span class="badge bg-soft-success text-success">Sent</span>
                                                @elseif($nl->status === 'sending')
                                                    <span class="badge bg-soft-warning text-warning">Sending...</span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>{{ $nl->sent_count > 0 ? number_format($nl->sent_count) : '—' }}</td>
                                            <td class="text-muted fs-12">{{ $nl->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center py-4 text-muted">No newsletters yet</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@include('admin.components.footer')