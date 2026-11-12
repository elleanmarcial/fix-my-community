<x-app-layout>
    <div class="fmc-narrow">
        <div class="hero">
            <svg viewBox="0 0 420 420" fill="none" stroke="var(--line)" stroke-width="1.5" aria-hidden="true"><g transform="translate(210 210)"><ellipse rx="190" ry="140" transform="rotate(-18)"/><ellipse rx="158" ry="112" transform="rotate(-14)"/><ellipse rx="126" ry="86" transform="rotate(-8)"/><ellipse rx="94" ry="62" transform="rotate(-2)"/><ellipse rx="62" ry="38" transform="rotate(6)"/><ellipse rx="30" ry="16" transform="rotate(14)"/></g></svg>
            <h1>What needs fixing on your street?</h1>
            <p>Tell your barangay what you see, then follow the report until it is resolved.</p>
            <a class="btn primary block-m" href="{{ route('reports.create') }}">Submit a report</a>
        </div>

        @if (session('success'))
            <div class="fmc-ok">{{ session('success') }}</div>
        @endif

        <div class="panel">
            <div class="fmc-title" style="margin-bottom:0">
                <h2>My reports</h2>
                @if ($reports->count() > 5)
                    <a class="link" href="{{ route('reports.index') }}">View all {{ $reports->count() }}</a>
                @endif
            </div>
            @forelse ($reports->take(5) as $report)
                <x-report-card :report="$report" />
            @empty
                <p>You haven't reported anything yet. Use the button above to send your first report.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
