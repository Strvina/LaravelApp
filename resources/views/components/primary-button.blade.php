<button {{ $attributes->merge(['type' => 'submit', 'class' => 'primary-btn']) }}>
    {{ $slot }}
</button>
