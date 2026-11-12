<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>Report an issue</h1>
            <p>The more specific you are, the faster your barangay can act.</p>
        </div>
    </x-slot>

    <div class="fmc-narrow">
        @if ($errors->any())
            <div class="panel" role="alert" style="border-color:var(--red)">
                <h3 style="color:var(--red)">Your report was not submitted</h3>
                <ul class="fmc-error">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="report-form" class="panel form" method="POST"
              action="{{ route('reports.store') }}"
              enctype="multipart/form-data"
              style="margin-top:0">
            @csrf

            <div class="lbl" role="group" aria-labelledby="cat-label">
                <span id="cat-label">Category</span>

                <div class="cats">
                    <label><input type="radio" name="category" value="Roads" @checked(old('category') === 'Roads') required><span>Roads</span></label>
                    <label><input type="radio" name="category" value="Garbage" @checked(old('category') === 'Garbage') required><span>Garbage</span></label>
                    <label><input type="radio" name="category" value="Drainage" @checked(old('category') === 'Drainage') required><span>Drainage</span></label>
                    <label><input type="radio" name="category" value="Streetlights" @checked(old('category') === 'Streetlights') required><span>Streetlights</span></label>
                    <label><input type="radio" name="category" value="Water" @checked(old('category') === 'Water') required><span>Water</span></label>
                    <label><input type="radio" name="category" value="Other" @checked(old('category') === 'Other') required><span>Other</span></label>
                </div>

                <x-input-error :messages="$errors->get('category')" />
            </div>

            <div>
                <x-input-label for="title" value="Title" />

                <x-text-input id="title" name="title" type="text"
                    maxlength="255" :value="old('title')"
                    placeholder="Broken streetlight near the basketball court"
                    required />

                <x-input-error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="location" value="Where is the issue?" />

                <x-text-input id="location" name="location" type="text"
                    maxlength="255" :value="old('location')"
                    placeholder="Corner of Mabini St. and Rizal Ave."
                    required />

                <p class="meta" style="margin:4px 0 0">
                    This is the issue's location, not your home address.
                </p>

                <x-input-error :messages="$errors->get('location')" />
            </div>

            <div>
                <x-input-label for="map" value="Pin the exact location" />

                <p class="meta" style="margin:0 0 8px">
                    Your current location will be detected automatically.
                    Move the pin or click another location to identify where
                    the incident actually happened.
                </p>

                <div id="map" class="fmc-map"></div>

                <div style="margin-top:8px">
                    <button type="button" id="current-location" class="btn">
                        <span aria-hidden="true">⌖</span>
                        Use my current location
                    </button>

                    <span id="location-status" class="meta" aria-live="polite">
                        Detecting your location...
                    </span>
                </div>

                {{-- Hidden coordinates sent to Laravel --}}
                <input type="hidden" id="latitude" name="latitude"
                    value="{{ old('latitude') }}">

                <input type="hidden" id="longitude" name="longitude"
                    value="{{ old('longitude') }}">

                <x-input-error :messages="$errors->get('latitude')" />
                <x-input-error :messages="$errors->get('longitude')" />

                <label for="location_confirmed" class="fmc-check"
                    style="margin-top:12px">
                    <input
                        type="checkbox"
                        id="location_confirmed"
                        name="location_confirmed"
                        value="1"
                        required
                        @checked(old('location_confirmed'))
                    >

                    <span>
                        I confirm that the map pin marks the actual location
                        of the reported incident.
                    </span>
                </label>
            </div>

            <div>
                <x-input-label for="description" value="What's happening?" />

                <textarea id="description" name="description" rows="5"
                    placeholder="How long has it been like this? Is it blocking anyone?"
                    required>{{ old('description') }}</textarea>

                <x-input-error :messages="$errors->get('description')" />
            </div>

            <div>
                <x-input-label for="photo" value="Photo (optional, up to 5 MB)" />

                <input id="photo" name="photo" type="file" accept="image/*">

                <x-input-error :messages="$errors->get('photo')" />
            </div>

            <label for="is_anonymous" class="fmc-check">
                <input id="is_anonymous" name="is_anonymous" type="checkbox"
                    value="1" @checked(old('is_anonymous'))>

                <span>Submit this report anonymously</span>
            </label>

            <div class="fmc-flex">
                <x-primary-button>Submit report</x-primary-button>
                <a class="btn" href="{{ route('dashboard') }}">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Leaflet Map --}}
    <link rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const defaultLatitude = 12.8797;
            const defaultLongitude = 121.7740;

            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const locationStatus = document.getElementById('location-status');
            const locationButton = document.getElementById('current-location');
            const reportForm = document.getElementById('report-form');
            const locationConfirmation =
                document.getElementById('location_confirmed');

            const map = L.map('map').setView(
                [defaultLatitude, defaultLongitude],
                6
            );

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(map);

            let marker = null;

            function setLocation(latitude, longitude, zoom = 17) {
                if (marker) {
                    marker.setLatLng([latitude, longitude]);
                } else {
                    marker = L.marker(
                        [latitude, longitude],
                        { draggable: true }
                    ).addTo(map);

                    marker.on('dragend', function (event) {
                        const position = event.target.getLatLng();

                        latitudeInput.value = position.lat.toFixed(7);
                        longitudeInput.value = position.lng.toFixed(7);

                        locationStatus.textContent =
                            'Location adjusted. Please confirm the incident location.';
                    });
                }

                latitudeInput.value = latitude.toFixed(7);
                longitudeInput.value = longitude.toFixed(7);

                map.setView([latitude, longitude], zoom);
            }

            function detectLocation() {
                if (!navigator.geolocation) {
                    locationStatus.textContent =
                        'Location detection is not supported. Please pin the map manually.';
                    return;
                }

                locationStatus.textContent =
                    'Detecting your location...';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        setLocation(
                            position.coords.latitude,
                            position.coords.longitude
                        );

                        locationStatus.textContent =
                            'Current location detected. Move the pin to the actual incident location if necessary.';
                    },
                    function () {
                        locationStatus.textContent =
                            'Location unavailable. Allow location access or pin the map manually.';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            }

            // Restore the previous pin after validation errors.
            const oldLatitude = parseFloat(latitudeInput.value);
            const oldLongitude = parseFloat(longitudeInput.value);

            if (
                Number.isFinite(oldLatitude) &&
                Number.isFinite(oldLongitude)
            ) {
                setLocation(oldLatitude, oldLongitude);

                locationStatus.textContent =
                    'Previous pinned location restored.';
            } else {
                detectLocation();
            }

            // Allow manual map selection.
            map.on('click', function (event) {
                setLocation(event.latlng.lat, event.latlng.lng);

                locationStatus.textContent =
                    'Location selected. Please confirm the incident location.';
            });

            locationButton.addEventListener('click', detectLocation);

            // Prevent submission without coordinates and confirmation.
            reportForm.addEventListener('submit', function (event) {
                const latitude = latitudeInput.value.trim();
                const longitude = longitudeInput.value.trim();

                if (latitude === '' || longitude === '') {
                    event.preventDefault();

                    locationStatus.textContent =
                        'Please pin the incident location before submitting.';

                    document.getElementById('map').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    alert(
                        'Please select the actual incident location on the map first.'
                    );

                    return;
                }

                if (!locationConfirmation.checked) {
                    event.preventDefault();

                    locationConfirmation.focus();

                    alert(
                        'Please confirm that the map pin marks the actual incident location.'
                    );
                }
            });
        });
    </script>
</x-app-layout>