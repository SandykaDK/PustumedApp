<?php

namespace App\Http\Controllers;

use App\Models\DetailPemusnahanObat;
use App\Models\NamaObat;
use App\Models\PemusnahanObat;
use App\Models\SatuanObat;
use App\Models\StokObat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PemusnahanObatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $today = Carbon::today();
        $limit = Carbon::today()->addDays(30);

        // Exclude stok rows that are already referenced by a PENDING pemusnahan request
        $pendingStokIds = DetailPemusnahanObat::whereHas('pemusnahan', function ($q) {
                $q->where('status', 'pending');
            })
            ->pluck('stok_obat_id')
            ->filter()
            ->unique()
            ->toArray();

        $query = StokObat::with('namaObat')
            ->whereBetween('tanggal_kadaluwarsa', [$today->toDateString(), $limit->toDateString()])
            // only consider batches that still have stock > 0
            ->where('stok', '>', 0)
            ->when(count($pendingStokIds) > 0, function ($q) use ($pendingStokIds) {
                $q->whereNotIn('id', $pendingStokIds);
            });

        // Search by nama obat
        $search = $request->get('search', '');
        if (!empty($search)) {
            $query->whereHas('namaObat', function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort_by = $request->get('sort_by', 'tanggal_kadaluwarsa');
        $direction = $request->get('direction', 'asc');

        $allowed_sorts = ['nama_obat', 'tanggal_kadaluwarsa', 'sisa_hari', 'jumlah_obat'];
        if (!in_array($sort_by, $allowed_sorts)) {
            $sort_by = 'tanggal_kadaluwarsa';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        // Apply sorting
        if ($sort_by === 'nama_obat') {
            $query->join('nama_obat', 'stok_obat.nama_obat_id', '=', 'nama_obat.id')
                ->orderBy('nama_obat.nama_obat', $direction)
                ->select('stok_obat.*');
        } elseif ($sort_by === 'tanggal_kadaluwarsa') {
            $query->orderBy('tanggal_kadaluwarsa', $direction);
        } elseif ($sort_by === 'sisa_hari') {
            // Calculate sisa_hari in query
            $query->orderByRaw("DATEDIFF(tanggal_kadaluwarsa, CURDATE()) {$direction}");
        } elseif ($sort_by === 'jumlah_obat') {
            $query->orderBy('stok', $direction);
        }

        $stokNearExpire = $query->orderBy('tanggal_kadaluwarsa', 'asc')->get();

        // Pemusnahan requests: split into pending and approved
        $pendingQuery = PemusnahanObat::with(['user','details.namaObat','details.satuan','details.stok'])
            ->where('pemusnahan_obat.status', 'pending');

        $approvedQuery = PemusnahanObat::with(['user','approver','details.namaObat','details.satuan','details.stok'])
            ->where('pemusnahan_obat.status', 'approved');

        $dimusnahkanQuery = PemusnahanObat::with(['user','approver','details.namaObat','details.satuan','details.stok'])
            ->where('pemusnahan_obat.status', 'dimusnahkan');

        if (!empty($search)) {
            $pendingQuery->whereHas('details.namaObat', function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%{$search}%");
            });

            $approvedQuery->whereHas('details.namaObat', function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%{$search}%");
            });

            $dimusnahkanQuery->whereHas('details.namaObat', function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%{$search}%");
            });
        }

        // Apply sorting for pemusnahan queries
        $pemusnahan_allowed_sorts = ['nama_obat', 'tanggal_kadaluwarsa', 'jumlah', 'tanggal_pemusnahan', 'approved_at', 'user_name', 'approver_name'];
        if (in_array($sort_by, $pemusnahan_allowed_sorts)) {
            if ($sort_by === 'nama_obat') {
                $pendingQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->join('nama_obat', 'detail_pemusnahan_obat.nama_obat_id', '=', 'nama_obat.id')
                    ->orderBy('nama_obat.nama_obat', $direction)
                    ->select('pemusnahan_obat.*');

                $approvedQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->join('nama_obat', 'detail_pemusnahan_obat.nama_obat_id', '=', 'nama_obat.id')
                    ->orderBy('nama_obat.nama_obat', $direction)
                    ->select('pemusnahan_obat.*');

                $dimusnahkanQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->join('nama_obat', 'detail_pemusnahan_obat.nama_obat_id', '=', 'nama_obat.id')
                    ->orderBy('nama_obat.nama_obat', $direction)
                    ->select('pemusnahan_obat.*');
            } elseif ($sort_by === 'tanggal_kadaluwarsa') {
                $pendingQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->join('stok_obat', 'detail_pemusnahan_obat.stok_obat_id', '=', 'stok_obat.id')
                    ->orderBy('stok_obat.tanggal_kadaluwarsa', $direction)
                    ->select('pemusnahan_obat.*');

                $approvedQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->join('stok_obat', 'detail_pemusnahan_obat.stok_obat_id', '=', 'stok_obat.id')
                    ->orderBy('stok_obat.tanggal_kadaluwarsa', $direction)
                    ->select('pemusnahan_obat.*');

                $dimusnahkanQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->join('stok_obat', 'detail_pemusnahan_obat.stok_obat_id', '=', 'stok_obat.id')
                    ->orderBy('stok_obat.tanggal_kadaluwarsa', $direction)
                    ->select('pemusnahan_obat.*');
            } elseif ($sort_by === 'jumlah') {
                $pendingQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->orderBy('detail_pemusnahan_obat.jumlah', $direction)
                    ->select('pemusnahan_obat.*');

                $approvedQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->orderBy('detail_pemusnahan_obat.jumlah', $direction)
                    ->select('pemusnahan_obat.*');

                $dimusnahkanQuery->join('detail_pemusnahan_obat', 'pemusnahan_obat.id', '=', 'detail_pemusnahan_obat.pemusnahan_obat_id')
                    ->orderBy('detail_pemusnahan_obat.jumlah', $direction)
                    ->select('pemusnahan_obat.*');
            } elseif ($sort_by === 'tanggal_pemusnahan') {
                $pendingQuery->orderBy('tanggal_pemusnahan', $direction);
                $approvedQuery->orderBy('tanggal_pemusnahan', $direction);
                $dimusnahkanQuery->orderBy('tanggal_pemusnahan', $direction);
            } elseif ($sort_by === 'approved_at') {
                $pendingQuery->orderBy('approved_at', $direction);
                $approvedQuery->orderBy('approved_at', $direction);
                $dimusnahkanQuery->orderBy('approved_at', $direction);
            } elseif ($sort_by === 'user_name') {
                $pendingQuery->join('users', 'pemusnahan_obat.user_id', '=', 'users.id')
                    ->orderBy('users.name', $direction)
                    ->select('pemusnahan_obat.*');

                $approvedQuery->join('users', 'pemusnahan_obat.user_id', '=', 'users.id')
                    ->orderBy('users.name', $direction)
                    ->select('pemusnahan_obat.*');

                $dimusnahkanQuery->join('users', 'pemusnahan_obat.user_id', '=', 'users.id')
                    ->orderBy('users.name', $direction)
                    ->select('pemusnahan_obat.*');
            } elseif ($sort_by === 'approver_name') {
                $pendingQuery->leftJoin('users as approvers', 'pemusnahan_obat.approver_id', '=', 'approvers.id')
                    ->orderBy('approvers.name', $direction)
                    ->select('pemusnahan_obat.*');

                $approvedQuery->leftJoin('users as approvers', 'pemusnahan_obat.approver_id', '=', 'approvers.id')
                    ->orderBy('approvers.name', $direction)
                    ->select('pemusnahan_obat.*');

                $dimusnahkanQuery->leftJoin('users as approvers', 'pemusnahan_obat.approver_id', '=', 'approvers.id')
                    ->orderBy('approvers.name', $direction)
                    ->select('pemusnahan_obat.*');
            }
        }

        $pending = $pendingQuery->orderBy('created_at', 'desc')->get();

        $approved = $approvedQuery
            ->orderBy('approved_at', 'desc')
            ->orderBy('updated_at', 'desc') // fallback order for records without approved_at
            ->get();

        $dimusnahkan = $dimusnahkanQuery
            ->orderBy('updated_at', 'desc')
            ->get();

        $namaObats = NamaObat::all();
        $satuanobats = SatuanObat::all();

        $petugasRoles = ['petugas_obat','petugas_gudang','petugas_administrasi'];
        $defaultTab = in_array($request->user()?->role, $petugasRoles) ? 'belum_diajukan' : ($request->user()?->role === 'kepala_pustu' ? 'belum_dikonfirmasi' : 'belum_diajukan');

        $allowedTabs = ['belum_diajukan', 'sudah_diajukan', 'sudah_disetujui', 'sudah_dimusnahkan', 'belum_dikonfirmasi', 'sudah_dikonfirmasi'];
        $activeTab = $request->get('tab', $defaultTab);
        if (!in_array($activeTab, $allowedTabs)) {
            $activeTab = $defaultTab;
        }

        return view('pemusnahan_obat.pemusnahan_obat', compact('stokNearExpire', 'pending', 'approved', 'dimusnahkan', 'namaObats', 'satuanobats', 'search', 'sort_by', 'direction', 'activeTab'));
    }

    public function store(Request $request)
    {
        $this->middleware('auth');

        $validated = $request->validate([
            'tanggal_pemusnahan' => 'required|date',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.nama_obat_id' => 'required|exists:nama_obat,id',
            'details.*.stok_obat_id' => 'nullable|exists:stok_obat,id',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.satuan_id' => 'nullable|exists:satuan_obat,id',
            'details.*.lokasi_penyimpanan' => 'nullable|string',
        ]);

        $p = PemusnahanObat::create([
            'user_id' => auth()->id(),
            'tanggal_pemusnahan' => $validated['tanggal_pemusnahan'],
            'status' => 'pending',
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        foreach ($validated['details'] as $d) {
            DetailPemusnahanObat::create([
                'pemusnahan_obat_id' => $p->id,
                'nama_obat_id' => $d['nama_obat_id'],
                'stok_obat_id' => $d['stok_obat_id'] ?? null,
                'jumlah' => $d['jumlah'],
                'satuan_id' => $d['satuan_id'] ?? null,
                'lokasi_penyimpanan' => $d['lokasi_penyimpanan'] ?? null,
            ]);
        }

        return redirect()->route('pemusnahan-obat.index')->with('success', 'Request pemusnahan berhasil diajukan.');
    }

    /**
     * Approve a pemusnahan request (only kepala_pustu)
     */
    public function approve($id)
    {
        $this->middleware('auth');

        $user = auth()->user();
        if (!$user || $user->role !== 'kepala_pustu') {
            return back()->with('error', 'Anda tidak berwenang untuk melakukan persetujuan.');
        }

        $p = PemusnahanObat::with('details')->findOrFail($id);
        if ($p->status !== 'pending') {
            return back()->with('error', 'Request telah diproses sebelumnya.');
        }

        // perform stock decrement and mark as approved
        DB::beginTransaction();
        try {
            foreach ($p->details as $detail) {
                if ($detail->stok_obat_id) {
                    $stok = StokObat::find($detail->stok_obat_id);
                    if ($stok) {
                        // pemusnahan berarti batch stok tersebut ditandai habis — set stok = 0
                        $stok->stok = 0;
                        $stok->save();
                    }
                }
            }

            $p->status = 'approved';
            $p->approved_by = $user->id;
            $p->approved_at = now();
            $p->save();

            DB::commit();
            return redirect()->route('pemusnahan-obat.index')->with('success', 'Pemusnahan obat telah disetujui dan diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        $this->middleware('auth');

        $p = PemusnahanObat::findOrFail($id);

        if ($p->status !== 'pending') {
            return back()->with('error', 'Request tidak dapat dibatalkan karena sudah diproses.');
        }

        if (auth()->id() !== $p->user_id) {
            return back()->with('error', 'Anda tidak berwenang membatalkan request ini.');
        }

        try {
            $p->status = 'cancelled';
            $p->save();
            return redirect()->route('pemusnahan-obat.index')->with('success', 'Request pemusnahan dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan request: ' . $e->getMessage());
        }
    }

    public function dimusnahkan(Request $request, $id)
    {
        $this->middleware('auth');

        $request->validate([
            'tanggal_pemusnahan' => 'required|date',
            'bukti_foto' => 'required|image|max:5120',
        ]);

        $p = PemusnahanObat::findOrFail($id);

        if ($p->status !== 'approved') {
            return back()->with('error', 'Pemusnahan hanya dapat diproses dari status approved.');
        }

        if ($request->hasFile('bukti_foto')) {
            $path = $request->file('bukti_foto')->store('pemusnahan_foto', 'public');
            $p->bukti_foto = $path;
        }

        $p->tanggal_pemusnahan = $request->input('tanggal_pemusnahan');
        $p->status = 'dimusnahkan';
        $p->save();

        return redirect()->route('pemusnahan-obat.index', ['tab' => 'sudah_dimusnahkan'])->with('success', 'Pemusnahan obat berhasil diproses dan dipindahkan ke tab sudah dimusnahkan.');
    }

    public function downloadFoto($id)
    {
        $p = PemusnahanObat::findOrFail($id);

        if (!$p->bukti_foto) {
            abort(404, 'Foto bukti pemusnahan tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($p->bukti_foto)) {
            abort(404, 'File bukti foto tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($p->bukti_foto);
    }
}
