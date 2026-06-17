@php($coaching = $coaching ?? null)

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label required" for="name">Coaching Name</label>
        <input type="text" id="name" name="name" maxlength="180" required
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $coaching?->name) }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="city_id">City</label>
        <select id="city_id" name="city_id" class="form-select select2 @error('city_id') is-invalid @enderror" required>
            <option value="">Select city…</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}" @selected(old('city_id', $coaching?->city_id) == $city->id)>
                    {{ $city->name }}, {{ $city->state }}
                </option>
            @endforeach
        </select>
        @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="owner_name">Owner Name</label>
        <input type="text" id="owner_name" name="owner_name" maxlength="120" required
               class="form-control @error('owner_name') is-invalid @enderror"
               value="{{ old('owner_name', $coaching?->owner_name) }}">
        @error('owner_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="180" required
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $coaching?->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="mobile">Mobile</label>
        <input type="text" id="mobile" name="mobile" maxlength="20" required
               class="form-control @error('mobile') is-invalid @enderror"
               value="{{ old('mobile', $coaching?->mobile) }}">
        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="logo">Logo <span class="text-muted">(JPG/PNG, max 2&nbsp;MB)</span></label>
        <input type="file" id="logo" name="logo" accept="image/*"
               class="form-control @error('logo') is-invalid @enderror">
        @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($coaching?->logo_url)
            <img src="{{ $coaching->logo_url }}" alt="Current logo" class="mt-2 rounded" style="height:48px">
        @endif
    </div>

    <div class="col-12">
        <label class="form-label" for="address">Address</label>
        <textarea id="address" name="address" rows="2" maxlength="1000"
                  class="form-control @error('address') is-invalid @enderror">{{ old('address', $coaching?->address) }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Location <span class="text-muted small fw-normal">(for nearby search in mobile app)</span></label>
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1" for="latitude">Latitude</label>
                <input type="number" id="latitude" name="latitude" step="any" min="-90" max="90"
                       class="form-control @error('latitude') is-invalid @enderror"
                       placeholder="e.g. 30.3165"
                       value="{{ old('latitude', $coaching?->latitude) }}">
                @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1" for="longitude">Longitude</label>
                <input type="number" id="longitude" name="longitude" step="any" min="-180" max="180"
                       class="form-control @error('longitude') is-invalid @enderror"
                       placeholder="e.g. 78.0322"
                       value="{{ old('longitude', $coaching?->longitude) }}">
                @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-primary w-100"
                        onclick="fmcOpenMapPicker('#latitude', '#longitude')">
                    <i class="bi bi-map me-1"></i>Map
                </button>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $coaching?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $coaching ? 'Update Coaching' : 'Create Coaching' }}</button>
    <a href="{{ route('admin.coachings.index') }}" class="btn btn-light">Cancel</a>
</div>
