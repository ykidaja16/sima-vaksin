<?php

namespace App\Http\Controllers;

use App\Models\VaccineType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VaccineTypeController extends Controller
{
    public function index()
    {
        $vaccineTypes = VaccineType::orderBy('nama_vaksin')->paginate(30);
        return view('vaccine-types.index', compact('vaccineTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vaksin' => 'required|string|max:100|unique:vaccine_types',
            'deskripsi' => 'nullable|string',
            'total_dosis' => 'required|integer|min:1|max:10',
            'interval_bulan' => 'required|string',
        ]);

        try {
            // Parse interval_bulan from string (e.g., "0, 2, 6" or "0, 12")
            $intervals = array_map('intval', array_map('trim', explode(',', $validated['interval_bulan'])));
            
            // Validate that intervals count matches total_dosis
            if (count($intervals) !== (int)$validated['total_dosis']) {
                return back()->with('error', 'Jumlah interval bulan harus sama dengan total dosis');
            }

            VaccineType::create([
                'nama_vaksin' => $validated['nama_vaksin'],
                'deskripsi' => $validated['deskripsi'],
                'total_dosis' => $validated['total_dosis'],
                'interval_bulan' => $intervals,
                'is_active' => true,
            ]);

            Log::info("Vaccine type {$validated['nama_vaksin']} created by IT");

            return redirect()->route('vaccine-types.index')
                ->with('success', 'Jenis vaksin berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error("Failed to create vaccine type: " . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan jenis vaksin: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $vaccineType = VaccineType::findOrFail($id);

        $validated = $request->validate([
            'nama_vaksin' => 'required|string|max:100|unique:vaccine_types,nama_vaksin,' . $id,
            'deskripsi' => 'nullable|string',
            'total_dosis' => 'required|integer|min:1|max:10',
            'interval_bulan' => 'required|string',
            'is_active' => 'boolean',
        ]);

        try {
            // Parse interval_bulan from string
            $intervals = array_map('intval', array_map('trim', explode(',', $validated['interval_bulan'])));
            
            // Validate that intervals count matches total_dosis
            if (count($intervals) !== (int)$validated['total_dosis']) {
                return back()->with('error', 'Jumlah interval bulan harus sama dengan total dosis');
            }

            $vaccineType->update([
                'nama_vaksin' => $validated['nama_vaksin'],
                'deskripsi' => $validated['deskripsi'],
                'total_dosis' => $validated['total_dosis'],
                'interval_bulan' => $intervals,
                'is_active' => $request->boolean('is_active', false),
            ]);

            Log::info("Vaccine type {$vaccineType->nama_vaksin} updated by IT");

            return redirect()->route('vaccine-types.index')
                ->with('success', 'Jenis vaksin berhasil diupdate');
        } catch (\Exception $e) {
            Log::error("Failed to update vaccine type: " . $e->getMessage());
            return back()->with('error', 'Gagal mengupdate jenis vaksin: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $vaccineType = VaccineType::findOrFail($id);
            
            // Check if vaccine type is being used
            if ($vaccineType->vaccines()->count() > 0) {
                return back()->with('error', 'Jenis vaksin tidak bisa dihapus karena sudah digunakan');
            }

            $nama = $vaccineType->nama_vaksin;
            $vaccineType->delete();

            Log::info("Vaccine type {$nama} deleted by IT");

            return redirect()->route('vaccine-types.index')
                ->with('success', 'Jenis vaksin berhasil dihapus');
        } catch (\Exception $e) {
            Log::error("Failed to delete vaccine type: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus jenis vaksin: ' . $e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        try {
            $vaccineType = VaccineType::findOrFail($id);
            $vaccineType->update(['is_active' => !$vaccineType->is_active]);

            $status = $vaccineType->is_active ? 'diaktifkan' : 'dinonaktifkan';
            Log::info("Vaccine type {$vaccineType->nama_vaksin} {$status} by IT");

            return redirect()->route('vaccine-types.index')
                ->with('success', "Jenis vaksin berhasil {$status}");
        } catch (\Exception $e) {
            Log::error("Failed to toggle vaccine type active status: " . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status jenis vaksin: ' . $e->getMessage());
        }
    }
}
