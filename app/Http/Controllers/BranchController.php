<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('nama_cabang')->paginate(30);
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_cabang' => 'required|string|max:100|unique:branches',
            'kode_prefix' => 'required|string|max:10|unique:branches|regex:/^[A-Z]{2}$/',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
        ], [
            'kode_prefix.regex' => 'Kode prefix harus 2 huruf kapital (contoh: LX, LZ)',
        ]);

        try {
            Branch::create([
                'nama_cabang' => $validated['nama_cabang'],
                'kode_prefix' => strtoupper($validated['kode_prefix']),
                'alamat' => $validated['alamat'],
                'no_telp' => $validated['no_telp'],
                'is_active' => true,
            ]);

            Log::info("Branch {$validated['nama_cabang']} created by IT");

            return redirect()->route('branches.index')
                ->with('success', 'Cabang berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error("Failed to create branch: " . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan cabang: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'nama_cabang' => 'required|string|max:100|unique:branches,nama_cabang,' . $id,
            'kode_prefix' => 'required|string|max:10|unique:branches,kode_prefix,' . $id . '|regex:/^[A-Z]{2}$/',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ], [
            'kode_prefix.regex' => 'Kode prefix harus 2 huruf kapital (contoh: LX, LZ)',
        ]);

        try {
            // Check if prefix is being changed and branch has patients
            if ($branch->kode_prefix !== strtoupper($validated['kode_prefix']) && $branch->patients()->count() > 0) {
                return back()->with('error', 'Kode prefix tidak bisa diubah karena cabang sudah memiliki data pasien');
            }

            $branch->update([
                'nama_cabang' => $validated['nama_cabang'],
                'kode_prefix' => strtoupper($validated['kode_prefix']),
                'alamat' => $validated['alamat'],
                'no_telp' => $validated['no_telp'],
                'is_active' => $request->boolean('is_active', false),
            ]);

            Log::info("Branch {$branch->nama_cabang} updated by IT");

            return redirect()->route('branches.index')
                ->with('success', 'Cabang berhasil diupdate');
        } catch (\Exception $e) {
            Log::error("Failed to update branch: " . $e->getMessage());
            return back()->with('error', 'Gagal mengupdate cabang: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            
            // Check if branch has patients
            if ($branch->patients()->count() > 0) {
                return back()->with('error', 'Cabang tidak bisa dihapus karena sudah memiliki data pasien');
            }

            $nama = $branch->nama_cabang;
            $branch->delete();

            Log::info("Branch {$nama} deleted by IT");

            return redirect()->route('branches.index')
                ->with('success', 'Cabang berhasil dihapus');
        } catch (\Exception $e) {
            Log::error("Failed to delete branch: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus cabang: ' . $e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            $branch->update(['is_active' => !$branch->is_active]);

            $status = $branch->is_active ? 'diaktifkan' : 'dinonaktifkan';
            Log::info("Branch {$branch->nama_cabang} {$status} by IT");

            return redirect()->route('branches.index')
                ->with('success', "Cabang berhasil {$status}");
        } catch (\Exception $e) {
            Log::error("Failed to toggle branch active status: " . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status cabang: ' . $e->getMessage());
        }
    }
}
