<x-guest-layout>
    <h2 style="margin-top:20px">Join your community</h2>

    <form id="registration-form" method="POST"
        action="{{ route('register') }}"
        class="form"
        style="margin-top:16px">

        @csrf

        <div>
            <x-input-label for="name" value="Full name" />

            <x-text-input
                id="name"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email address" />

            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            />

            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Home barangay selection --}}
        <div class="fmc-address-select">
            <h3 style="margin-bottom:4px">Your home address</h3>

            <p class="meta" style="margin:0 0 12px">
                Select your location using the dropdowns below.
                This helps us identify your assigned barangay.
            </p>

            <div>
                <x-input-label for="region" value="Region" />

                <select id="region" class="fmc-select" required>
                    <option value="">Select Region</option>
                </select>
            </div>

            <div>
                <x-input-label for="province" value="Province" />

                <select id="province" class="fmc-select" disabled required>
                    <option value="">Select Province</option>
                </select>
            </div>

            <div>
                <x-input-label for="city" value="City / Municipality" />

                <select id="city" class="fmc-select" disabled required>
                    <option value="">Select City / Municipality</option>
                </select>
            </div>

            <div>
                <x-input-label for="barangay" value="Barangay" />

                <select id="barangay" class="fmc-select" disabled required>
                    <option value="">Select Barangay</option>
                </select>
            </div>

            <input
                type="hidden"
                id="barangay_id"
                name="barangay_id"
                value="{{ old('barangay_id') }}"
            />

            <x-input-error :messages="$errors->get('barangay_id')" />

            <p class="meta" style="margin:8px 0 0">
                Your selected barangay is stored in your account.
                Only authorized barangay staff can access your home
                address information.
            </p>
        </div>

        <div>
            <x-input-label for="password" value="Password" />

            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label
                for="password_confirmation"
                value="Confirm password"
            />

            <x-text-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
        </div>

        <x-primary-button>Create account</x-primary-button>
    </form>

    <style>
        .fmc-address-select {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .fmc-select {
            display: block;
            width: 100%;
            min-height: 42px;
            padding: 9px 12px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.12);
            color: inherit;
            font: inherit;
            outline: none;
        }

        .fmc-select:focus {
            border-color: var(--green, #a8e6cf);
            box-shadow: 0 0 0 2px rgba(168, 230, 207, 0.2);
        }

        .fmc-select:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .fmc-select option {
            background: #ffffff;
            color: #173d32;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const barangayData = {{ \Illuminate\Support\Js::from($barangayOptions) }};

            const regionSelect = document.getElementById('region');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');
            const barangayIdInput = document.getElementById('barangay_id');
            const registrationForm = document.getElementById('registration-form');

            const oldBarangayId = barangayIdInput.value;

            function unique(values) {
                return [...new Set(values.filter(value => value))].sort(
                    (a, b) => a.localeCompare(b)
                );
            }

            function populate(select, values, placeholder) {
                select.innerHTML = '';

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = placeholder;

                select.appendChild(defaultOption);

                values.forEach(function (value) {
                    const option = document.createElement('option');

                    option.value = value;
                    option.textContent = value;

                    select.appendChild(option);
                });

                select.disabled = values.length === 0;
            }

            function resetProvince() {
                populate(provinceSelect, [], 'Select Province');
                resetCity();
            }

            function resetCity() {
                populate(citySelect, [], 'Select City / Municipality');
                resetBarangay();
            }

            function resetBarangay() {
                populate(barangaySelect, [], 'Select Barangay');
                barangayIdInput.value = '';
            }

            function loadRegions() {
                const regions = unique(
                    barangayData.map(item => item.region)
                );

                populate(regionSelect, regions, 'Select Region');
            }

            function loadProvinces() {
                const region = regionSelect.value;

                const provinces = unique(
                    barangayData
                        .filter(item => item.region === region)
                        .map(item => item.province)
                );

                populate(provinceSelect, provinces, 'Select Province');
                resetCity();
            }

            function loadCities() {
                const region = regionSelect.value;
                const province = provinceSelect.value;

                const cities = unique(
                    barangayData
                        .filter(item =>
                            item.region === region &&
                            item.province === province
                        )
                        .map(item => item.city)
                );

                populate(
                    citySelect,
                    cities,
                    'Select City / Municipality'
                );

                resetBarangay();
            }

            function loadBarangays() {
                const region = regionSelect.value;
                const province = provinceSelect.value;
                const city = citySelect.value;

                const matchingBarangays = barangayData
                    .filter(item =>
                        item.region === region &&
                        item.province === province &&
                        item.city === city
                    )
                    .sort((a, b) => a.name.localeCompare(b.name));

                barangaySelect.innerHTML = '';

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select Barangay';

                barangaySelect.appendChild(defaultOption);

                matchingBarangays.forEach(function (item) {
                    const option = document.createElement('option');

                    option.value = item.id;
                    option.textContent = item.name;

                    barangaySelect.appendChild(option);
                });

                barangaySelect.disabled = matchingBarangays.length === 0;
                barangayIdInput.value = '';
            }

            regionSelect.addEventListener('change', loadProvinces);
            provinceSelect.addEventListener('change', loadCities);
            citySelect.addEventListener('change', loadBarangays);

            barangaySelect.addEventListener('change', function () {
                barangayIdInput.value = barangaySelect.value;
            });

            // Load the first dropdown.
            loadRegions();

            // Restore the previous selection after validation errors.
            if (oldBarangayId) {
                const previousBarangay = barangayData.find(
                    item => String(item.id) === String(oldBarangayId)
                );

                if (previousBarangay) {
                    regionSelect.value = previousBarangay.region;
                    regionSelect.dispatchEvent(new Event('change'));

                    provinceSelect.value = previousBarangay.province;
                    provinceSelect.dispatchEvent(new Event('change'));

                    citySelect.value = previousBarangay.city;
                    citySelect.dispatchEvent(new Event('change'));

                    barangaySelect.value = String(previousBarangay.id);
                    barangayIdInput.value = String(previousBarangay.id);
                }
            }

            // Prevent registration without a selected barangay.
            registrationForm.addEventListener('submit', function (event) {
                if (!barangayIdInput.value) {
                    event.preventDefault();

                    alert('Please select your Region, Province, City/Municipality, and Barangay.');
                    barangaySelect.focus();
                }
            });
        });
    </script>
</x-guest-layout>