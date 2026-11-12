@props(['report'])
@php
    $reporter = $report->is_anonymous ? 'Anonymous Resident' : ($report->user->name ?? 'Unknown');
@endphp
<tr class="row" data-row
    data-text="{{ strtolower($report->title.' '.$report->location.' '.$report->id) }}"
    data-status="{{ $report->status }}"
    data-category="{{ $report->category }}">
    <td>
        <a href="{{ route('admin.reports.show', $report) }}" style="font-weight:600;color:inherit">{{ $report->title }}</a>
        <div class="mono">FCM-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }} · {{ $report->category }}</div>
        @if ($report->isOverdue())
            <div class="late"><svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M7 1l6.5 11.5h-13z" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M7 5.5v3M7 10v.5" stroke="currentColor" stroke-width="1.5"/></svg>Overdue by {{ (int) (($report->reported_at ?? $report->created_at)->diffInDays(now()) - \App\Models\Report::OVERDUE_AFTER_DAYS) }} days</div>
        @endif
    </td>
    <td>{{ $report->location }}</td>
    <td><x-status-chip :status="$report->status" /></td>
    <td>{{ $report->assigned_to ?: 'Unassigned' }}</td>
    <td>{{ $reporter }}</td>
    <td class="meta">{{ ($report->reported_at ?? $report->created_at)->format('M j, Y') }}</td>
</tr>
