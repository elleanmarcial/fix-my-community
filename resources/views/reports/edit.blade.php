<x-app-layout>
    <x-slot name="header"><h1>Edit report</h1></x-slot>
    <div class="fmc-narrow">
        <div class="panel">
            <p>Editing is not available yet.</p>
            <p class="meta">Current report: {{ $report->title }}</p>
            <a class="btn" href="{{ route('reports.show', $report) }}">Back to report</a>
        </div>
    </div>
</x-app-layout>
