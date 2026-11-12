<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const STATUSES = ['Submitted', 'Verified', 'In Progress', 'Resolved', 'Rejected'];

    public function index(): View
    {
        $counts = Report::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $count = fn (string $status): int => (int) ($counts[$status] ?? 0);
        $totalReports = (int) $counts->sum();

        // Open reports drive the action queue and the assignment panel.
        $open = Report::with('user')
            ->whereNotIn('status', ['Resolved', 'Rejected'])
            ->orderBy('created_at')
            ->get();

        $unassigned = $open->filter(fn ($r) => blank($r->assigned_to))->count();
        $overdue = $open->filter(fn ($r) => $r->isOverdue())->count();
        $staffAssigned = Report::whereNotNull('assigned_to')
            ->where('assigned_to', '!=', '')
            ->distinct()
            ->count('assigned_to');

        // Seven weeks of trend data, oldest first.
        $weekStarts = collect(range(6, 0))->map(fn ($i) => now()->startOfWeek()->subWeeks($i));
        $since = $weekStarts->first();

        $received = Report::where('created_at', '>=', $since)->get(['status', 'created_at']);
        $resolvedRecent = Report::whereNotNull('resolved_at')
            ->where('resolved_at', '>=', $since)
            ->get(['reported_at', 'created_at', 'resolved_at']);

        $inWeek = fn ($date, Carbon $start) => $date >= $start && $date < $start->copy()->addWeek();
        $daysToResolve = fn ($r) => abs(($r->reported_at ?? $r->created_at)->diffInHours($r->resolved_at)) / 24;

        $stack = [];
        foreach (self::STATUSES as $status) {
            $stack[$status] = $weekStarts->map(
                fn ($w) => $received->filter(fn ($r) => $r->status === $status && $inWeek($r->created_at, $w))->count()
            )->all();
        }

        $charts = [
            'weeks' => $weekStarts->map(fn ($w) => $w->format('M j'))->all(),
            'stack' => $stack,
            'resolved' => $weekStarts->map(
                fn ($w) => $resolvedRecent->filter(fn ($r) => $inWeek($r->resolved_at, $w))->count()
            )->all(),
            'days' => $weekStarts->map(function ($w) use ($resolvedRecent, $inWeek, $daysToResolve) {
                $rows = $resolvedRecent->filter(fn ($r) => $inWeek($r->resolved_at, $w));

                return $rows->isEmpty() ? null : round($rows->avg($daysToResolve), 1);
            })->all(),
            'categories' => Report::selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($r) => [$r->category, (int) $r->total])
                ->values()
                ->all(),
        ];

        $allResolved = Report::whereNotNull('resolved_at')->get(['reported_at', 'created_at', 'resolved_at']);
        $averageDays = $allResolved->isEmpty() ? null : round($allResolved->avg($daysToResolve), 1);

        return view('admin.dashboard', [
            'totalReports' => $totalReports,
            'submittedReports' => $count('Submitted'),
            'verifiedReports' => $count('Verified'),
            'inProgressReports' => $count('In Progress'),
            'resolvedReports' => $count('Resolved'),
            'rejectedReports' => $count('Rejected'),
            'unassigned' => $unassigned,
            'assignedOpen' => $open->count() - $unassigned,
            'staffAssigned' => $staffAssigned,
            'overdue' => $overdue,
            'averageDays' => $averageDays,
            'resolvedThisWeek' => $allResolved->filter(fn ($r) => $r->resolved_at >= now()->startOfWeek())->count(),
            'resolutionRate' => $totalReports > 0 ? round($count('Resolved') / $totalReports * 100) : 0,
            'queue' => $open->take(8),
            'openCount' => $open->count(),
            'charts' => $charts,
        ]);
    }
}
