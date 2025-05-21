<div {{ $attributes->merge(['class' => 'card bg-base-100 shadow-md']) }}>
    @if($title)
        <div class="card-header p-4 border-b border-base-200">
            <h2 class="text-lg font-semibold">{{ $title }}</h2>
        </div>
    @endif

    <div class="card-body p-4">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-footer p-4 bg-base-200 border-t border-base-300">
            {{ $footer }}
        </div>
    @endisset
</div>
