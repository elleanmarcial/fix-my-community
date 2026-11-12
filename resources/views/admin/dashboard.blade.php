<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>Community overview</h1>
            <p>Report monitoring for your barangay.</p>
        </div>
        <a class="btn primary block-m" href="{{ route('admin.reports.index') }}">Open all reports</a>
    </x-slot>

    <div class="sp3" style="margin-top:0">
        <section class="sp" aria-labelledby="p1">
            <h2 id="p1">Report status</h2>
            <div class="stats">
                <div><b>{{ $submittedReports }}</b><span>Waiting for review</span></div>
                <div><b>{{ $verifiedReports }}</b><span>Verified</span></div>
                <div><b>{{ $inProgressReports }}</b><span>In progress</span></div>
                <div><b>{{ $resolvedReports }}</b><span>Resolved</span></div>
            </div>
        </section>
        <section class="sp" aria-labelledby="p2">
            <h2 id="p2">Assignments</h2>
            <div class="stats">
                <div><b>{{ $unassigned }}</b><span>Open and unassigned</span></div>
                <div><b>{{ $assignedOpen }}</b><span>Open and assigned</span></div>
                <div><b>{{ $staffAssigned }}</b><span>Staff with reports</span></div>
                <div class="bad"><b>{{ $overdue }}</b><span>Overdue reports</span></div>
            </div>
        </section>
        <section class="sp" aria-labelledby="p3">
            <h2 id="p3">Resolution</h2>
            <div class="stats">
                <div><b>{{ $averageDays !== null ? $averageDays.' days' : 'None yet' }}</b><span>Average time to resolve</span></div>
                <div><b>{{ $resolutionRate }}%</b><span>Resolution rate</span></div>
                <div><b>{{ $resolvedThisWeek }}</b><span>Resolved this week</span></div>
                <div><b>{{ $totalReports }}</b><span>Total reports</span></div>
            </div>
        </section>
    </div>

    <div class="charts">
        <section class="ch"><h3>Reports received each week</h3><div id="c-stack"></div></section>
        <section class="ch"><h3>Reports by category</h3><div id="c-donut" class="donutwrap"></div></section>
        <section class="ch"><h3>Average days to resolve</h3><div id="c-line"></div></section>
        <section class="ch"><h3>Reports resolved each week</h3><div id="c-bar"></div></section>
    </div>
    <script type="application/json" id="fmc-chart-data">@json($charts)</script>

    <section class="panel">
        <div class="fmc-title" style="margin-bottom:0">
            <h2>Reports requiring action</h2>
            @if ($openCount > $queue->count())
                <a class="link" href="{{ route('admin.reports.index') }}">View all {{ $openCount }} open reports</a>
            @endif
        </div>
        @if ($queue->isEmpty())
            <p>Nothing needs action right now. New reports will appear here as residents submit them.</p>
        @else
            <div class="tw">
                <table>
                    <thead><tr><th>Report</th><th>Issue location</th><th>Status</th><th>Assigned to</th><th>Reporter</th><th>Submitted</th></tr></thead>
                    <tbody>
                        @foreach ($queue as $report)
                            <x-report-row :report="$report" />
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-app-layout>
