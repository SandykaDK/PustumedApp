<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Permintaan Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/permintaan_obat/permintaan_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
</head>
<body>
    <x-sidebar />
    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">
            <div class="page-header">
                <h1>Permintaan Obat</h1>
                <p>Laporan pemakaian dan lembar permintaan obat bulan {{ $monthLabel }} {{ $selectedYear }}</p>
            </div>

            <x-alert type="success" />
            <x-alert type="error" />

            <div class="card">
                <div class="table-actions">
                    <form method="GET" action="{{ route('permintaan-obat.index') }}">
                        <div class="filter-row">
                            <div class="search-wrapper">
                                <span class="search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" class="search-input" placeholder="Cari nama obat..." value="{{ $search }}">
                            </div>

                            <div class="date-input-group">
                                <label for="month_year" class="date-label">Bulan / Tahun</label>
                                <input id="month_year" type="month" name="month_year" class="date-input month-year-input" value="{{ $monthYearValue }}" min="{{ min($yearOptions) }}-01" max="{{ max($yearOptions) }}-12">
                            </div>



                            <a href="{{ route('permintaan-obat.index') }}" class="btn-filter btn-reset">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.995-1.465" />
                                </svg>
                                <span>Reset</span>
                            </a>

                            <button type="submit" name="print" value="1" class="btn-filter btn-print" @if(!empty($reportNotice)) data-confirm-message="{{ $reportNotice }}" @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                <span>Cetak</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="confirm-modal" id="draftPrintModal" aria-hidden="true">
                    <div class="confirm-modal__backdrop" data-confirm-close></div>
                    <div class="confirm-modal__panel" role="dialog" aria-modal="true" aria-labelledby="draftPrintTitle">
                        <div class="confirm-modal__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3l-8.47-14.14a2 2 0 0 0-3.42 0Z" />
                            </svg>
                        </div>
                        <h3 id="draftPrintTitle">Cetak sebagai draft?</h3>
                        <p id="draftPrintMessage"></p>
                        <div class="confirm-modal__actions">
                            <button type="button" class="confirm-modal__button confirm-modal__button--ghost" data-confirm-close>Batal</button>
                            <button type="button" class="confirm-modal__button confirm-modal__button--primary" id="draftPrintConfirm">Cetak</button>
                        </div>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="permintaan-obat-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Sat</th>
                                <th>Stok Awal</th>
                                <th>Persediaan</th>
                                <th>Pemakaian</th>
                                <th>Sisa Stok</th>
                                <th>Permintaan</th>
                                <th>Pemberian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $index }}</td>
                                    <td>{{ $item['nama_obat'] }}</td>
                                    <td>{{ $item['satuan'] }}</td>
                                    <td>{{ number_format($item['stok_awal']) }}</td>
                                    <td>{{ number_format($item['persediaan']) }}</td>
                                    <td>{{ number_format($item['pemakaian']) }}</td>
                                    <td>{{ number_format($item['sisa_stok']) }}</td>
                                    <td>{{ number_format($item['permintaan']) }}</td>
                                    <td>{{ number_format($item['pemberian']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="empty">Tidak ada data permintaan obat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <div class="pagination-section">
                    <div class="pagination-controls">
                        <div class="per-page-selector">
                            <form method="GET" action="{{ route('permintaan-obat.index') }}" class="per-page-form">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="month_year" value="{{ request('month_year', $monthYearValue) }}">
                                <label for="per_page_footer" class="per-page-label">Tampilkan:</label>
                                <select name="per_page" id="per_page_footer" class="per-page-input" onchange="this.form.submit()">
                                    <option value="10" {{ $perPageOption == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPageOption == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPageOption == 50 ? 'selected' : '' }}>50</option>
                                    <option value="all" {{ $perPageOption == 'all' ? 'selected' : '' }}>Semua</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="pagination-wrapper">
                        {{ $items->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    (function() {
        const filterForm = document.querySelector('.table-actions form[method="GET"]');
        if (!filterForm) return;

        const searchField = filterForm.querySelector('input[name="search"]');
        const filterFields = filterForm.querySelectorAll('select, input[type="date"], input[type="month"]');
        const printButton = filterForm.querySelector('button[name="print"]');
        const printModal = document.getElementById('draftPrintModal');
        const printModalMessage = document.getElementById('draftPrintMessage');
        const printModalConfirm = document.getElementById('draftPrintConfirm');
        const printModalCloseButtons = printModal ? printModal.querySelectorAll('[data-confirm-close]') : [];
        let filterDebounceTimer = null;
        let printSubmitBypass = false;

        const submitFilter = () => filterForm.submit();
        const submitFilterDebounced = (delay = 450) => {
            if (filterDebounceTimer) clearTimeout(filterDebounceTimer);
            filterDebounceTimer = setTimeout(submitFilter, delay);
        };

        if (searchField) {
            searchField.addEventListener('input', function() {
                submitFilterDebounced(500);
            });

            searchField.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitFilterDebounced(0);
                }
            });
        }

        filterFields.forEach(function(field) {
            field.addEventListener('change', function() {
                submitFilterDebounced(250);
            });
        });

        filterForm.addEventListener('submit', function(event) {
            if (printSubmitBypass) {
                printSubmitBypass = false;
                return;
            }

            const submitter = event.submitter || document.activeElement;
            const isPrintSubmit = submitter && submitter === printButton;
            const confirmMessage = printButton ? printButton.dataset.confirmMessage : null;

            if (isPrintSubmit && confirmMessage && printModal) {
                event.preventDefault();
                openPrintModal(confirmMessage + ' Lanjut cetak draft?');
            }
        });

        const openPrintModal = (message) => {
            if (!printModal || !printModalMessage || !printModalConfirm) return;

            printModalMessage.textContent = message;
            printModal.classList.add('is-open');
            printModal.setAttribute('aria-hidden', 'false');
            printModalConfirm.focus();
        };

        const closePrintModal = () => {
            if (!printModal) return;

            printModal.classList.remove('is-open');
            printModal.setAttribute('aria-hidden', 'true');
        };

        if (printModalConfirm) {
            printModalConfirm.addEventListener('click', function() {
                closePrintModal();
                printSubmitBypass = true;
                filterForm.requestSubmit(printButton);
            });
        }

        printModalCloseButtons.forEach(function(button) {
            button.addEventListener('click', closePrintModal);
        });

        if (printModal) {
            printModal.addEventListener('click', function(event) {
                if (event.target === printModal) {
                    closePrintModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && printModal.classList.contains('is-open')) {
                    closePrintModal();
                }
            });
        }
    })();
</script>
</body>
</html>
