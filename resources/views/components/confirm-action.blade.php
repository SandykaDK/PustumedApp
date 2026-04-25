@props([
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'action' => '#',
    'confirmLabel' => 'OK',
    'method' => 'POST',
    'cancelLabel' => 'Batal',
    'bodyDimClass' => null, // optional: string class to toggle on <body> when modal opens
])

@php
    $passedId = $attributes->get('id');
    $modalId = $passedId ? $passedId.'-modal' : 'confirm-action-'.uniqid();
@endphp

<div {{ $attributes->except('id')->merge(['class' => 'confirm-action-component']) }}>
    <div class="confirm-action-trigger" style="display:inline">
        {{ $slot }}
    </div>

    <div id="{{ $modalId }}" class="confirm-action-modal" style="display:none; position:fixed; inset:0; z-index:9999999 !important; align-items:flex-start; justify-content:center; padding-top:4.5rem; overflow:auto;">
        <div class="cd-overlay" style="position:absolute; inset:0; background:rgba(0,0,0,0.5); z-index:9999998;" data-close></div>
        <div class="cd-box" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title" style="position:relative; background:#fff; max-width:480px; width:90%; padding:1.25rem; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.2); z-index:9999999;">
            <h3 id="{{ $modalId }}-title" style="margin:0 0 .5rem; font-size:1.125rem;">{{ $title }}</h3>
            <p style="margin:0 0 1rem; color:#333;">{{ $message }}</p>
            <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                <button type="button" class="btn btn-secondary cd-cancel" data-cancel style="background: #f3f4f6 !important; color: #111827 !important; padding: 10px 18px !important; border-radius: 10px !important; font-size: 14px !important; border: none !important; cursor: pointer !important; box-shadow: 0 2px 8px rgba(15,23,42,0.04) !important; transition: background-color 120ms linear !important;">{{ $cancelLabel }}</button>
                <form method="POST" action="{{ $action }}" style="margin:0;">
                    @csrf
                    @if(strtoupper($method) !== 'POST')
                        @method($method)
                    @endif
                    <button type="submit" class="btn btn-danger cd-confirm" style="background: #10b981 !important; color: #fff !important; padding: 10px 18px !important; border-radius: 10px !important; font-size: 14px !important; border: none !important; cursor: pointer !important; box-shadow: 0 6px 14px rgba(16,185,129,0.08) !important; transition: background-color 120ms linear !important;">{{ $confirmLabel }}</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Modal transition and stacking */
        .confirm-action-component .confirm-action-modal { /* inline fallback kept on element */ }
        .confirm-action-component .confirm-action-modal .cd-box { transform: translateY(-8px); opacity:0; transition: transform .14s ease, opacity .14s ease; }
        .confirm-action-component .confirm-action-modal.open .cd-box { transform: translateY(0); opacity:1; }

        /* Base button styles - matching modal.css */
        .confirm-action-component .btn { padding: 10px 18px !important; border-radius: 10px !important; font-size: 14px !important; font-weight: 500 !important; border: none !important; transition: background-color 120ms linear, box-shadow 120ms ease !important; cursor: pointer !important; }
        .confirm-action-component .btn.cd-cancel { background: #f3f4f6 !important; color: #111827 !important; box-shadow: 0 2px 8px rgba(15,23,42,0.04) !important; }
        .confirm-action-component .btn.cd-confirm { background: #10b981 !important; color: #fff !important; box-shadow: 0 6px 14px rgba(16,185,129,0.08) !important; }
        .confirm-action-component .btn.cd-cancel:hover { background: #e5e7eb !important; box-shadow: 0 4px 12px rgba(15,23,42,0.08) !important; }
        .confirm-action-component .btn.cd-confirm:hover { background: #059669 !important; box-shadow: 0 8px 16px rgba(5,150,105,0.12) !important; }
        .confirm-action-component .btn:focus { outline: none !important; }
    </style>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    (function() {
        var modal = document.getElementById('{{ $modalId }}');
        if (!modal) return;
        var parent = modal.parentNode;
        var trigger = parent.querySelector('.confirm-action-trigger');
        if (!trigger) return;

        var bodyDimClass = @json($bodyDimClass);
        // keep references so we can temporarily re-parent modal to <body> while open
        var originalParent = modal.parentNode;
        var originalNextSibling = modal.nextSibling;

        function openModal() {
            // move modal to document.body to escape any ancestor stacking-context/filter
            if (modal.parentNode !== document.body) {
                originalParent = modal.parentNode;
                originalNextSibling = modal.nextSibling;
                document.body.appendChild(modal);
            }

            modal.style.display = 'flex';
            modal.classList.add('open');
            if (bodyDimClass) document.body.classList.add(bodyDimClass);
            var focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]');
            if (focusable) focusable.focus();
        }

        function closeModal() {
            modal.classList.remove('open');
            setTimeout(function() {
                modal.style.display = 'none';
                // restore modal to original location so component markup remains in place
                if (originalParent && originalParent !== document.body) {
                    try {
                        if (originalNextSibling && originalNextSibling.parentNode === originalParent) {
                            originalParent.insertBefore(modal, originalNextSibling);
                        } else {
                            originalParent.appendChild(modal);
                        }
                    } catch (e) {
                        // fail silently if DOM changed; modal will remain under body
                    }
                }
            }, 160);
            if (bodyDimClass) document.body.classList.remove(bodyDimClass);
            if (trigger && typeof trigger.focus === 'function') trigger.focus();
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            openModal();
        });

        var cancel = modal.querySelector('[data-cancel]');
        var overlay = modal.querySelector('[data-close]');

        if (cancel) {
            cancel.addEventListener('click', closeModal);
            // hover effect for cancel button
            cancel.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#e5e7eb !important';
                this.style.boxShadow = '0 4px 12px rgba(15,23,42,0.08) !important';
            });
            cancel.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '#f3f4f6 !important';
                this.style.boxShadow = '0 2px 8px rgba(15,23,42,0.04) !important';
            });
        }

        if (overlay) overlay.addEventListener('click', closeModal);

        // hover effect for confirm button
        var confirmBtn = modal.querySelector('.cd-confirm');
        if (confirmBtn) {
            confirmBtn.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#059669 !important';
                this.style.boxShadow = '0 8px 16px rgba(5,150,105,0.12) !important';
            });
            confirmBtn.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '#10b981 !important';
                this.style.boxShadow = '0 6px 14px rgba(16,185,129,0.08) !important';
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
        });
    })();
});
</script>
