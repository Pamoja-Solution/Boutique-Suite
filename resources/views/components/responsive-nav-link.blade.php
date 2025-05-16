@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-4 py-3 border-l-4 border-primary text-primary font-medium bg-primary/10 hover:bg-primary/20 transition-colors duration-200'
            : 'block w-full ps-4 py-3 border-l-4 border-transparent text-base-content hover:text-primary hover:bg-base-200/50 hover:border-primary/30 transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>