@props([
    'class' => 'btn primary btn-icon-only',
    'label' => 'Thêm vào giỏ hàng',
])

<button class="{{ $class }}" type="submit" aria-label="{{ $label }}">
    <svg class="btn-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <circle cx="9" cy="20" r="1.5" fill="currentColor"></circle>
        <circle cx="17" cy="20" r="1.5" fill="currentColor"></circle>
        <path d="M3 4h2l2.3 10.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L20 7H7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M13 9v4m-2-2h4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
    </svg>
    <span class="sr-only">{{ $label }}</span>
</button>
