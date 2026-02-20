{{-- Location: resources/views/admin/members/edit.blade.php --}}
@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Member</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.members.index') }}">Members</a></li>
                    <li class="breadcrumb-item">Edit {{ $member->name }}</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Edit Member — {{ $member->name }}</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.members.update', $member->id) }}">
                                @csrf @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $member->name) }}" required />
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="+2348012345678" value="{{ old('phone', $member->phone) }}" required />
                                    <div class="form-text">Must start with +234 followed by 10 digits e.g. +2348012345678</div>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Channel</label>
                                    <select name="channel" class="form-select">
                                        <option value="whatsapp" {{ old('channel', $member->channel) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="sms" {{ old('channel', $member->channel) == 'sms' ? 'selected' : '' }}>SMS</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Campus</label>
                                    <select name="campus_id" class="form-select">
                                        <option value="">— Select Campus —</option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('campus_id', $member->campus_id) == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="morning_alert" id="morningAlert" value="1"
                                               {{ old('morning_alert', $member->morning_alert) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="morningAlert">
                                            <i class="feather-bell me-1"></i> Enable Morning Alert
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Alert Time</label>
                                    <input type="time" name="alert_time" class="form-control"
                                           value="{{ old('alert_time', $member->alert_time) }}" />
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Save Changes
                                    </button>
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-light">Cancel</a>
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