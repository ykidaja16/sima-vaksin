<?php

namespace App\Exports;

use App\Models\Patient;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatientsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;
    protected $jenisVaksin;
    protected $sortField;
    protected $sortDirection;

    public function __construct($search = null, $jenisVaksin = null, $sortField = 'pid', $sortDirection = 'asc')
    {
        $this->search = $search;
        $this->jenisVaksin = $jenisVaksin;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
    }

    public function query()
    {
        $query = Patient::with(['vaccines.vaccineType']);

        // Search functionality
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('pid', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter by vaccine type
        if ($this->jenisVaksin) {
            $query->whereHas('vaccines.vaccineType', function ($q) {
                $q->where('nama_vaksin', $this->jenisVaksin);
            });
        }

        // Sorting
        $allowedSortFields = ['pid', 'nama_pasien', 'no_hp', 'alamat', 'dob'];
        
        if (in_array($this->sortField, $allowedSortFields)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('pid', 'asc');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'PID',
            'Nama Pasien',
            'No HP',
            'Alamat',
            'Tanggal Lahir',
            'Umur',
            'Jenis Vaksin',
        ];
    }

    public function map($patient): array
    {
        // Get vaccine types as comma-separated string
        $vaccineTypes = $patient->vaccines->map(function ($vaccine) {
            return $vaccine->jenis_vaksin;
        })->implode(', ');

        return [
            $patient->pid,
            $patient->nama_pasien,
            $patient->no_hp ?? '-',
            $patient->alamat ?? '-',
            $patient->dob ? $patient->dob->format('d-m-Y') : '-',
            $patient->age ? $patient->age . ' th' : '-',
            $vaccineTypes ?: '-',
        ];
    }
}
