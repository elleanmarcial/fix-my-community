@props(['report'])
<article class="rep">
    <div class="rep-h">
        <div>
            <h3><a href="{{ route('reports.show', $report) }}" style="color:inherit">{{ $report->title }}</a></h3>
            <div class="meta">{{ $report->category }} · {{ $report->location }}</div>
        </div>
        <div style="text-align:right">
            <x-status-chip :status="$report->status" />
            <div class="mono">FCM-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
    <x-progress-track :status="$report->status" />
    <div class="meta">Submitted {{ ($report->reported_at ?? $report->created_at)->diffForHumans() }}</div>
</article>
