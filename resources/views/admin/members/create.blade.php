@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Add Member</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.members.index') }}">Members</a></li>
                    <li class="breadcrumb-item">Add Member</li>
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
                <div class="col-xxl-6 col-xl-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">New Member Details</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.members.store') }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           placeholder="e.g. John Doe" value="{{ old('name') }}" required />
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="e.g. +2348012345678" value="{{ old('phone') }}" required />
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Channel</label>
                                    <select name="channel" class="form-select">
                                        <option value="whatsapp" {{ old('channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="sms" {{ old('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Campus</label>
                                    <select name="campus_id" class="form-select">
                                        <option value="">— Select Campus —</option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="morning_alert" id="morningAlert" value="1" {{ old('morning_alert') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="morningAlert">
                                            <i class="feather-bell me-1"></i> Enable Morning Alert
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Alert Time</label>
                                    <input type="time" name="alert_time" class="form-control" value="{{ old('alert_time', '06:00') }}" />
                                    <div class="form-text">Time to send the morning prayer - NLP (if alert is on)</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-user-plus me-2"></i> Add Member
                                    </button>
                                    <a href="{{ route('admin.members.index') }}" class="btn btn-light">Cancel</a>
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