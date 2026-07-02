<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\ResepObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResepController extends Controller
{
    public function index(Request $request)
    {
        $query = Resep::with('obat')
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal_resep')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('no_resep', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_resep', $request->input('tanggal'));
        }

        $resep = $query->paginate(20)->withQueryString();

        return view('resep.index', compact('resep'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('resep.create', ['namaDokter' => $user->name]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pasien'         => 'required|string|max:100',
            'umur'                => 'required|integer|min:0|max:150',
            'alamat'              => 'required|string|max:255',
            'tanggal_resep'       => 'required|date',
            'obat'                => 'required|array|min:1',
            'obat.*.nama_obat'       => 'required|string|max:100',
            'obat.*.kekuatan'        => 'nullable|string|max:10',
            'obat.*.satuan_kekuatan' => 'required|in:mg,ml,%,-',
            'obat.*.dosis_kali'      => 'required|integer|min:1|max:99',
            'obat.*.dosis_jumlah' => 'required|integer|min:1|max:99',
            'obat.*.waktu_minum'  => 'required|in:Pagi,Siang,Sore,Malam,Sesuai Dosis',
            'obat.*.makan'        => 'required|in:Sebelum Makan,Sesudah Makan,-',
            'obat.*.jumlah'       => 'required|integer|min:0|max:9999',
            'obat.*.satuan'       => 'required|in:tablet,kaplet,kapsul,strip,tube,botol,-',
            'obat.*.keterangan'   => 'nullable|string',
        ], [
            'nama_pasien.required'          => 'Nama pasien wajib diisi',
            'umur.required'                 => 'Umur wajib diisi',
            'umur.min'                      => 'Umur tidak valid',
            'umur.max'                      => 'Umur tidak valid',
            'alamat.required'               => 'Alamat wajib diisi',
            'tanggal_resep.required'        => 'Tanggal resep wajib diisi',
            'obat.required'                 => 'Minimal satu obat harus ditambahkan',
            'obat.min'                      => 'Minimal satu obat harus ditambahkan',
            'obat.*.nama_obat.required'     => 'Nama obat wajib diisi',
            'obat.*.dosis_kali.required'    => 'Dosis (berapa kali) wajib diisi',
            'obat.*.dosis_jumlah.required'  => 'Dosis (berapa tablet) wajib diisi',
            'obat.*.waktu_minum.required'   => 'Waktu minum wajib dipilih',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $resep = DB::transaction(function () use ($request, $user) {
                $noResep = Resep::generateNoResep();

                $resep = Resep::create([
                    'no_resep'      => $noResep,
                    'user_id'       => $user->id,
                    'nama_dokter'   => $user->name,
                    'nama_pasien'   => $request->input('nama_pasien'),
                    'umur'          => $request->input('umur'),
                    'alamat'        => $request->input('alamat'),
                    'tanggal_resep' => $request->input('tanggal_resep'),
                ]);

                $obatData = collect($request->input('obat'))->map(fn($o) => [
                    'resep_id'    => $resep->id,
                    'nama_obat'        => $o['nama_obat'],
                    'kekuatan'         => $o['kekuatan'] ?? null,
                    'satuan_kekuatan'  => $o['satuan_kekuatan'],
                    'dosis'            => $o['dosis_kali'] . 'x' . $o['dosis_jumlah'],
                    'waktu_minum' => $o['waktu_minum'],
                    'makan'       => $o['makan'],
                    'jumlah'      => $o['jumlah'],
                    'satuan'      => $o['satuan'],
                    'keterangan'  => $o['keterangan'] ?? null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ])->toArray();

                ResepObat::insert($obatData);

                return $resep;
            });

            Log::info('Resep created', [
                'resep_id' => $resep->id,
                'no_resep' => $resep->no_resep,
                'dokter'   => $user->name,
            ]);

            return redirect()->route('resep.show', $resep->id)
                ->with('success', "Resep {$resep->no_resep} berhasil disimpan")
                ->with('auto_print', true);

        } catch (\Exception $e) {
            Log::error('Failed to create resep: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Gagal menyimpan resep: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $resep = Resep::with('obat')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('resep.show', compact('resep'));
    }

    public function pdf($id)
    {
        $resep = Resep::with('obat')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('resep.print', compact('resep'));
    }

    public function destroy($id)
    {
        $resep = Resep::where('user_id', Auth::id())->findOrFail($id);

        try {
            $noResep = $resep->no_resep;
            $resep->delete();

            Log::info('Resep deleted', [
                'no_resep' => $noResep,
                'deleted_by' => Auth::user()->username ?? Auth::id(),
            ]);

            return redirect()->route('resep.index')
                ->with('success', "Resep {$noResep} berhasil dihapus");
        } catch (\Exception $e) {
            Log::error('Failed to delete resep: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus resep');
        }
    }
}
