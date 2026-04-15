<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Models\VaccineType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createRoles();
        $this->createBranches();
        $this->createVaccineTypes();
        $this->createDefaultUsers();
    }

    private function createRoles(): void
    {
        $roles = [
            [
                'nama_role' => 'IT',
                'deskripsi' => 'Administrator IT - Mengelola master data dan user',
                'is_active' => true,
            ],
            [
                'nama_role' => 'Admin',
                'deskripsi' => 'Admin Operasional - Mengelola data pasien dan reminder',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['nama_role' => $role['nama_role']],
                $role
            );
        }

        $this->command->info('Roles created successfully.');
    }

    private function createBranches(): void
    {
        $branches = [
            [
                'nama_cabang' => 'Ciliwung',
                'kode_prefix' => 'LX',
                'alamat' => 'Jl. Ciliwung No. 1, Jakarta',
                'no_telp' => '021-1234567',
                'is_active' => true,
            ],
            [
                'nama_cabang' => 'Tangkuban Perahu',
                'kode_prefix' => 'LZ',
                'alamat' => 'Jl. Tangkuban Perahu No. 2, Bandung',
                'no_telp' => '022-7654321',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['kode_prefix' => $branch['kode_prefix']],
                $branch
            );
        }

        $this->command->info('Branches created successfully.');
    }

    private function createVaccineTypes(): void
    {
        $vaccineTypes = [
            [
                'nama_vaksin' => 'HPV',
                'deskripsi' => 'Vaksin Human Papillomavirus untuk mencegah kanker serviks',
                'interval_bulan' => [0, 2, 6],
                'total_dosis' => 3,
                'is_active' => true,
            ],
            [
                'nama_vaksin' => 'Hepatitis',
                'deskripsi' => 'Vaksin Hepatitis B untuk mencegah infeksi virus hepatitis B',
                'interval_bulan' => [0, 1, 6],
                'total_dosis' => 3,
                'is_active' => true,
            ],
            [
                'nama_vaksin' => 'Influenza',
                'deskripsi' => 'Vaksin flu untuk mencegah infeksi virus influenza',
                'interval_bulan' => [0, 12],
                'total_dosis' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($vaccineTypes as $vaccineType) {
            VaccineType::firstOrCreate(
                ['nama_vaksin' => $vaccineType['nama_vaksin']],
                $vaccineType
            );
        }

        $this->command->info('Vaccine types created successfully.');
    }

    private function createDefaultUsers(): void
    {
        $itRole = Role::where('nama_role', 'IT')->first();
        $adminRole = Role::where('nama_role', 'Admin')->first();

        if (!$itRole || !$adminRole) {
            $this->command->error('Roles not found. Please run role seeder first.');
            return;
        }

        // Create IT User
        User::firstOrCreate(
            ['username' => 'it'],
            [
                'name' => 'IT Administrator',
                'username' => 'it',
                'password' => Hash::make('password'),
                'role_id' => $itRole->id,
                'is_active' => true,
            ]
        );

        // Create Admin User
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Operasional',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        $this->command->info('Default users created successfully.');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('------------------');
        $this->command->info('IT User:');
        $this->command->info('  Username: it');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Admin User:');
        $this->command->info('  Username: admin');
        $this->command->info('  Password: password');
    }
}
