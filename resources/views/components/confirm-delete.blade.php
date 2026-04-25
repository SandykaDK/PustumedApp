@props(['title' => 'Konfirmasi Penghapusan', 'message' => 'Apakah Anda yakin ingin menghapus item ini?', 'action' => '#', 'method' => 'DELETE'])

<div {{ $attributes->except('id')->merge(['class' => 'confirm-delete-component']) }}>
    <style>
        /* Unified overlay for consistent dimming (same as approve) */
        body.confirm-approve-open::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999997;
            pointer-events: none;
        }

        /* Ensure modal sits above overlay */
        .confirm-delete-component .confirm-action-modal { z-index: 9999999 !important; }
        .confirm-delete-component .confirm-action-modal .cd-box { z-index: 9999999 !important; }
    </style>

    <x-confirm-action :title="$title" :message="$message" :action="$action" confirmLabel="Hapus" :method="$method" bodyDimClass="confirm-approve-open" {{ $attributes->only('id') }}>
        {{ $slot }}
    </x-confirm-action>
</div>
