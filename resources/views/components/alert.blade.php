@props(['type' => null, 'message' => null, 'autoHide' => true, 'duration' => 7000])

@php
    // Tentukan tipe dan pesan dari parameter atau session
    $type = $type ?? (session('success') ? 'success' : (session('error') ? 'error' : null));
    $message = $message ?? ($type ? session($type) : null);
    $id = 'alert_' . uniqid();
    $title = $type === 'success' ? 'Berhasil' : 'Terjadi Kesalahan';
@endphp

@if($type && $message)
    <div id="{{ $id }}" class="toast {{ $type }}" role="status" aria-live="{{ $type === 'error' ? 'assertive' : 'polite' }}" style="--toast-duration: {{ (int) $duration }}ms;">
        <div class="toast-glow" aria-hidden="true"></div>
        <div class="toast-icon" aria-hidden="true">
            @if($type === 'success')
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @elseif($type === 'error')
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @endif
        </div>

        <div class="toast-content">
            <div class="toast-title">{{ $title }}</div>
            <div class="toast-message">{{ $message }}</div>
        </div>

        <button type="button" class="toast-close" aria-label="Tutup" title="Tutup">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        @if($autoHide)
            <div class="toast-progress" aria-hidden="true"></div>
        @endif
    </div>

    <script>
        (function() {
            const toast = document.getElementById('{{ $id }}');
            if (!toast) return;

            // Start hidden, then trigger enter animation
            requestAnimationFrame(() => {
                toast.classList.add('show');
            });

            const removeToast = () => {
                // add hide class to animate exit
                toast.classList.add('hide');

                const onTransitionEnd = function(e) {
                    // only respond to opacity/transform transitions
                    if (e.propertyName && (e.propertyName === 'opacity' || e.propertyName === 'transform')) {
                        toast.removeEventListener('transitionend', onTransitionEnd);
                        toast.remove();
                    }
                };

                toast.addEventListener('transitionend', onTransitionEnd);
            };

            const closeBtn = toast.querySelector('.toast-close');
            closeBtn && closeBtn.addEventListener('click', function() {
                removeToast();
            });

            // Auto hide when enabled
            const autoHide = {{ $autoHide ? 'true' : 'false' }};
            if (autoHide) {
                setTimeout(function() {
                    removeToast();
                }, {{ (int) $duration }});
            }
        })();
    </script>
@endif
