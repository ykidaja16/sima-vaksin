<?php

namespace App\Http\Controllers;

use App\Models\ProtokolAbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProtokolAbiController extends Controller
{
    public function index(Request $request)
    {
        $query = ProtokolAbi::where('user_id', Auth::id())
            ->orderByDesc('tanggal_pemeriksaan')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('no_protokol', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_pemeriksaan', $request->input('tanggal'));
        }

        $protokol = $query->paginate(20)->withQueryString();

        return view('protokol-abi.index', compact('protokol'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('protokol-abi.create', ['namaDokter' => $user->name]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pasien'               => 'required|string|max:100',
            'umur'                      => 'required|integer|min:0|max:150',
            'alamat'                    => 'required|string|max:255',
            'tanggal_pemeriksaan'       => 'required|date',
            'right_arm_sistolik'        => 'required|integer|min:1|max:300',
            'right_arm_diastolik'       => 'required|integer|min:1|max:300',
            'left_arm_sistolik'         => 'required|integer|min:1|max:300',
            'left_arm_diastolik'        => 'required|integer|min:1|max:300',
            'right_ankle_sistolik'      => 'required|integer|min:1|max:300',
            'right_ankle_diastolik'     => 'required|integer|min:1|max:300',
            'left_ankle_sistolik'       => 'required|integer|min:1|max:300',
            'left_ankle_diastolik'      => 'required|integer|min:1|max:300',
            'highest_brachial_sistolik' => 'required|integer|min:1|max:300',
            'abi_left_pembilang'        => 'required|integer|min:1|max:300',
            'abi_left_penyebut'         => 'required|integer|min:1|max:300',
            'abi_right_pembilang'       => 'required|integer|min:1|max:300',
            'abi_right_penyebut'        => 'required|integer|min:1|max:300',
        ], [
            'nama_pasien.required'         => 'Nama pasien wajib diisi',
            'umur.required'                => 'Umur wajib diisi',
            'alamat.required'              => 'Alamat wajib diisi',
            'tanggal_pemeriksaan.required' => 'Tanggal pemeriksaan wajib diisi',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $protokol = DB::transaction(function () use ($request, $user) {
                $noProtokol = ProtokolAbi::generateNoProtokol();

                return ProtokolAbi::create([
                    'no_protokol'               => $noProtokol,
                    'user_id'                   => $user->id,
                    'nama_dokter'               => $user->name,
                    'nama_pasien'               => $request->input('nama_pasien'),
                    'umur'                      => $request->input('umur'),
                    'alamat'                    => $request->input('alamat'),
                    'tanggal_pemeriksaan'       => $request->input('tanggal_pemeriksaan'),
                    'right_arm_sistolik'        => $request->input('right_arm_sistolik'),
                    'right_arm_diastolik'       => $request->input('right_arm_diastolik'),
                    'right_arm_mean'            => round(($request->input('right_arm_sistolik') + 2 * $request->input('right_arm_diastolik')) / 3),
                    'left_arm_sistolik'         => $request->input('left_arm_sistolik'),
                    'left_arm_diastolik'        => $request->input('left_arm_diastolik'),
                    'left_arm_mean'             => round(($request->input('left_arm_sistolik') + 2 * $request->input('left_arm_diastolik')) / 3),
                    'right_ankle_sistolik'      => $request->input('right_ankle_sistolik'),
                    'right_ankle_diastolik'     => $request->input('right_ankle_diastolik'),
                    'right_ankle_mean'          => round(($request->input('right_ankle_sistolik') + 2 * $request->input('right_ankle_diastolik')) / 3),
                    'left_ankle_sistolik'       => $request->input('left_ankle_sistolik'),
                    'left_ankle_diastolik'      => $request->input('left_ankle_diastolik'),
                    'left_ankle_mean'           => round(($request->input('left_ankle_sistolik') + 2 * $request->input('left_ankle_diastolik')) / 3),
                    'highest_brachial_sistolik' => $request->input('highest_brachial_sistolik'),
                    'abi_left_pembilang'        => $request->input('abi_left_pembilang'),
                    'abi_left_penyebut'         => $request->input('abi_left_penyebut'),
                    'abi_left_hasil'            => round($request->input('abi_left_pembilang') / $request->input('abi_left_penyebut'), 2),
                    'abi_right_pembilang'       => $request->input('abi_right_pembilang'),
                    'abi_right_penyebut'        => $request->input('abi_right_penyebut'),
                    'abi_right_hasil'           => round($request->input('abi_right_pembilang') / $request->input('abi_right_penyebut'), 2),
                ]);
            });

            Log::info('Protokol ABI created', [
                'protokol_id' => $protokol->id,
                'no_protokol' => $protokol->no_protokol,
                'dokter'      => $user->name,
            ]);

            return redirect()->route('protokol-abi.show', $protokol->id)
                ->with('success', "Protokol ABI {$protokol->no_protokol} berhasil disimpan")
                ->with('auto_print', true);

        } catch (\Exception $e) {
            Log::error('Failed to create protokol ABI: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Gagal menyimpan protokol ABI: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $protokol = ProtokolAbi::where('user_id', Auth::id())->findOrFail($id);

        return view('protokol-abi.show', compact('protokol'));
    }

    public function pdf($id)
    {
        $protokol = ProtokolAbi::where('user_id', Auth::id())->findOrFail($id);

        return view('protokol-abi.print', compact('protokol'));
    }

    public function destroy($id)
    {
        $protokol = ProtokolAbi::where('user_id', Auth::id())->findOrFail($id);

        try {
            $noProtokol = $protokol->no_protokol;
            $protokol->delete();

            Log::info('Protokol ABI deleted', [
                'no_protokol' => $noProtokol,
                'deleted_by'  => Auth::user()->username ?? Auth::id(),
            ]);

            return redirect()->route('protokol-abi.index')
                ->with('success', "Protokol ABI {$noProtokol} berhasil dihapus");
        } catch (\Exception $e) {
            Log::error('Failed to delete protokol ABI: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus protokol ABI');
        }
    }
}
