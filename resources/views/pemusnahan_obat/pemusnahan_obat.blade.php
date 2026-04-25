<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemusnahan Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pemusnahan_obat/pemusnahan_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
</head>
<body>

    <x-sidebar />
    <x-navbar />

    <div class="main-wrapper">
        <div class="container main-content">
            <div class="page-header">
                <h1>Pemusnahan Obat - Mendekati Kadaluwarsa (&lt; 30 hari)</h1>
            </div>

            <div class="card">
                <div class="table-actions">
                    <form method="GET" action="{{ route('pemusnahan-obat.index') }}">
                        <input type="hidden" name="tab" id="currentTabInput" value="{{ $activeTab ?? 'belum_diajukan' }}">
                        <div class="filter-row">
                            <div class="search-wrapper">
                                <span class="search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" class="search-input" placeholder="Cari nama obat..." value="{{ $search }}">
                            </div>
                            <button type="submit" class="btn-filter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span>Cari</span>
                            </button>
                        </div>
                    </form>
                </div>

                @php $role = auth()->user()?->role ?? null; @endphp

                @php
                    $petugasRoles = ['petugas_obat','petugas_gudang','petugas_administrasi'];
                    $role = auth()->user()?->role ?? null;
                @endphp

                <div class="tabs-bar" style="margin-top:12px;">
                    <div class="tabs" role="tablist" aria-label="Pemusnahan tabs">
                        @if(in_array($role, $petugasRoles))
                            <button class="tab-btn" data-tab="belum_diajukan">Belum Diajukan</button>
                            <button class="tab-btn" data-tab="sudah_diajukan">Sudah Diajukan</button>
                            <button class="tab-btn" data-tab="sudah_disetujui">Sudah Disetujui</button>
                            <button class="tab-btn" data-tab="sudah_dimusnahkan">Sudah Dimusnahkan</button>
                        @elseif($role === 'kepala_pustu')
                            <button class="tab-btn" data-tab="belum_dikonfirmasi">Belum Dikonfirmasi</button>
                            <button class="tab-btn" data-tab="sudah_dikonfirmasi">Sudah Dikonfirmasi</button>
                            <button class="tab-btn" data-tab="sudah_dimusnahkan">Sudah Dimusnahkan</button>
                        @else
                            <button class="tab-btn" data-tab="belum_diajukan">Belum Diajukan</button>
                            <button class="tab-btn" data-tab="sudah_diajukan">Sudah Diajukan</button>
                            <button class="tab-btn" data-tab="sudah_dimusnahkan">Sudah Dimusnahkan</button>
                        @endif
                    </div>
                </div>

                <!-- single table container: contents swap depending on active tab -->
                <div id="mainTableContainer" class="table-wrapper" style="margin-top:12px;">
                    {{-- default content will be injected by JS on load (templates rendered below) --}}
                </div>

                <!-- templates for each tab (rendered server-side, used by JS to swap into the single table) -->

                <script type="text/template" id="tpl-belum_diajukan">
                    <table class="pemusnahan-obat-table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'nama_obat', 'direction' => $sort_by === 'nama_obat' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Nama Obat
                                        @if($sort_by === 'nama_obat')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>No. Batch</th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_kadaluwarsa', 'direction' => $sort_by === 'tanggal_kadaluwarsa' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Kadaluwarsa
                                        @if($sort_by === 'tanggal_kadaluwarsa')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'sisa_hari', 'direction' => $sort_by === 'sisa_hari' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Sisa Hari
                                        @if($sort_by === 'sisa_hari')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'jumlah_obat', 'direction' => $sort_by === 'jumlah_obat' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Jumlah Obat
                                        @if($sort_by === 'jumlah_obat')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stokNearExpire as $i => $stok)
                                <tr>
                                    <td>{{ $stok->namaObat?->nama_obat ?? '-' }}</td>
                                    <td>{{ $stok->no_batch ?? '-' }}</td>
                                    <td>{{ optional($stok->tanggal_kadaluwarsa)->translatedFormat('d F Y') }}</td>
                                    @php
                                        $remaining = null;
                                        if ($stok->tanggal_kadaluwarsa) {
                                            $remaining = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($stok->tanggal_kadaluwarsa), false);
                                        }
                                    @endphp
                                    <td>
                                        @if(is_null($remaining)) - @elseif($remaining > 0) {{ $remaining }} hari @elseif($remaining === 0) Hari ini @else Kadaluarsa @endif
                                    </td>
                                    <td>{{ $stok->stok }}</td>
                                    <td>
                                        @if(in_array($role, $petugasRoles))
                                            <button class="btn-apply-pemusnahan"
                                                data-stok-id="{{ $stok->id }}"
                                                data-nama-id="{{ $stok->nama_obat_id }}"
                                                data-tanggal="{{ optional($stok->tanggal_kadaluwarsa)->toDateString() }}"
                                                data-stok-qty="{{ $stok->stok }}"
                                                data-no-batch="{{ $stok->no_batch }}"
                                                data-lokasi="{{ $stok->keterangan ?? '' }}">Ajukan</button>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty">Tidak ada stok yang mendekati kadaluwarsa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </script>

                <script type="text/template" id="tpl-sudah_diajukan">
                    <table class="pemusnahan-obat-table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'nama_obat', 'direction' => $sort_by === 'nama_obat' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Nama Obat
                                        @if($sort_by === 'nama_obat')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>No. Batch</th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_kadaluwarsa', 'direction' => $sort_by === 'tanggal_kadaluwarsa' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Kadaluwarsa
                                        @if($sort_by === 'tanggal_kadaluwarsa')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'sisa_hari', 'direction' => $sort_by === 'sisa_hari' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Sisa Hari
                                        @if($sort_by === 'sisa_hari')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'jumlah', 'direction' => $sort_by === 'jumlah' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Jumlah Obat
                                        @if($sort_by === 'jumlah')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_pengajuan', 'direction' => $sort_by === 'tanggal_pengajuan' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Pengajuan
                                        @if($sort_by === 'tanggal_pengajuan')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $myPending = $pending->where('user_id', auth()->id());
                            @endphp
                            @forelse($myPending as $i => $req)
                                @foreach($req->details as $detail)
                                    <tr>
                                        <td>{{ $detail->namaObat?->nama_obat ?? '-' }}</td>
                                        <td>{{ $detail->stok?->no_batch ?? '-' }}</td>
                                        <td>{{ optional($detail->stok?->tanggal_kadaluwarsa)->translatedFormat('d F Y') }}</td>
                                        @php
                                            $rem = null;
                                            if ($detail->stok?->tanggal_kadaluwarsa) {
                                                $rem = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($detail->stok->tanggal_kadaluwarsa), false);
                                            }
                                        @endphp
                                        <td>@if(is_null($rem)) - @elseif($rem > 0) {{ $rem }} hari @elseif($rem === 0) Hari ini @else Kadaluarsa @endif</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        @php
                                            $displayDate = '-';
                                            if (!empty($req->tanggal_pemusnahan)) {
                                                try {
                                                    $displayDate = \Carbon\Carbon::parse($req->tanggal_pemusnahan)->translatedFormat('d F Y');
                                                } catch (\Exception $e) {
                                                    $displayDate = (string) $req->tanggal_pemusnahan;
                                                }
                                            } elseif (!empty($req->created_at)) {
                                                try {
                                                    $displayDate = \Carbon\Carbon::parse($req->created_at)->translatedFormat('d F Y');
                                                } catch (\Exception $e) {
                                                    $displayDate = (string) $req->created_at;
                                                }
                                            }
                                        @endphp
                                        <td>{{ $displayDate }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn edit viewPemusnahan" data-req='@json($req)' title="Lihat">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322C3.2 7.036 7.522 4 12 4c4.478 0 8.8 3.036 9.964 8.322a1.125 1.125 0 0 1 0 .356C20.8 16.964 16.478 20 12 20c-4.478 0-8.8-3.036-9.964-8.322a1.125 1.125 0 0 1 0-.356z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                                                </button>
                                                @if(auth()->id() === $req->user_id)
                                                    <x-confirm-delete :action="url('/pemusnahan-obat/'.$req->id.'/cancel')" title="Konfirmasi Pembatalan" message="Yakin ingin membatalkan pengajuan ini?" confirmLabel="Batal" method="POST">
                                                        <button type="button" class="action-btn delete" title="Batal Pengajuan">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </x-confirm-delete>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="7" class="empty">Anda belum mengajukan pemusnahan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </script>

                <script type="text/template" id="tpl-sudah_disetujui">
                    <table class="pemusnahan-obat-table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'nama_obat', 'direction' => $sort_by === 'nama_obat' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Nama Obat
                                        @if($sort_by === 'nama_obat')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>No. Batch</th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_kadaluwarsa', 'direction' => $sort_by === 'tanggal_kadaluwarsa' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Kadaluwarsa
                                        @if($sort_by === 'tanggal_kadaluwarsa')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'jumlah', 'direction' => $sort_by === 'jumlah' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Jumlah Obat
                                        @if($sort_by === 'jumlah')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'approved_at', 'direction' => $sort_by === 'approved_at' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Disetujui
                                        @if($sort_by === 'approved_at')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $myApproved = $approved->where('user_id', auth()->id()); @endphp
                            @forelse($myApproved as $i => $req)
                                @foreach($req->details as $detail)
                                    <tr>
                                        <td>{{ $detail->namaObat?->nama_obat ?? '-' }}</td>
                                        <td>{{ $detail->stok?->no_batch ?? '-' }}</td>
                                        <td>{{ optional($detail->stok?->tanggal_kadaluwarsa)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        @php
                                            $approvalDate = '-';
                                            if (!empty($req->approved_at)) {
                                                try {
                                                    $dt = \Carbon\Carbon::parse($req->approved_at);
                                                    if ($dt->format('H:i:s') === '00:00:00') {
                                                        $approvalDate = $dt->translatedFormat('d F Y');
                                                    } else {
                                                        $approvalDate = $dt->translatedFormat('d F Y, H:i:s');
                                                    }
                                                } catch (\Exception $e) {
                                                    $approvalDate = (string) $req->approved_at;
                                                }
                                            }
                                        @endphp
                                        <td>{{ $approvalDate }}</td>
                                        <td>
                                            <button class="action-btn process processPemusnahan" data-id="{{ $req->id }}" data-req='@json($req)' title="Proses">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="8" class="empty">Belum ada pengajuan Anda yang disetujui.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </script>

                <script type="text/template" id="tpl-sudah_dimusnahkan">
                    <table class="pemusnahan-obat-table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'nama_obat', 'direction' => $sort_by === 'nama_obat' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Nama Obat
                                        @if($sort_by === 'nama_obat')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>No. Batch</th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_kadaluwarsa', 'direction' => $sort_by === 'tanggal_kadaluwarsa' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Kadaluwarsa
                                        @if($sort_by === 'tanggal_kadaluwarsa')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'jumlah', 'direction' => $sort_by === 'jumlah' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Jumlah Obat
                                        @if($sort_by === 'jumlah')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_pemusnahan', 'direction' => $sort_by === 'tanggal_pemusnahan' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Pemusnahan
                                        @if($sort_by === 'tanggal_pemusnahan')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Bukti Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dimusnahkan as $i => $req)
                                @foreach($req->details as $detail)
                                    <tr>
                                        <td>{{ $detail->namaObat?->nama_obat ?? '-' }}</td>
                                        <td>{{ $detail->stok?->no_batch ?? '-' }}</td>
                                        <td>{{ optional($detail->stok?->tanggal_kadaluwarsa)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>{{ optional($req->tanggal_pemusnahan)->translatedFormat('d F Y') }}</td>
                                        <td>
                                            @if($req->bukti_foto)
                                                <a href="{{ route('pemusnahan-obat.download-foto', $req->id) }}" target="_blank">Lihat</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <button class="action-btn edit viewPemusnahan" data-req='@json($req)' title="Lihat">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322C3.2 7.036 7.522 4 12 4c4.478 0 8.8 3.036 9.964 8.322a1.125 1.125 0 0 1 0 .356C20.8 16.964 16.478 20 12 20c-4.478 0-8.8-3.036-9.964-8.322a1.125 1.125 0 0 1 0-.356z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="7" class="empty">Belum ada data pemusnahan yang selesai.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </script>

                <script type="text/template" id="tpl-belum_dikonfirmasi">
                    <table class="pemusnahan-obat-table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_pemusnahan', 'direction' => $sort_by === 'tanggal_pemusnahan' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Pengajuan
                                        @if($sort_by === 'tanggal_pemusnahan')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'user_name', 'direction' => $sort_by === 'user_name' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Pengaju
                                        @if($sort_by === 'user_name')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Nama Obat</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pending ?? [] as $i => $req)
                                <tr>
                                    <td>{{ optional($req->tanggal_pemusnahan)->translatedFormat('d F Y, H:i:s') }}</td>
                                    <td>{{ $req->user?->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $names = [];
                                            if ($req->details) {
                                                foreach ($req->details as $detail) {
                                                    if ($detail->namaObat) {
                                                        $names[] = $detail->namaObat->nama_obat;
                                                    }
                                                }
                                            }
                                            $displayNames = !empty($names) ? implode(', ', $names) : '-';
                                        @endphp
                                        {{ $displayNames }}
                                    </td>
                                    <td>{{ Str::limit($req->keterangan, 40) }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit viewPemusnahan" data-req='@json($req)' title="Lihat">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322C3.2 7.036 7.522 4 12 4c4.478 0 8.8 3.036 9.964 8.322a1.125 1.125 0 0 1 0 .356C20.8 16.964 16.478 20 12 20c-4.478 0-8.8-3.036-9.964-8.322a1.125 1.125 0 0 1 0-.356z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                                            </button>
                                            @if(auth()->user()?->role === 'kepala_pustu')
                                                <x-confirm-approve :action="url('/pemusnahan-obat/'.$req->id.'/approve')" title="Konfirmasi Persetujuan" message="Anda akan menyetujui permintaan ini. Lanjutkan?" confirmLabel="Setujui" method="POST">
                                                    <button type="button" class="action-btn approve" title="Setujui">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    </button>
                                                </x-confirm-approve>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty">Tidak ada request pemusnahan yang menunggu konfirmasi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </script>

                <script type="text/template" id="tpl-sudah_dikonfirmasi">
                    <table class="pemusnahan-obat-table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_pemusnahan', 'direction' => $sort_by === 'tanggal_pemusnahan' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Pengajuan
                                        @if($sort_by === 'tanggal_pemusnahan')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'user_name', 'direction' => $sort_by === 'user_name' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Pengaju
                                        @if($sort_by === 'user_name')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'approver_name', 'direction' => $sort_by === 'approver_name' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Disetujui Oleh
                                        @if($sort_by === 'approver_name')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'approved_at', 'direction' => $sort_by === 'approved_at' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Disetujui
                                        @if($sort_by === 'approved_at')
                                            @if($direction === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($approved ?? [] as $i => $req)
                                <tr>
                                    <td>
                                        @php
                                            try {
                                                $displayTanggalPemusnahan = $req->tanggal_pemusnahan ? $req->tanggal_pemusnahan->translatedFormat('d F Y, H:i:s') : '-';
                                            } catch (\Exception $e) {
                                                $displayTanggalPemusnahan = $req->tanggal_pemusnahan ? $req->tanggal_pemusnahan->format('Y-m-d H:i:s') : '-';
                                            }
                                        @endphp
                                        {{ $displayTanggalPemusnahan }}
                                    </td>
                                    <td>{{ $req->user?->name ?? '-' }}</td>
                                    <td>{{ $req->approver?->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            try {
                                                $displayApprovedAt = $req->approved_at ? $req->approved_at->translatedFormat('d F Y, H:i:s') : '-';
                                            } catch (\Exception $e) {
                                                $displayApprovedAt = $req->approved_at ? $req->approved_at->format('Y-m-d H:i:s') : '-';
                                            }
                                        @endphp
                                        {{ $displayApprovedAt }}
                                    </td>
                                    <td>{{ Str::limit($req->keterangan, 40) }}</td>
                                    <td>
                                        <button class="action-btn edit viewPemusnahan" data-req='@json($req)' title="Lihat">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322C3.2 7.036 7.522 4 12 4c4.478 0 8.8 3.036 9.964 8.322a1.125 1.125 0 0 1 0 .356C20.8 16.964 16.478 20 12 20c-4.478 0-8.8-3.036-9.964-8.322a1.125 1.125 0 0 1 0-.356z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty">Belum ada riwayat pemusnahan yang dikonfirmasi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </script>
            </div>
        </div>
    </div>


<!-- Create Pemusnahan Modal -->
<div id="createPemusnahanModal" class="modal hidden" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2>Ajukan Pemusnahan Obat</h2>
            <button type="button" id="closeCreatePemusnahanModal" class="modal-close" aria-label="Tutup">×</button>
        </div>
        <div class="modal-body">
            <form id="createPemusnahanForm" method="POST" action="{{ route('pemusnahan-obat.store') }}" class="form-component">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tanggal_pemusnahan">Tanggal Pemusnahan</label>
                        <input type="date" id="tanggal_pemusnahan" name="tanggal_pemusnahan" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="keterangan_pemusnahan">Keterangan</label>
                        <input type="text" id="keterangan_pemusnahan" name="keterangan">
                    </div>
                </div>

                <div style="margin-top: 12px; border-top:1px solid #e5e7eb; padding-top:12px;">
                    <h4 style="margin:0 0 8px 0; font-size:14px; font-weight:600;">Detail Barang</h4>
                    <div class="table-wrapper">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Kadaluwarsa</th>
                                    <th>Satuan</th>
                                    <th>Lokasi</th>
                                </tr>
                            </thead>
                            <tbody id="pemusnahanDetailItems"></tbody>
                        </table>
                    </div>
                    <button type="button" id="pemusnahanAddDetail" class="btn-add-detail">+ Tambah Item</button>
                </div>

                <div class="modal-actions" style="margin-top:18px;">
                    <button type="button" id="cancelCreatePemusnahan" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Pemusnahan Modal -->
<div id="viewPemusnahanModal" class="modal hidden" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2>Detail Pemusnahan</h2>
            <button type="button" id="closeViewPemusnahanModal" class="modal-close" aria-label="Tutup">×</button>
        </div>
        <div class="modal-body" id="viewPemusnahanBody">
            <!-- populated by JS -->
        </div>
    </div>
</div>

<!-- Process Pemusnahan Modal -->
<div id="processPemusnahanModal" class="modal hidden" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2>Proses Pemusnahan Obat</h2>
            <button type="button" id="closeProcessPemusnahanModal" class="modal-close" aria-label="Tutup">×</button>
        </div>
        <div class="modal-body">
            <form id="processPemusnahanForm" method="POST" enctype="multipart/form-data" class="form-component">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="tab" value="sudah_dimusnahkan">
                <div class="form-group">
                    <label for="process_tanggal_pemusnahan">Tanggal Pemusnahan</label>
                    <input id="process_tanggal_pemusnahan" name="tanggal_pemusnahan" type="date" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="process_bukti_foto">Bukti Foto Pemusnahan</label>
                    <input id="process_bukti_foto" name="bukti_foto" type="file" accept="image/*" required class="form-control">
                </div>
                <div class="modal-actions" style="margin-top: 12px;">
                    <button type="button" id="cancelProcessPemusnahan" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // data for selects
    // include data attributes so we can read lokasi and satuan directly from the option
    const namaOptions = `@foreach($namaObats as $no)<option value="{{ $no->id }}" data-lokasi="{{ addslashes($no->lokasi_penyimpanan ?? '') }}" data-satuan="{{ $no->satuan_obat_id ?? '' }}">{{ addslashes($no->nama_obat) }}</option>@endforeach`;
    const satuanOptions = `@foreach($satuanobats as $so)<option value="{{ $so->id }}">{{ addslashes($so->satuan_obat) }}</option>@endforeach`;

    // quick map for nama obat metadata (satuan/lokasi) used for prefill
    const namaMeta = @json($namaObats->mapWithKeys(function($i){ return [$i->id => ['satuan_id' => $i->satuan_obat_id ?? null, 'lokasi' => $i->lokasi_penyimpanan ?? '']]; }));

    // modal controls
    const openCreateBtn = document.getElementById('openCreatePemusnahan');
    const createModal = document.getElementById('createPemusnahanModal');
    const closeCreateBtn = document.getElementById('closeCreatePemusnahanModal');
    const cancelCreateBtn = document.getElementById('cancelCreatePemusnahan');
    const pemusnahanDetailItems = document.getElementById('pemusnahanDetailItems');
    const pemusnahanAddDetail = document.getElementById('pemusnahanAddDetail');
    const createPemusnahanForm = document.getElementById('createPemusnahanForm');

    const viewModal = document.getElementById('viewPemusnahanModal');
    const closeViewBtn = document.getElementById('closeViewPemusnahanModal');
    const viewBody = document.getElementById('viewPemusnahanBody');

    const processModal = document.getElementById('processPemusnahanModal');
    const closeProcessBtn = document.getElementById('closeProcessPemusnahanModal');
    const cancelProcessBtn = document.getElementById('cancelProcessPemusnahan');
    const processForm = document.getElementById('processPemusnahanForm');

    let pemusnahanIndex = 0;

    function createPemusnahanRow(index, data = {}) {
        const namaId = data.nama_obat_id || '';
        const jumlah = data.jumlah || '';
        const stokId = data.stok_obat_id || '';
        const satuanId = data.satuan_id || '';
        const lokasi = data.lokasi_penyimpanan || '';
        const readonly = !!data.readonly;

        return `
            <tr data-index="${index}">
                <td>
                    <select name="details[${index}][nama_obat_id]" class="table-input nama-obat-select-pemusnahan" data-index="${index}" ${readonly ? 'disabled' : 'required'}>
                        <option value="">Pilih Obat</option>
                        ${namaOptions}
                    </select>
                    ${readonly && namaId ? `<input type="hidden" name="details[${index}][nama_obat_id]" value="${namaId}">` : ''}
                </td>
                <td>
                    <input type="number" name="details[${index}][jumlah]" class="table-input" min="1" value="${jumlah}" ${readonly ? 'readonly' : 'required'}>
                </td>
                <td>
                    <select name="details[${index}][stok_obat_id]" class="table-input stok-select-pemusnahan" data-index="${index}" ${readonly ? 'disabled' : ''}>
                        <option value="">Pilih Tanggal</option>
                    </select>
                    ${readonly && stokId ? `<input type="hidden" name="details[${index}][stok_obat_id]" value="${stokId}">` : ''}
                </td>
                <td>
                    <select name="details[${index}][satuan_id]" class="table-input" ${readonly ? 'disabled' : ''}>
                        <option value="">Pilih Satuan</option>
                        ${satuanOptions}
                    </select>
                    ${readonly && satuanId ? `<input type="hidden" name="details[${index}][satuan_id]" value="${satuanId}">` : ''}
                </td>
                <td>
                    <input type="text" name="details[${index}][lokasi_penyimpanan]" class="table-input" value="${lokasi}" ${readonly ? 'readonly' : ''}>
                </td>
            </tr>
        `;
    }

    function openCreatePemusnahan() {
        if (!createModal) return;
        createModal.classList.remove('hidden');
        createModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        document.getElementById('tanggal_pemusnahan').value = new Date().toISOString().slice(0,10);
    }
    function closeCreatePemusnahan() {
        if (!createModal) return;
        createModal.classList.add('hidden');
        createModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = 'auto';
        // reset form
        if (pemusnahanDetailItems) pemusnahanDetailItems.innerHTML = '';
        // restore add-detail button visibility
        if (pemusnahanAddDetail) pemusnahanAddDetail.style.display = '';
        pemusnahanIndex = 0;
    }

    function openProcessPemusnahan() {
        if (!processModal) return;
        processModal.classList.remove('hidden');
        processModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeProcessPemusnahan() {
        if (!processModal) return;
        processModal.classList.add('hidden');
        processModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = 'auto';
        if (processForm) {
            processForm.reset();
            processForm.action = '';
        }
    }

    function onProcessPemusnahan(e) {
        const btn = e.currentTarget;
        const id = btn.dataset.id;
        if (!id || !processForm) return;

        processForm.action = `/pemusnahan-obat/${id}/dimusnahkan`;
        openProcessPemusnahan();
    }

    if (closeProcessBtn) closeProcessBtn.addEventListener('click', closeProcessPemusnahan);
    if (cancelProcessBtn) cancelProcessBtn.addEventListener('click', closeProcessPemusnahan);

    if (openCreateBtn) openCreateBtn.addEventListener('click', () => {
        // ensure add-detail is available for manual create
        if (pemusnahanAddDetail) pemusnahanAddDetail.style.display = '';
        // start with one empty row
        pemusnahanDetailItems.innerHTML = createPemusnahanRow(pemusnahanIndex++);
        attachPemusnahanListeners();
        openCreatePemusnahan();
    });
    if (closeCreateBtn) closeCreateBtn.addEventListener('click', closeCreatePemusnahan);
    if (cancelCreateBtn) cancelCreateBtn.addEventListener('click', closeCreatePemusnahan);

    if (pemusnahanAddDetail) pemusnahanAddDetail.addEventListener('click', () => {
        pemusnahanDetailItems.insertAdjacentHTML('beforeend', createPemusnahanRow(pemusnahanIndex++));
        attachPemusnahanListeners();
    });

    function attachPemusnahanListeners() {
        document.querySelectorAll('.btn-delete-row').forEach(btn => {
            btn.removeEventListener('click', onDeleteRow);
            btn.addEventListener('click', onDeleteRow);
        });
        document.querySelectorAll('.nama-obat-select-pemusnahan').forEach(sel => {
            sel.removeEventListener('change', onNamaPemusnahanChange);
            sel.addEventListener('change', onNamaPemusnahanChange);
        });
    }

    function onDeleteRow(e) {
        const tr = e.target.closest('tr');
        if (tr) tr.remove();
    }

    function onNamaPemusnahanChange(e) {
        const sel = e.target;
        const namaId = sel.value;
        const idx = sel.dataset.index;
        const stokSelect = document.querySelector(`.stok-select-pemusnahan[data-index="${idx}"]`);
        const row = document.querySelector(`tr[data-index="${idx}"]`);
        const satuanSelect = row ? row.querySelector('select[name$="[satuan_id]"]') : null;
        const lokasiInput = row ? row.querySelector('input[name$="[lokasi_penyimpanan]"]') : null;
        if (!stokSelect) return;
        stokSelect.innerHTML = '<option value="">Pilih Tanggal</option>';
        if (!namaId) return;
        // prefer namaMeta (server-side JSON) to autofill satuan and lokasi, fall back to option dataset
        if (namaMeta && namaMeta[namaId]) {
            if (satuanSelect && namaMeta[namaId].satuan_id) satuanSelect.value = namaMeta[namaId].satuan_id;
            if (lokasiInput) lokasiInput.value = namaMeta[namaId].lokasi || '';
        } else {
            const selectedOption = sel.options[sel.selectedIndex];
            if (selectedOption) {
                const optSatuan = selectedOption.dataset.satuan;
                const optLokasi = selectedOption.dataset.lokasi;
                if (satuanSelect && optSatuan) satuanSelect.value = optSatuan;
                if (lokasiInput) lokasiInput.value = optLokasi || '';
            }
        }

        // then populate stok select via API
        fetch(`/pengeluaran-obat/stok/${namaId}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.text = s.display || s.tanggal_kadaluwarsa;
                    stokSelect.appendChild(opt);
                });
            }).catch(err => console.error(err));
    }

    const userRole = `{{ auth()->user()?->role ?? '' }}`;
    const petugasRoles = ['petugas_obat','petugas_gudang','petugas_administrasi'];
    const defaultTab = `{{ $activeTab ?? 'belum_diajukan' }}`;

    function renderTab(tabKey) {
        const tabInput = document.getElementById('currentTabInput');
        if (tabInput) {
            tabInput.value = tabKey;
        }
        const tpl = document.getElementById('tpl-' + tabKey);
        const container = document.getElementById('mainTableContainer');
        if (!tpl || !container) return;
        container.innerHTML = tpl.innerHTML;
        attachTableListeners();
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === tabKey));
    }

    function formatDateTimeISO(iso) {
        if (!iso) return '-';
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const d = new Date(iso);
        if (isNaN(d)) return iso;
        const day = String(d.getDate()).padStart(2,'0');
        const month = months[d.getMonth()] || '';
        const year = d.getFullYear();
        const hh = String(d.getHours()).padStart(2,'0');
        const mm = String(d.getMinutes()).padStart(2,'0');
        const ss = String(d.getSeconds()).padStart(2,'0');
        return `${day} ${month} ${year}, ${hh}:${mm}:${ss}`;
    }

    function onViewPemusnahan(e) {
        const btn = e.currentTarget;
        const req = JSON.parse(btn.dataset.req);
        const pengaju = req.user?.name || '-';
        const tanggal = formatDateTimeISO(req.tanggal_pemusnahan);
        const keterangan = req.keterangan || '-';

        let html = `<div class="pemusnahan-meta">` +
                    `<div class="meta-item"><label>Pengaju</label><div class="value">${pengaju}</div></div>` +
                    `<div class="meta-item"><label>Tanggal</label><div class="value">${tanggal}</div></div>` +
                    `<div class="meta-item" style="flex:1 1 300px;"><label>Keterangan</label><div class="value">${keterangan}</div></div>` +
                    `</div>`;

        html += `<div class="table-wrapper"><table class="detail-table"><thead><tr><th>Nama Obat</th><th>Jumlah</th><th>Satuan</th><th>Lokasi</th><th>Tanggal Kadaluwarsa</th></tr></thead><tbody>`;
        (req.details || []).forEach(d => {
            const tgl = d.stok?.tanggal_kadaluwarsa ? formatDateTimeISO(d.stok.tanggal_kadaluwarsa) : (d.stok_obat_id ? 'terpilih' : '');
            html += `<tr><td>${d.nama_obat?.nama_obat || d.nama_obat_id}</td><td>${d.jumlah}</td><td>${d.satuan?.satuan_obat || d.satuan_id || ''}</td><td>${d.lokasi_penyimpanan || ''}</td><td>${tgl}</td></tr>`;
        });
        html += `</tbody></table></div>`;
        viewBody.innerHTML = html;
        viewModal.classList.remove('hidden');
        viewModal.setAttribute('aria-hidden','false');
        document.body.style.overflow = 'hidden';
    }

    function onApplyPemusnahan(e) {
        const el = e.currentTarget;
        const stokId = el.dataset.stokId;
        const namaId = el.dataset.namaId;
        const stokQty = el.dataset.stokQty || '';
        const tanggal = el.dataset.tanggal || '';
        // prefer lokasi from dataset, fallback to namaMeta if available
        const lokasi = el.dataset.lokasi || ((namaMeta && namaMeta[namaId] && namaMeta[namaId].lokasi) ? namaMeta[namaId].lokasi : '');

        // clear and insert a single READ-ONLY row (user should not change detail when using per-row Ajukan)
        pemusnahanDetailItems.innerHTML = '';
        const idx = pemusnahanIndex++;
            pemusnahanDetailItems.insertAdjacentHTML('beforeend', createPemusnahanRow(idx, {
            nama_obat_id: namaId,
            jumlah: stokQty,
            stok_obat_id: stokId,
            satuan_id: (namaMeta[namaId] || {}).satuan_id || '',
            lokasi_penyimpanan: lokasi || ((namaMeta[namaId] || {}).lokasi || ''),
            readonly: true
        }));

        attachPemusnahanListeners();

        // hide "Tambah detail" when modal was opened via per-row Ajukan
        if (pemusnahanAddDetail) pemusnahanAddDetail.style.display = 'none';

        const row = pemusnahanDetailItems.querySelector(`tr[data-index="${idx}"]`);
        if (row) {
            const namaSel = row.querySelector('.nama-obat-select-pemusnahan');
            const stokSel = row.querySelector('.stok-select-pemusnahan');
            const jumlahInput = row.querySelector('input[name$="[jumlah]"]');
            const satuanSel = row.querySelector('select[name$="[satuan_id]"]');

            if (namaSel) namaSel.value = namaId;
            if (jumlahInput) jumlahInput.value = stokQty;
            if (satuanSel && (namaMeta[namaId] || {}).satuan_id) satuanSel.value = namaMeta[namaId].satuan_id;

            if (stokSel) {
                stokSel.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = stokId;
                opt.text = tanggal ? new Date(tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : 'Terpilih';
                stokSel.appendChild(opt);
                stokSel.value = stokId;
            }
            // ensure lokasi input is filled from namaMeta if still empty
            const lokasiInput = row ? row.querySelector('input[name$="[lokasi_penyimpanan]"]') : null;
            if (lokasiInput && !lokasiInput.value && namaMeta && namaMeta[namaId] && namaMeta[namaId].lokasi) {
                lokasiInput.value = namaMeta[namaId].lokasi;
            }
        }

        openCreatePemusnahan();
    }

    function attachTableListeners() {
        document.querySelectorAll('.viewPemusnahan').forEach(btn => {
            btn.removeEventListener('click', onViewPemusnahan);
            btn.addEventListener('click', onViewPemusnahan);
        });
        document.querySelectorAll('.processPemusnahan').forEach(btn => {
            btn.removeEventListener('click', onProcessPemusnahan);
            btn.addEventListener('click', onProcessPemusnahan);
        });
        document.querySelectorAll('.btn-apply-pemusnahan').forEach(btn => {
            btn.removeEventListener('click', onApplyPemusnahan);
            btn.addEventListener('click', onApplyPemusnahan);
        });
        // initialize confirm-delete and confirm-approve components in dynamically-injected templates
        document.querySelectorAll('.confirm-delete-component, .confirm-approve-component').forEach(comp => {
            if (comp.dataset.caInit) return; // already initialized
            const modal = comp.querySelector('.confirm-action-modal');
            const trigger = comp.querySelector('.confirm-action-trigger');
            if (!modal || !trigger) return;

            // extract bodyDimClass from component data or detect from classes
            const bodyDimClass = comp.dataset.bodyDimClass || (comp.classList.contains('confirm-approve-component') ? 'confirm-approve-open' : null);
            const originalParent = modal.parentNode;
            const originalNextSibling = modal.nextSibling;

            function openModal() {
                // move modal to body to escape stacking context
                if (modal.parentNode !== document.body) {
                    document.body.appendChild(modal);
                }
                modal.style.display = 'flex';
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
                if (bodyDimClass) document.body.classList.add(bodyDimClass);
                const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]');
                if (focusable) focusable.focus();
            }

            function closeModal() {
                modal.classList.remove('open');
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    if (bodyDimClass) document.body.classList.remove(bodyDimClass);
                    // restore modal to original location
                    if (originalParent && originalParent !== document.body) {
                        try {
                            if (originalNextSibling && originalNextSibling.parentNode === originalParent) {
                                originalParent.insertBefore(modal, originalNextSibling);
                            } else {
                                originalParent.appendChild(modal);
                            }
                        } catch (e) {}
                    }
                    if (typeof trigger.focus === 'function') trigger.focus();
                }, 160);
            }

            trigger.addEventListener('click', function(e) { e.preventDefault(); openModal(); });
            const cancel = modal.querySelector('[data-cancel]');
            const overlay = modal.querySelector('[data-close]');
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
            const confirmBtn = modal.querySelector('.cd-confirm');
            if (confirmBtn) {
                confirmBtn.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#dc2626 !important';
                    this.style.boxShadow = '0 8px 16px rgba(220,38,38,0.12) !important';
                });
                confirmBtn.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '#ef4444 !important';
                    this.style.boxShadow = '0 6px 14px rgba(239,68,68,0.08) !important';
                });
            }
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.classList.contains('open')) closeModal(); });
            comp.dataset.caInit = '1';
        });
    }

    document.querySelectorAll('.tab-btn').forEach(btn => btn.addEventListener('click', function() {
        renderTab(this.dataset.tab);
    }));

    // render default on load
    renderTab(defaultTab);

    // close view modal
    if (closeViewBtn) closeViewBtn.addEventListener('click', () => {
        viewModal.classList.add('hidden');
        viewModal.setAttribute('aria-hidden','true');
        document.body.style.overflow = 'auto';
    });



    // Form validation: ensure at least one detail row
    if (createPemusnahanForm) {
        createPemusnahanForm.addEventListener('submit', function(e) {
            const rows = Array.from(this.querySelectorAll('tbody tr'));
            if (rows.length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 item untuk diajukan.');
                return;
            }
            // ensure fields filled
            for (const r of rows) {
                const nama = r.querySelector('[name$="[nama_obat_id]"]');
                const jumlah = r.querySelector('[name$="[jumlah]"]');
                if (!nama || !nama.value || !jumlah || !jumlah.value) {
                    e.preventDefault();
                    alert('Lengkapi semua detail item sebelum mengajukan.');
                    return;
                }
            }
        });
    }
</script>

</body>
</html>
