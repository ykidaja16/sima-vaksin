<?php

namespace App\Http\Controllers;

use App\Models\VaccineSchedule;
use App\Services\VaccineScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReminderController extends Controller
{
    protected VaccineScheduleService $scheduleService;

    public function __construct(VaccineScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        $today = now()->startOfDay();
        $h7Date = now()->addDays(7)->endOfDay();

        // Base query with eager loading
        $query = VaccineSchedule::with([
                'patient:id,branch_id,pid,nama_pasien,no_hp,alamat,dob',
                'patient.branch:id,kode_prefix',
                'vaccine:id,vaccine_type_id',
                'vaccine.vaccineType:id,nama_vaksin'
            ])
            ->whereBetween('tanggal_vaksin', [$today, $h7Date])
            ->orderBy('tanggal_vaksin', 'asc')
            ->select('id', 'patient_id', 'vaccine_id', 'dosis_ke', 'tanggal_vaksin', 'status', 'completed_at');

        // Filter by status (default: pending)
        $status = $request->input('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_vaksin', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_vaksin', '<=', $request->input('date_to'));
        }

        $schedules = $query->paginate(30);

        $stats = [
            'total_pending' => VaccineSchedule::pending()->count(),
            'h7_count' => VaccineSchedule::pending()
                ->whereBetween('tanggal_vaksin', [$today, $h7Date])
                ->count(),
            'overdue' => VaccineSchedule::where('status', 'pending')
                ->where('tanggal_vaksin', '<', $today)
                ->count(),
        ];

        return view('reminders.index', compact('schedules', 'stats'));
    }

    public function complete(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $schedule = VaccineSchedule::findOrFail($id);
            $schedule->markAsCompleted($request->input('keterangan'));

            DB::commit();
            Log::info("Schedule {$id} marked as completed by user");

            return redirect()->route('reminders.index')
                ->with('success', 'Vaksinasi berhasil ditandai sebagai selesai');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to complete schedule {$id}: " . $e->getMessage());

            return redirect()->route('reminders.index')
                ->with('error', 'Gagal menyelesaikan vaksinasi: ' . $e->getMessage());
        }
    }

    public function markReminderSent($id)
    {
        try {
            $schedule = VaccineSchedule::findOrFail($id);
            
            // Mark as completed when reminder is sent (as per requirements)
            $schedule->markAsCompleted('Reminder terkirim via WhatsApp');

            Log::info("Schedule {$id} marked as completed after reminder sent");

            return response()->json([
                'success' => true,
                'message' => 'Reminder berhasil ditandai terkirim dan status diupdate ke selesai'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to mark reminder sent for schedule {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai reminder'
            ], 500);
        }
    }
}
