<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center btn btn-primary']) }}>
    {{ $slot }}
</button>
