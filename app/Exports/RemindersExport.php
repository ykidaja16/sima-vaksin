<?php

namespace App\Exports;

use App\Models\VaccineSchedule;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RemindersExport implements FromQuery, WithHeadings, WithMapping
{
    protected $status;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($status = 'pending', $dateFrom = null, $dateTo = null)
    {
        $this->status = $status;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function query()
    {
        $today = now()->startOfDay();
        $h7Date = now()->addDays(7)->endOfDay();

        $query = VaccineSchedule::with([
                'patient:id,branch_id,pid,nama_pasien,no_hp,alamat,dob',
                'patient.branch:id,kode_prefix',
                'vaccine:id,vaccine_type_id',
                'vaccine.vaccineType:id,nama_vaksin,total_dosis'
            ])
            ->whereBetween('tanggal_vaksin', [$today, $h7Date])
            ->orderBy('tanggal_vaksin', 'asc')
            ->select('id', 'patient_id', 'vaccine_id', 'dosis_ke', 'tanggal_vaksin', 'status', 'completed_at');

        // Filter by status
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // Filter by date range
        if ($this->dateFrom) {
            $query->whereDate('tanggal_vaksin', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('tanggal_vaksin', '<=', $this->dateTo);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'PID',
            'Nama Pasien',
            'No HP',
            'Jenis Vaksin',
            'Dosis Ke',
            'Tanggal Vaksin',
            'Countdown',
            'Status',
        ];
    }

    public function map($schedule): array
    {
        $daysUntil = floor(now()->diffInDays($schedule->tanggal_vaksin, false));
        
        if ($daysUntil == 0) {
            $countdown = 'HARI INI';
        } elseif ($daysUntil == 1) {
            $countdown = 'Besok';
        } elseif ($daysUntil < 0) {
            $countdown = 'Overdue';
        } else {
            $countdown = $daysUntil . ' hari';
        }

        return [
            $schedule->patient->pid,
            $schedule->patient->nama_pasien,
            $schedule->patient->no_hp ?? '-',
            $schedule->vaccine->jenis_vaksin,
            $schedule->dosis_ke,
            $schedule->tanggal_vaksin->format('d-m-Y'),
            $countdown,
            ucfirst($schedule->status),
        ];
    }
}
