@props([
    'title' => 'Konfirmasi Pengajuan',
    'message' => 'Apakah Anda yakin ingin menyetujui pengajuan pemusnahan obat ini?',
    'action' => '#',
    'confirmLabel' => 'Setuju',
    'method' => 'POST'
])

<div {{ $attributes->except('id')->merge(['class' => 'confirm-approve-component']) }}>
    <style>
        /* Unified overlay for consistent dimming */
        body.confirm-approve-open::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999997;
            pointer-events: none;
        }

        /* Ensure the modal for approve sits above the overlay */
        .confirm-approve-component .confirm-action-modal { z-index: 9999999 !important; }
        .confirm-approve-component .confirm-action-modal .cd-box { z-index: 9999999 !important; }
    </style>

    <x-confirm-action :title="$title" :message="$message" :action="$action" :confirmLabel="$confirmLabel" :method="$method" bodyDimClass="confirm-approve-open" {{ $attributes->only('id') }}>
        {{ $slot }}
    </x-confirm-action>
</div>
