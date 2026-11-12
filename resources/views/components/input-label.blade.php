@props(['value'])

<label {{ $attributes->merge(['class' => 'fmc-label']) }}>
    {{ $value ?? $slot }}
</label>
