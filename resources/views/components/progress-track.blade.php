@props(['status'])
@php
    $flow = ['Submitted', 'Verified', 'In Progress', 'Resolved'];
    $i = array_search($status, $flow);
@endphp
@if ($status === 'Rejected')
    <p class="late">This report was not accepted. See the notes from your barangay for details.</p>
@else
    <ol class="track" aria-label="Progress: {{ $status }}">
        @foreach ($flow as $j => $f)
            <li class="{{ $i !== false && $j <= $i ? 'done' : '' }} {{ $j === $i ? 'now' : '' }}">{{ $f }}</li>
        @endforeach
    </ol>
@endif
