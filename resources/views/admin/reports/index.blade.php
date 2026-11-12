<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>Community reports</h1>
            <p>Search, filter and open any report to assign it or update its status.</p>
        </div>
    </x-slot>

    <section class="panel" data-filter-table>
        @if ($reports->isEmpty())
            <p>No reports have been submitted yet.</p>
        @else
            <div class="filters" style="grid-template-columns:2fr 1fr 1fr">
                <label><span class="sr">Search reports</span><input type="search" data-filter-q placeholder="Search title, location or ID"></label>
                <label><span class="sr">Category</span>
                    <select data-filter-category>
                        <option value="">All categories</option>
                        @foreach ($reports->pluck('category')->unique()->sort() as $category)
                            <option>{{ $category }}</option>
                        @endforeach
                    </select>
                </label>
                <label><span class="sr">Status</span>
                    <select data-filter-status>
                        <option value="">All statuses</option>
                        @foreach (['Submitted', 'Verified', 'In Progress', 'Resolved', 'Rejected'] as $status)
                            <option>{{ $status }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="tw">
                <table>
                    <thead><tr><th>Report</th><th>Issue location</th><th>Status</th><th>Assigned to</th><th>Reporter</th><th>Submitted</th></tr></thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <x-report-row :report="$report" />
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p data-empty hidden>No reports match these filters. <button class="btn" type="button" data-clear>Clear filters</button></p>
        @endif
    </section>
</x-app-layout>
