<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\VaccineSchedule;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = [];

        if ($user->isAdmin()) {
            $data['totalPasien']    = \App\Models\Patient::count();
            $data['reminderHariIni'] = VaccineSchedule::pending()
                ->whereDate('tanggal_vaksin', today())
                ->count();
            $data['reminderH7']     = VaccineSchedule::pending()
                ->whereBetween('tanggal_vaksin', [today(), today()->addDays(7)])
                ->count();
            $data['jadwalBulanIni'] = VaccineSchedule::whereMonth('tanggal_vaksin', now()->month)
                ->whereYear('tanggal_vaksin', now()->year)
                ->count();
        }

        if ($user->isDokter()) {
            $data['totalResep']      = Resep::where('user_id', $user->id)->count();
            $data['resepHariIni']    = Resep::where('user_id', $user->id)
                ->whereDate('tanggal_resep', today())
                ->count();
            $data['resepBulanIni']   = Resep::where('user_id', $user->id)
                ->whereMonth('tanggal_resep', now()->month)
                ->whereYear('tanggal_resep', now()->year)
                ->count();
            $data['resepTerbaru']    = Resep::with('obat')
                ->where('user_id', $user->id)
                ->orderByDesc('tanggal_resep')
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        if ($user->isIT()) {
            $data['totalUser']      = \App\Models\User::count();
            $data['totalCabang']    = \App\Models\Branch::count();
            $data['totalVaksin']    = \App\Models\VaccineType::count();
            $data['userAktif']      = \App\Models\User::where('is_active', true)->count();
        }

        return view('dashboard', compact('user', 'data'));
    }
}
