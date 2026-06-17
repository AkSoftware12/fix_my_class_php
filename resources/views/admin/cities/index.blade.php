@extends('layouts.app')

@section('title', 'Cities')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'City Management',
        'subtitle' => 'Cities where coaching centres operate',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.cities.export', 'module' => 'cities'])->render()
            .(auth()->user()->can('cities.create')
                ? '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cityModal" onclick="resetCityForm()"><i class="bi bi-plus-lg me-1"></i> Add City</button>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search name or state…">
                </div>
                <div class="col-md-3">
                    <select id="filterStatus" class="form-select">
                        <option value="">All statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="citiesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>State</th><th>Coachings</th><th>Status</th><th>Created</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Create / edit modal --}}
    <div class="modal fade" id="cityModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="cityForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="cityModalTitle">Add City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cityId">

                    {{-- Mode toggle --}}
                    <div class="mb-3 d-flex gap-2" id="cityModalToggle">
                        <button type="button" class="btn btn-sm btn-primary" id="modeAuto">
                            <i class="bi bi-list-ul me-1"></i> Select from List
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="modeManual">
                            <i class="bi bi-pencil me-1"></i> Enter Manually
                        </button>
                    </div>

                    {{-- State — always visible --}}
                    <div class="mb-3">
                        <label class="form-label required" for="cityState">State</label>
                        @if($userCity)
                            <input type="text" class="form-control" id="cityState" name="state"
                                value="{{ $userCity->state }}" readonly>
                        @else
                        <select class="form-select" id="cityState" name="state" required>
                            <option value="">— Select State —</option>
                            <option>Andhra Pradesh</option>
                            <option>Arunachal Pradesh</option>
                            <option>Assam</option>
                            <option>Bihar</option>
                            <option>Chhattisgarh</option>
                            <option>Goa</option>
                            <option>Gujarat</option>
                            <option>Haryana</option>
                            <option>Himachal Pradesh</option>
                            <option>Jharkhand</option>
                            <option>Karnataka</option>
                            <option>Kerala</option>
                            <option>Madhya Pradesh</option>
                            <option>Maharashtra</option>
                            <option>Manipur</option>
                            <option>Meghalaya</option>
                            <option>Mizoram</option>
                            <option>Nagaland</option>
                            <option>Odisha</option>
                            <option>Punjab</option>
                            <option>Rajasthan</option>
                            <option>Sikkim</option>
                            <option>Tamil Nadu</option>
                            <option>Telangana</option>
                            <option>Tripura</option>
                            <option>Uttar Pradesh</option>
                            <option>Uttarakhand</option>
                            <option>West Bengal</option>
                            <option>Delhi</option>
                            <option>Jammu &amp; Kashmir</option>
                            <option>Ladakh</option>
                            <option>Chandigarh</option>
                            <option>Puducherry</option>
                            <option>Andaman &amp; Nicobar Islands</option>
                            <option>Dadra &amp; Nagar Haveli and Daman &amp; Diu</option>
                            <option>Lakshadweep</option>
                        </select>
                        @endif
                        <div class="invalid-feedback" data-field="state"></div>
                    </div>

                    {{-- Auto mode: city dropdown --}}
                    <div id="autoFields">
                    <div class="mb-3">
                        <label class="form-label required" for="cityName">City Name</label>
                        <select class="form-select" id="cityName" name="name" required>
                            <option value="">— Select State first —</option>
                        </select>
                        <div class="invalid-feedback" data-field="name"></div>
                    </div>
                    </div>{{-- /autoFields --}}

                    {{-- Manual mode --}}
                    <div id="manualFields" style="display:none">
                    <div class="mb-3">
                        <label class="form-label required" for="manualCity">City Name</label>
                        <input type="text" class="form-control" id="manualCity" maxlength="120" placeholder="e.g. Prayagraj">
                        <div class="invalid-feedback" data-field="name"></div>
                    </div>
                    </div>{{-- /manualFields --}}

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="cityActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="cityActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#citiesTable').DataTable({
        ajax: {
            url: '{{ route('admin.cities.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'state' },
            { data: 'coachings_count' },
            { data: 'status', orderable: true },
            { data: 'created_at' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterStatus').on('change', () => table.ajax.reload());

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }

    const stateCities = {
        'Andhra Pradesh': ['Visakhapatnam','Vijayawada','Guntur','Nellore','Kurnool','Tirupati','Kakinada','Rajahmundry','Kadapa','Anantapur'],
        'Arunachal Pradesh': ['Itanagar','Naharlagun','Pasighat','Tezpur'],
        'Assam': ['Guwahati','Silchar','Dibrugarh','Jorhat','Nagaon','Tinsukia','Tezpur','Bongaigaon'],
        'Bihar': ['Patna','Gaya','Bhagalpur','Muzaffarpur','Darbhanga','Purnia','Arrah','Bihar Sharif','Begusarai','Katihar'],
        'Chhattisgarh': ['Raipur','Bhilai','Bilaspur','Korba','Durg','Rajnandgaon','Jagdalpur','Ambikapur'],
        'Goa': ['Panaji','Margao','Vasco da Gama','Mapusa','Ponda'],
        'Gujarat': ['Ahmedabad','Surat','Vadodara','Rajkot','Bhavnagar','Jamnagar','Gandhinagar','Anand','Nadiad','Morbi','Mehsana','Bharuch'],
        'Haryana': ['Faridabad','Gurgaon','Panipat','Ambala','Hisar','Karnal','Rohtak','Sonipat','Yamunanagar','Panchkula','Bhiwani','Sirsa'],
        'Himachal Pradesh': ['Shimla','Dharamshala','Mandi','Solan','Baddi','Kullu','Hamirpur','Una'],
        'Jharkhand': ['Ranchi','Jamshedpur','Dhanbad','Bokaro','Hazaribagh','Deoghar','Giridih','Phusro'],
        'Karnataka': ['Bangalore','Mysore','Hubli','Mangalore','Belgaum','Gulbarga','Davangere','Shimoga','Tumkur','Bijapur','Bellary','Raichur'],
        'Kerala': ['Thiruvananthapuram','Kochi','Kozhikode','Thrissur','Kollam','Kannur','Alappuzha','Palakkad','Malappuram','Kottayam'],
        'Madhya Pradesh': ['Bhopal','Indore','Gwalior','Jabalpur','Ujjain','Sagar','Satna','Rewa','Dewas','Ratlam','Singrauli','Burhanpur'],
        'Maharashtra': ['Mumbai','Pune','Nagpur','Nashik','Aurangabad','Solapur','Thane','Kolhapur','Amravati','Nanded','Sangli','Malegaon','Jalgaon','Akola'],
        'Manipur': ['Imphal','Thoubal','Bishnupur','Churachandpur'],
        'Meghalaya': ['Shillong','Tura','Jowai'],
        'Mizoram': ['Aizawl','Lunglei','Champhai'],
        'Nagaland': ['Kohima','Dimapur','Mokokchung'],
        'Odisha': ['Bhubaneswar','Cuttack','Rourkela','Sambalpur','Berhampur','Puri','Balasore','Baripada'],
        'Punjab': ['Ludhiana','Amritsar','Jalandhar','Patiala','Bathinda','Mohali','Hoshiarpur','Pathankot','Moga','Firozpur'],
        'Rajasthan': ['Jaipur','Jodhpur','Kota','Ajmer','Bikaner','Udaipur','Alwar','Bhilwara','Bharatpur','Sikar','Sri Ganganagar','Pali'],
        'Sikkim': ['Gangtok','Namchi','Gyalshing'],
        'Tamil Nadu': ['Chennai','Coimbatore','Madurai','Tiruchirappalli','Salem','Tirunelveli','Vellore','Erode','Tiruppur','Dindigul','Thoothukudi','Thanjavur'],
        'Telangana': ['Hyderabad','Warangal','Nizamabad','Karimnagar','Khammam','Ramagundam','Mahbubnagar','Nalgonda'],
        'Tripura': ['Agartala','Udaipur','Dharmanagar','Ambassa'],
        'Uttar Pradesh': ['Lucknow','Kanpur','Agra','Varanasi','Allahabad','Meerut','Ghaziabad','Noida','Bareilly','Aligarh','Mathura','Moradabad','Gorakhpur','Firozabad','Saharanpur','Jhansi','Muzaffarnagar','Etawah'],
        'Uttarakhand': ['Dehradun','Haridwar','Rishikesh','Roorkee','Nainital','Haldwani','Rudrapur','Kashipur'],
        'West Bengal': ['Kolkata','Howrah','Durgapur','Asansol','Siliguri','Bardhaman','Malda','Bardhaman','Haldia','Kharagpur'],
        'Delhi': ['New Delhi','Delhi','Dwarka','Rohini','Saket','Janakpuri','Laxmi Nagar','Uttam Nagar'],
        'Jammu & Kashmir': ['Srinagar','Jammu','Anantnag','Baramulla','Sopore','Kathua'],
        'Ladakh': ['Leh','Kargil'],
        'Chandigarh': ['Chandigarh'],
        'Puducherry': ['Puducherry','Karaikal','Mahe','Yanam'],
        'Andaman & Nicobar Islands': ['Port Blair'],
        'Dadra & Nagar Haveli and Daman & Diu': ['Daman','Diu','Silvassa'],
        'Lakshadweep': ['Kavaratti'],
    };

    function populateCities(state, selectedCity = '') {
        const $city = $('#cityName');
        $city.empty();
        if (!state || !stateCities[state]) {
            $city.append('<option value="">— Select State first —</option>');
            $city.trigger('change');
            return;
        }
        $city.append('<option value="">— Select City —</option>');
        stateCities[state].forEach(c => {
            $city.append(`<option value="${c}" ${c === selectedCity ? 'selected' : ''}>${c}</option>`);
        });
        if (selectedCity && !stateCities[state].includes(selectedCity)) {
            $city.append(`<option value="${selectedCity}" selected>${selectedCity}</option>`);
        }
        $city.trigger('change');
    }

    $('#cityState').on('change', function () {
        populateCities($(this).val());
    });

    let currentMode = 'auto';

    function setMode(mode) {
        currentMode = mode;
        if (mode === 'auto') {
            $('#autoFields').show();
            $('#manualFields').hide();
            $('#cityName').prop('required', true);
            $('#manualCity').prop('required', false).val('');
            $('#modeAuto').removeClass('btn-outline-secondary').addClass('btn-primary');
            $('#modeManual').removeClass('btn-primary').addClass('btn-outline-secondary');
        } else {
            $('#autoFields').hide();
            $('#manualFields').show();
            $('#cityName').prop('required', false);
            $('#manualCity').prop('required', true);
            $('#modeManual').removeClass('btn-outline-secondary').addClass('btn-primary');
            $('#modeAuto').removeClass('btn-primary').addClass('btn-outline-secondary');
        }
    }

    $('#modeAuto').on('click', () => setMode('auto'));
    $('#modeManual').on('click', () => setMode('manual'));

    const isCityAdmin = {{ $userCity ? 'true' : 'false' }};
    @if($userCity)
    const cityAdminState = '{{ $userCity->state }}';
    @endif

    function initCitySelects() {
        if (!isCityAdmin) {
            $('#cityState').select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $('#cityModal'), placeholder: '— Select State —' });
        }
        $('#cityName').select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $('#cityModal'), placeholder: '— Select City —', tags: true });
    }

    $('#cityModal').on('shown.bs.modal', function () {
        initCitySelects();
        if (isCityAdmin) {
            populateCities(cityAdminState);
            $('#cityModalToggle').hide();
        }
    });

    function resetCityForm() {
        $('#cityId').val('');
        $('#cityModalTitle').text('Add City');
        $('#cityForm .is-invalid').removeClass('is-invalid');
        $('#manualCity').val('');
        $('#cityActive').prop('checked', true);
        setMode('auto');
        if (isCityAdmin) {
            $('#cityState').val(cityAdminState);
            populateCities(cityAdminState);
        } else {
            $('#cityState').val('').trigger('change');
            populateCities('');
        }
    }

    function editCity(id) {
        $.get(`{{ url('admin/cities') }}/${id}/edit`).done((res) => {
            resetCityForm();
            $('#cityId').val(res.city.id);
            if (isCityAdmin) {
                $('#cityState').val(cityAdminState);
                populateCities(cityAdminState, res.city.name);
            } else {
                $('#cityState').val(res.city.state).trigger('change');
                populateCities(res.city.state, res.city.name);
            }
            $('#cityActive').prop('checked', !!res.city.is_active);
            $('#cityModalTitle').text('Edit City');
            new bootstrap.Modal('#cityModal').show();
        });
    }

    $('#cityForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#cityId').val();
        const url = id ? `{{ url('admin/cities') }}/${id}` : '{{ route('admin.cities.store') }}';
        const payload = {
            name: currentMode === 'manual' ? $('#manualCity').val() : $('#cityName').val(),
            state: $('#cityState').val(),
            is_active: $('#cityActive').is(':checked') ? 1 : 0,
            _method: id ? 'PUT' : 'POST',
        };

        $(this).find('.is-invalid').removeClass('is-invalid');

        $.post(url, payload)
            .done((res) => {
                bootstrap.Modal.getInstance(document.getElementById('cityModal')).hide();
                fmcToast(res.message);
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    Object.entries(errors).forEach(([field, msgs]) => {
                        $(`#cityForm [name="${field}"]`).addClass('is-invalid');
                        $(`#cityForm [data-field="${field}"]`).text(msgs[0]);
                    });
                } else {
                    fmcToast(xhr.responseJSON?.message || 'Save failed.', 'error');
                }
            });
    });
</script>
@endpush
