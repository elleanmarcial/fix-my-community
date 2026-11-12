<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>{{ $report->title }}</h1>
            <p class="mono">FCM-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <x-status-chip :status="$report->status" />
    </x-slot>

    <div class="fmc-narrow">

        @if (session('success'))
            <div class="fmc-ok">{{ session('success') }}</div>
        @endif

        {{-- Progress --}}
        <div class="panel">
            <h2>Progress</h2>

            <div style="margin-top:12px">
                <x-progress-track :status="$report->status" />
            </div>
        </div>

        {{-- Report Description and Photo --}}
        <div class="panel">
            <h2>What was reported</h2>

            <p style="white-space:pre-line;max-width:70ch">
                {{ $report->description }}
            </p>

            @if ($report->photo_path)
                @if (\Illuminate\Support\Facades\Storage::disk('public')->exists($report->photo_path))
                    <div style="margin-top:16px">
                        <img
                            src="{{ asset('storage/' . $report->photo_path) }}"
                            alt="Photo submitted with this report"
                            style="display:block;width:100%;max-width:600px;max-height:400px;object-fit:contain;border-radius:14px;margin-top:12px;"
                        >
                    </div>
                @else
                    <p class="meta" style="margin-top:12px">
                        The uploaded photo could not be found in storage.
                    </p>
                @endif
            @endif
        </div>

        {{-- Report Location Map --}}
        @if ($report->latitude !== null && $report->longitude !== null)
            <div class="panel">
                <h2>Report Location</h2>

                <p class="meta" style="margin:6px 0 12px">
                    This map shows the exact location where the issue was reported.
                </p>

                <div
                    id="report-map"
                    class="fmc-map"
                    style="width:100%;height:350px;border-radius:14px;overflow:hidden;"
                ></div>
            </div>
        @endif

        {{-- Report Details --}}
        <div class="panel">
            <h2>Details</h2>

            <dl class="fmc-dl" style="margin-top:12px">
                <div>
                    <dt>Category</dt>
                    <dd>{{ $report->category }}</dd>
                </div>

                <div>
                    <dt>Issue location</dt>
                    <dd>{{ $report->location }}</dd>
                </div>

                <div>
                    <dt>Submitted</dt>
                    <dd>
                        {{ ($report->reported_at ?? $report->created_at)->format('M j, Y g:i A') }}
                    </dd>
                </div>

                <div>
                    <dt>Assigned to</dt>
                    <dd>{{ $report->assigned_to ?: 'Not yet assigned' }}</dd>
                </div>

                <div>
                    <dt>Resolved</dt>
                    <dd>
                        {{ $report->resolved_at
                            ? $report->resolved_at->format('M j, Y g:i A')
                            : 'Not resolved yet' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Notes History --}}
        <section class="panel" id="notes-history">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px">
                <h2>Notes History</h2>

                <span class="meta">
                    {{ $report->notes->count() }}
                    {{ \Illuminate\Support\Str::plural('update', $report->notes->count()) }}
                </span>
            </div>

            @if ($report->notes->isEmpty())
                <p class="meta" style="margin-top:14px">
                    No updates have been posted yet. Check back later for updates from your barangay.
                </p>
            @else
                <div style="display:grid;gap:16px;margin-top:16px">
                    @foreach ($report->notes as $note)
                        <div style="padding-bottom:14px;border-bottom:1px solid rgba(255,255,255,.15)">
                            <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap">
                                <b>
                                    {{ $loop->first ? 'Latest update' : 'Previous update' }}
                                </b>

                                <span class="meta">
                                    {{ $note->created_at->format('M j, Y g:i A') }}
                                </span>
                            </div>

                            <p style="white-space:pre-line;margin-top:8px">
                                {{ $note->note }}
                            </p>

                            <p class="meta" style="margin-top:6px">
                                Posted by {{ $note->author->name ?? 'Barangay staff' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Actions --}}
        <div class="fmc-flex">
            <a class="btn" href="{{ route('reports.index') }}">
                Back to my reports
            </a>

            @if ($report->status === 'Submitted')
                <a class="btn" href="{{ route('reports.edit', $report) }}">
                    Edit report
                </a>
            @endif
        </div>
    </div>

    {{-- Leaflet --}}
    @if ($report->latitude !== null && $report->longitude !== null)
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        >

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const latitude = {{ (float) $report->latitude }};
                const longitude = {{ (float) $report->longitude }};

                const map = L.map('report-map').setView(
                    [latitude, longitude],
                    17
                );

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);

                L.marker([latitude, longitude])
                    .addTo(map)
                    .bindPopup(@json($report->location))
                    .openPopup();

                setTimeout(function () {
                    map.invalidateSize();
                }, 200);
            });
        </script>
    @endif
</x-app-layout>