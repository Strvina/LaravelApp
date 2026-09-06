<button {{ $attributes->merge(['type' => 'button', 'class' => 'secondary-btn disabled:opacity-25']) }}>
    {{ $slot }}
</button>
