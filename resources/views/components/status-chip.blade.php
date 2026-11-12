@props(['status'])
@php
    $cls = ['Submitted' => 's-submitted', 'Verified' => 's-verified', 'In Progress' => 's-progress', 'Resolved' => 's-resolved', 'Rejected' => 's-rejected'][$status] ?? 's-submitted';
@endphp
<span class="chip {{ $cls }}"><i></i>{{ $status }}</span>
