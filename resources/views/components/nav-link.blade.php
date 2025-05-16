@props(['active'])

@php
$classes = ($active ?? false)
            ? 'btn btn-ghost rounded-btn text-primary border-b-2 border-primary hover:border-primary-focus font-medium'
            : 'btn btn-ghost rounded-btn text-base-content hover:text-primary hover:border-b-2 hover:border-primary/50 font-medium transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="px-2 py-1">{{ $slot }}</span>
</a>