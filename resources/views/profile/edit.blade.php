<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>My Profile</h1>
            <p>Manage your account and barangay admin application.</p>
        </div>
    </x-slot>

    <div class="fmc-narrow profile-page">

        {{-- Profile Information --}}
        <div class="panel profile-panel">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Barangay Admin Application --}}
        @if ($user->role === 'resident')
            <div class="panel profile-panel">

                <h2>Become a Barangay Admin</h2>

                <p class="profile-description">
                    Apply to become part of your barangay's community
                    management team. Your application will be reviewed
                    by the Super Admin.
                </p>

                @if (session('admin_application_success'))
                    <div class="application-success">
                        {{ session('admin_application_success') }}
                    </div>
                @endif

                @if ($errors->has('admin_application'))
                    <div class="application-error">
                        {{ $errors->first('admin_application') }}
                    </div>
                @endif

                @if ($application && $application->status === 'pending_review')

                    <div class="application-pending">
                        <h3>Application Pending Review</h3>
                        <p>Your application is waiting for Super Admin approval.</p>
                        <p class="application-meta">
                            Position:
                            <strong>{{ ucfirst($application->position) }}</strong>
                        </p>
                        <p>
                            Submitted:
                            {{ \Illuminate\Support\Carbon::parse($application->created_at)->format('M j, Y g:i A') }}
                        </p>
                    </div>

                @elseif ($application && $application->status === 'approved')

                    <div class="application-approved">
                        <h3>Application Approved</h3>
                        <p>Your application has been approved.</p>
                    </div>

                @else

                    @if ($application && $application->status === 'rejected')
                        <div class="application-rejected">
                            <strong>Previous Application Rejected</strong>
                            <p>
                                {{ $application->review_notes ?: 'You may submit a new application.' }}
                            </p>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('profile.admin-application.store') }}"
                        enctype="multipart/form-data"
                        id="barangay-application-form"
                        class="form profile-application-form"
                    >
                        @csrf

                        {{-- Region --}}
                        <div>
                            <label for="region">Region</label>
                            <select id="region" class="fmc-address-select">
                                <option value="">Choose your region</option>
                            </select>
                        </div>

                        {{-- Province --}}
                        <div>
                            <label for="province">Province</label>
                            <select id="province" class="fmc-address-select" disabled>
                                <option value="">Choose your province</option>
                            </select>
                        </div>

                        {{-- City --}}
                        <div>
                            <label for="city">City / Municipality</label>
                            <select id="city" class="fmc-address-select" disabled>
                                <option value="">Choose your city</option>
                            </select>
                        </div>

                        {{-- Barangay --}}
                        <div>
                            <label for="barangay">Barangay</label>
                            <select id="barangay" class="fmc-address-select" disabled>
                                <option value="">Choose your barangay</option>
                            </select>

                            <input
                                type="hidden"
                                name="barangay_id"
                                id="barangay_id"
                                value="{{ old('barangay_id') }}"
                            >

                            <x-input-error :messages="$errors->get('barangay_id')" />
                        </div>

                        {{-- Position --}}
                        <div>
                            <label for="position">Application Position</label>

                            <select
                                id="position"
                                name="position"
                                class="fmc-address-select"
                                required
                            >
                                <option value="">Choose a position</option>

                                <option value="member" @selected(old('position') === 'member')>
                                    Barangay Member
                                </option>

                                <option value="lead" @selected(old('position') === 'lead')>
                                    Barangay Lead
                                </option>
                            </select>

                            <x-input-error :messages="$errors->get('position')" />
                        </div>

                        {{-- Supporting Proof --}}
                        <div>
                            <label for="proof">Supporting Proof (optional)</label>

                            <input
                                id="proof"
                                name="proof"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="profile-file-input"
                            >

                            <p class="profile-hint">
                                Accepted formats: JPG, PNG, PDF. Maximum 5 MB.
                            </p>

                            <x-input-error :messages="$errors->get('proof')" />
                        </div>

                        <button type="submit" class="profile-submit-button">
                            Submit Application
                        </button>
                    </form>

                @endif
            </div>
        @endif

        {{-- Update Password --}}
        <div class="panel profile-panel">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete Account --}}
        <div class="panel profile-panel">
            @include('profile.partials.delete-user-form')
        </div>

    </div>

    <style>
        /* PROFILE PAGE LAYOUT */
        .profile-page {
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        /* GLASS PANELS */
        .profile-panel {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            padding: 24px !important;
        }

        /* TEXT INSIDE PROFILE PANELS */
        .profile-panel h2,
        .profile-panel h3,
        .profile-panel label,
        .profile-panel p,
        .profile-panel span {
            color: #ffffff;
        }

        .profile-panel h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .profile-panel h3 {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .profile-description {
            color: rgba(255, 255, 255, 0.82) !important;
            margin: 8px 0 20px;
            line-height: 1.6;
        }

        /* PROFILE INPUTS */
        .profile-panel input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        .profile-panel select,
        .profile-panel textarea {
            display: block;
            width: 100%;
            min-height: 44px;
            padding: 10px 14px;
            background: #f7fffb !important;
            color: #164e3b !important;
            border: 1px solid #a7d9c1 !important;
            border-radius: 10px !important;
            font-size: 14px;
        }

        .profile-panel input:focus,
        .profile-panel select:focus,
        .profile-panel textarea:focus {
            outline: 2px solid rgba(255, 255, 255, 0.4);
            border-color: #ffffff !important;
        }

        .profile-panel input::placeholder,
        .profile-panel textarea::placeholder {
            color: #668477 !important;
        }

        /* CASCADING ADDRESS DROPDOWNS */
        .profile-application-form > div {
            margin-bottom: 16px;
        }

        .profile-application-form label {
            display: block;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .fmc-address-select {
            display: block !important;
            width: 100% !important;
            min-height: 46px !important;
            padding: 10px 14px !important;
            border: 1px solid #a7d9c1 !important;
            border-radius: 10px !important;
            background-color: #f7fffb !important;
            color: #164e3b !important;
            font-size: 14px !important;
            appearance: auto !important;
            -webkit-appearance: auto !important;
        }

        .fmc-address-select:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            background-color: #e5eee9 !important;
        }

        .fmc-address-select:focus {
            outline: 2px solid rgba(255, 255, 255, 0.4);
            border-color: #ffffff !important;
        }

        /* FILE INPUT */
        .profile-file-input {
            display: block;
            width: 100%;
            margin-top: 8px;
            color: #ffffff !important;
        }

        .profile-hint {
            color: rgba(255, 255, 255, 0.75) !important;
            font-size: 12px;
            margin-top: 6px;
        }

        /* SUBMIT BUTTON */
        .profile-submit-button {
            background: #d8fff0;
            color: #075640;
            padding: 12px 22px;
            border: 0;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .profile-submit-button:hover {
            background: #ffffff;
            transform: translateY(-1px);
        }

        /* APPLICATION STATUS MESSAGES */
        .application-success,
        .application-approved {
            background: rgba(220, 252, 231, 0.95);
            color: #166534;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .application-success {
            color: #166534 !important;
        }

        .application-error,
        .application-rejected {
            background: rgba(254, 226, 226, 0.95);
            color: #991b1b;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .application-error {
            color: #991b1b !important;
        }

        .application-rejected p {
            color: #991b1b !important;
            margin-top: 6px;
        }

        .application-pending {
            background: rgba(254, 249, 195, 0.96);
            color: #854d0e;
            padding: 18px;
            border-radius: 12px;
        }

        .application-pending h3,
        .application-pending p {
            color: #854d0e !important;
        }

        .application-approved h3,
        .application-approved p {
            color: #166534 !important;
        }

        .application-meta {
            margin-top: 8px;
        }

        /* FORM VALIDATION */
        .profile-panel [class*="text-red"] {
            color: #fecaca !important;
        }

        /* MOBILE */
        @media (max-width: 640px) {
            .profile-page {
                padding-left: 12px;
                padding-right: 12px;
            }

            .profile-panel {
                padding: 18px !important;
            }
        }
    </style>

    {{-- BARANGAY CASCADING DROPDOWN DATA --}}
    @if ($user->role === 'resident' &&
        (! $application || $application->status === 'rejected'))

        @php
            $barangayOptions = $barangays->map(function ($b) {
                return [
                    'id' => $b->id,
                    'region' => $b->region ?: 'Unspecified Region',
                    'province' => $b->province ?: 'No Province / NCR',
                    'city' => $b->city,
                    'name' => $b->name,
                ];
            })->values();
        @endphp

        <script>
            const barangayData = {{ \Illuminate\Support\Js::from($barangayOptions) }};

            document.addEventListener('DOMContentLoaded', function () {
                const data = barangayData;

                const region = document.getElementById('region');
                const province = document.getElementById('province');
                const city = document.getElementById('city');
                const barangay = document.getElementById('barangay');
                const barangayId = document.getElementById('barangay_id');
                const form = document.getElementById('barangay-application-form');

                if (!form || !region || !province || !city || !barangay || !barangayId) {
                    return;
                }

                const oldId = barangayId.value;

                function unique(values) {
                    return [...new Set(values.filter(Boolean))]
                        .sort((a, b) => a.localeCompare(b));
                }

                function populate(select, items, placeholder) {
                    select.innerHTML = '';

                    const first = document.createElement('option');
                    first.value = '';
                    first.textContent = placeholder;
                    select.appendChild(first);

                    items.forEach(item => {
                        const option = document.createElement('option');

                        if (typeof item === 'object') {
                            option.value = item.id;
                            option.textContent = item.name;
                        } else {
                            option.value = item;
                            option.textContent = item;
                        }

                        select.appendChild(option);
                    });

                    select.disabled = items.length === 0;
                }

                // Populate regions.
                populate(
                    region,
                    unique(data.map(b => b.region)),
                    'Choose your region'
                );

                // Region changes.
                region.addEventListener('change', function () {
                    barangayId.value = '';

                    populate(province, [], 'Choose your province');
                    populate(city, [], 'Choose your city');
                    populate(barangay, [], 'Choose your barangay');

                    const filtered = data.filter(b =>
                        b.region === region.value
                    );

                    populate(
                        province,
                        unique(filtered.map(b => b.province)),
                        'Choose your province'
                    );
                });

                // Province changes.
                province.addEventListener('change', function () {
                    barangayId.value = '';

                    populate(city, [], 'Choose your city');
                    populate(barangay, [], 'Choose your barangay');

                    const filtered = data.filter(b =>
                        b.region === region.value &&
                        b.province === province.value
                    );

                    populate(
                        city,
                        unique(filtered.map(b => b.city)),
                        'Choose your city'
                    );
                });

                // City changes.
                city.addEventListener('change', function () {
                    barangayId.value = '';

                    populate(barangay, [], 'Choose your barangay');

                    const filtered = data.filter(b =>
                        b.region === region.value &&
                        b.province === province.value &&
                        b.city === city.value
                    );

                    filtered.sort((a, b) =>
                        a.name.localeCompare(b.name)
                    );

                    populate(
                        barangay,
                        filtered,
                        'Choose your barangay'
                    );
                });

                // Barangay selection.
                barangay.addEventListener('change', function () {
                    barangayId.value = barangay.value;
                });

                // Prevent submission without barangay selection.
                form.addEventListener('submit', function (event) {
                    if (!barangayId.value) {
                        event.preventDefault();
                        alert('Please select your barangay.');
                        barangay.focus();
                    }
                });

                // Restore selected address after validation errors.
                if (oldId) {
                    const selected = data.find(b =>
                        String(b.id) === String(oldId)
                    );

                    if (selected) {
                        region.value = selected.region;
                        region.dispatchEvent(new Event('change'));

                        province.value = selected.province;
                        province.dispatchEvent(new Event('change'));

                        city.value = selected.city;
                        city.dispatchEvent(new Event('change'));

                        barangay.value = String(selected.id);
                        barangayId.value = selected.id;
                    }
                }
            });
        </script>
    @endif

</x-app-layout>