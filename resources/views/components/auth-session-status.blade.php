@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'fmc-ok']) }}>
        {{ $status }}
    </div>
@endif
