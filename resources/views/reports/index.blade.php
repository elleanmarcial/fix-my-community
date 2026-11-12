<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>My reports</h1>
            <p>Every report you have submitted and where it stands.</p>
        </div>
        <a class="btn primary block-m" href="{{ route('reports.create') }}">Submit a report</a>
    </x-slot>

    <div class="fmc-narrow">
        @if (session('success'))
            <div class="fmc-ok">{{ session('success') }}</div>
        @endif

        <div class="panel">
            @forelse ($reports as $report)
                <x-report-card :report="$report" />
            @empty
                <p>You haven't submitted any reports yet. Report a pothole, a broken light or a clogged canal and track it here.</p>
                <a class="btn primary" href="{{ route('reports.create') }}">Submit your first report</a>
            @endforelse
        </div>
    </div>
</x-app-layout>
