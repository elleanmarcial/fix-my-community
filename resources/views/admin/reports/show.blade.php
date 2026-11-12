<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>{{ $report->title }}</h1>
            <p class="mono">FCM-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <x-status-chip :status="$report->status" />
    </x-slot>

    @if (session('success'))
        <div class="fmc-ok">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="panel" role="alert" style="border-color:var(--red)">
            <h3 style="color:var(--red)">Unable to update report</h3>
            <ul class="fmc-error">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid2">
        <div style="display:grid;gap:16px">
            <section class="panel">
                <h2>What was reported</h2>

                @if ($report->isOverdue())
                    <p class="late" style="margin:8px 0 0">
                        Overdue: open for more than
                        {{ \App\Models\Report::OVERDUE_AFTER_DAYS }} days
                    </p>
                @endif

                <p style="white-space:pre-line;max-width:70ch">
                    {{ $report->description }}
                </p>

                @if ($report->photo_path)
                    <img
                        class="fmc-photo"
                        src="{{ asset('storage/' . $report->photo_path) }}"
                        alt="Photo submitted with this report"
                        style="max-height:360px"
                    >
                @endif
            </section>

            <section class="panel">
                <h2>Details</h2>

                <dl class="fmc-dl" style="margin-top:12px">
                    <div>
                        <dt>Category</dt>
                        <dd>{{ $report->category }}</dd>
                    </div>

                    <div>
                        <dt>Issue location</dt>
                        <dd>
                            {{ $report->location }}

                            @if ($report->latitude !== null && $report->longitude !== null)
                                <br>
                                <a class="link"
                                    href="https://www.openstreetmap.org/?mlat={{ $report->latitude }}&mlon={{ $report->longitude }}#map=18/{{ $report->latitude }}/{{ $report->longitude }}"
                                    target="_blank"
                                    rel="noopener">
                                    View pin on map
                                </a>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt>Submitted</dt>
                        <dd>
                            {{ ($report->reported_at ?? $report->created_at)->format('M j, Y g:i A') }}
                        </dd>
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

                <div class="anon" style="margin-top:16px">
                    <span>
                        <b>
                            {{ $report->is_anonymous
                                ? 'Anonymous Resident'
                                : ($report->user->name ?? 'Unknown') }}
                        </b>
                        <br>
                        <span class="meta">
                            {{ $report->is_anonymous
                                ? 'The resident chose to submit this report anonymously.'
                                : 'Reporter' }}
                        </span>
                    </span>
                </div>
            </section>
        </div>

        <div style="display:grid;gap:16px;align-content:start">
            {{-- Manage Report --}}
            <form class="panel form"
                method="POST"
                action="{{ route('admin.reports.update', $report) }}"
                data-confirm-status
                style="margin-top:0">

                @csrf
                @method('PATCH')

                <h2>Manage report</h2>

                <div>
                    <x-input-label for="status" value="Status" />

                    <select id="status" name="status"
                        data-initial="{{ $report->status }}" required>

                        @foreach (['Submitted', 'Verified', 'In Progress', 'Resolved', 'Rejected'] as $status)
                            <option value="{{ $status }}"
                                @selected(old('status', $report->status) === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-error :messages="$errors->get('status')" />
                </div>

                <div>
                    <x-input-label for="assigned_to" value="Assigned to" />

                    <x-text-input
                        id="assigned_to"
                        name="assigned_to"
                        type="text"
                        maxlength="255"
                        :value="old('assigned_to', $report->assigned_to)"
                        placeholder="Name of the responsible staff member"
                    />

                    <x-input-error :messages="$errors->get('assigned_to')" />
                </div>

                <div>
                    <x-input-label for="new_note" value="New note" />

                    <textarea
                        id="new_note"
                        name="new_note"
                        rows="5"
                        placeholder="Write a new update for the resident..."
                    >{{ old('new_note') }}</textarea>

                    <p class="meta" style="margin:4px 0 0">
                        Each new note is saved separately and added to Notes History.
                    </p>

                    <x-input-error :messages="$errors->get('new_note')" />
                </div>

                <div class="fmc-flex">
                    <x-primary-button>Save changes</x-primary-button>

                    <a class="btn" href="{{ route('admin.reports.index') }}">
                        Back to reports
                    </a>
                </div>
            </form>

            {{-- Notes History Preview --}}
            <section class="panel" id="notes-history">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:10px">
                    <h2>Notes History</h2>

                    <span class="meta">
                        {{ $report->notes->count() }} {{ \Illuminate\Support\Str::plural('update', $report->notes->count()) }}
                    </span>
                </div>

                @if ($report->notes->isEmpty())
                    <p class="meta" style="margin-top:14px">
                        No notes have been added yet.
                    </p>
                @else
                    <div style="display:grid;gap:14px;margin-top:16px">
                        @foreach ($report->notes->take(4) as $note)
                            <div style="padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,.15)">
                                <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap">
                                    <b>
                                        {{ $loop->first ? 'Latest note' : 'Previous note' }}
                                    </b>

                                    <span class="meta">
                                        {{ $note->created_at->format('M j, Y g:i A') }}
                                    </span>
                                </div>

                                <p style="white-space:pre-line;margin-top:6px">
                                    {{ $note->note }}
                                </p>

                                <p class="meta" style="margin-top:6px">
                                    By {{ $note->author->name ?? 'Unknown user' }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    @if ($report->notes->count() > 4)
                        <details style="margin-top:14px">
                            <summary class="link" style="cursor:pointer">
                                View all {{ $report->notes->count() }} notes
                            </summary>

                            <div style="display:grid;gap:14px;margin-top:16px">
                                @foreach ($report->notes->skip(4) as $note)
                                    <div style="padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,.15)">
                                        <b>Previous note</b>

                                        <p style="white-space:pre-line;margin-top:6px">
                                            {{ $note->note }}
                                        </p>

                                        <p class="meta" style="margin-top:6px">
                                            {{ $note->author->name ?? 'Unknown user' }}
                                            · {{ $note->created_at->format('M j, Y g:i A') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endif
            </section>
        </div>
    </div>
</x-app-layout>