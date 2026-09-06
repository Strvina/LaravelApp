<button {{ $attributes->merge(['type' => 'submit', 'class' => 'danger-btn']) }}>
    {{ $slot }}
</button>
