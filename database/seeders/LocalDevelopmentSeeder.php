<?php

namespace Database\Seeders;

use App\Actions\Landlord\CreateTenantAction;
use App\DTOs\Tenant\ProvisionTenantData;
use App\DTOs\Tenant\TenantAdminData;
use App\Enums\Landlord\BillingCycle;
use App\Enums\Landlord\DomainType;
use App\Enums\Landlord\PackageStatus;
use App\Enums\Landlord\PlatformUserStatus;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Enums\Tenant\DegreeLevel;
use App\Enums\Tenant\Gender;
use App\Enums\Tenant\SemesterType;
use App\Enums\Tenant\StudentStatus;
use App\Enums\Tenant\UserStatus;
use App\Enums\Tenant\UserType;
use App\Models\Landlord\Domain;
use App\Models\Landlord\Package;
use App\Models\Landlord\PlatformUser;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\AcademicYear;
use App\Models\Tenant\Building;
use App\Models\Tenant\Course;
use App\Models\Tenant\Curriculum;
use App\Models\Tenant\Department;
use App\Models\Tenant\Faculty;
use App\Models\Tenant\Lecturer;
use App\Models\Tenant\Role;
use App\Models\Tenant\Room;
use App\Models\Tenant\Semester;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudyProgram;
use App\Models\Tenant\User as TenantUser;
use App\Services\Tenant\TenantProvisioningService;
use App\Tenancy\Contracts\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LocalDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('=== [1/3] Menyiapkan Landlord Platform Users & Packages ===');

        // 1. Landlord Super Admin
        $platformAdmin = PlatformUser::updateOrCreate(
            ['email' => 'admin@siakad.test'],
            [
                'name' => 'Super Administrator Landlord',
                'password' => Hash::make('password'),
                'status' => PlatformUserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );
        $this->command?->info("✓ Platform Admin: admin@siakad.test / password");

        // 2. Packages
        $starterPkg = Package::updateOrCreate(
            ['code' => 'STARTER'],
            [
                'name' => 'Paket Starter',
                'description' => 'Untuk institusi rintisan hingga 500 mahasiswa aktif.',
                'price' => 1500000,
                'billing_cycle' => BillingCycle::MONTHLY,
                'status' => PackageStatus::Active,
                'limits' => ['max_students' => 500, 'max_storage_gb' => 10],
                'sort_order' => 1,
            ]
        );

        $proPkg = Package::updateOrCreate(
            ['code' => 'PRO'],
            [
                'name' => 'Paket Professional',
                'description' => 'Untuk universitas dan politeknik hingga 2.500 mahasiswa.',
                'price' => 3500000,
                'billing_cycle' => BillingCycle::MONTHLY,
                'status' => PackageStatus::Active,
                'limits' => ['max_students' => 2500, 'max_storage_gb' => 50],
                'sort_order' => 2,
            ]
        );

        $this->command?->info("✓ Paket Starter & Pro siap.");

        // 3. Demo Tenant
        $this->command?->info('=== [2/3] Menyiapkan Tenant Demo (Universitas Demo) ===');

        $demoTenant = Tenant::where('code', 'DEMO')->first();

        if (! $demoTenant) {
            $sqlitePath = database_path('tenant_demo.sqlite');
            if (file_exists($sqlitePath)) {
                @unlink($sqlitePath);
            }

            $createAction = app(CreateTenantAction::class);
            /** @var TenantProvisioningService $provisioningService */
            $provisioningService = app(TenantProvisioningService::class);

            $demoTenant = $createAction->execute(new ProvisionTenantData(
                name: 'Universitas Demo Indonesia',
                code: 'DEMO',
                slug: 'demo',
                database: $sqlitePath,
                driver: 'sqlite',
                packageId: (string) $proPkg->getKey(),
                admin: new TenantAdminData(
                    name: 'Admin Universitas Demo',
                    email: 'admin@demo.test',
                    username: 'admin',
                    password: 'password',
                ),
            ));

            $job = ProvisioningJob::create([
                'tenant_id' => $demoTenant->getKey(),
                'job_type' => 'PROVISION_TENANT',
                'status' => ProvisioningJobStatus::PENDING,
                'attempts' => 1,
            ]);

            $provisioningService->provision($demoTenant, $job, attempt: 1);
            $this->command?->info("✓ Database tenant_demo.sqlite berhasil diprovisioning.");
        }

        // Tambahkan Domain lokal yang fleksibel (localhost, 127.0.0.1, demo.mysaasmp.test)
        $domains = [
            'localhost' => DomainType::CUSTOM,
            '127.0.0.1' => DomainType::CUSTOM,
            'demo.mysaasmp.test' => DomainType::SUBDOMAIN,
            'demo.siakad.test' => DomainType::SUBDOMAIN,
        ];

        foreach ($domains as $domainName => $type) {
            Domain::firstOrCreate(
                ['domain' => $domainName],
                [
                    'tenant_id' => $demoTenant->getKey(),
                    'domain' => $domainName,
                    'type' => $type,
                    'is_primary' => $domainName === 'demo.mysaasmp.test',
                    'is_verified' => true,
                    'verified_at' => now(),
                    'ssl_status' => 'active',
                ]
            );
        }
        $this->command?->info("✓ Domain lokal didaftarkan (localhost, 127.0.0.1, demo.mysaasmp.test).");

        // 4. Seeding Data Akademik & Sivitas Tenant Demo
        $this->command?->info('=== [3/3] Menyiapkan Sivitas & Data Akademik Tenant Demo ===');

        /** @var TenantManager $tenantManager */
        $tenantManager = app(TenantManager::class);
        $tenantManager->initialize($demoTenant);

        try {
            // Inisialisasi Peran & Hak Akses Dasar Tenant
            (new \Database\Seeders\Tenant\TenantRolePermissionSeeder())->run();
            Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'tenant']);
            Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'tenant']);
            Role::firstOrCreate(['name' => 'lecturer', 'guard_name' => 'tenant']);
            Role::firstOrCreate(['name' => 'student', 'guard_name' => 'tenant']);

            // Pastikan User Staff ada
            $staffUser = TenantUser::firstOrCreate(
                ['email' => 'staff@demo.test'],
                [
                    'name' => 'Budi Santoso (Staff Akademik)',
                    'username' => 'staff',
                    'password' => Hash::make('password'),
                    'status' => UserStatus::Active,
                    'user_type' => UserType::Staff,
                    'email_verified_at' => now(),
                ]
            );
            if (! $staffUser->hasRole('staff')) {
                $staffUser->assignRole('staff');
            }

            // Tahun Akademik & Semester
            $ta = AcademicYear::firstOrCreate(
                ['code' => '2026/2027'],
                [
                    'name' => 'Tahun Akademik 2026/2027',
                    'start_date' => '2026-09-01',
                    'end_date' => '2027-08-31',
                    'status' => 'active',
                ]
            );

            $semester = Semester::firstOrCreate(
                ['code' => '20261'],
                [
                    'academic_year_id' => $ta->id,
                    'name' => 'Semester Ganjil 2026/2027',
                    'semester_type' => SemesterType::Odd,
                    'start_date' => '2026-09-01',
                    'end_date' => '2027-01-31',
                    'is_active' => true,
                    'sequence' => 1,
                ]
            );

            // Fakultas, Jurusan, Prodi
            $fakultas = Faculty::firstOrCreate(
                ['code' => 'FTI'],
                [
                    'name' => 'Fakultas Teknologi Informasi',
                    'short_name' => 'FTI',
                    'status' => 'active',
                ]
            );

            $jurusan = Department::firstOrCreate(
                ['code' => 'IF'],
                [
                    'faculty_id' => $fakultas->id,
                    'name' => 'Jurusan Informatika & Komputer',
                    'status' => 'active',
                ]
            );

            $prodiTIF = StudyProgram::firstOrCreate(
                ['code' => '55201'],
                [
                    'department_id' => $jurusan->id,
                    'name' => 'S1 Teknik Informatika',
                    'degree_level' => DegreeLevel::S1,
                    'status' => 'active',
                ]
            );

            $prodiSI = StudyProgram::firstOrCreate(
                ['code' => '57201'],
                [
                    'department_id' => $jurusan->id,
                    'name' => 'S1 Sistem Informasi',
                    'degree_level' => DegreeLevel::S1,
                    'status' => 'active',
                ]
            );

            // Gedung & Ruang
            $gedung = Building::firstOrCreate(
                ['code' => 'G-A'],
                [
                    'name' => 'Gedung Rektorat & Kuliah A',
                    'description' => 'Gedung Rektorat & Kuliah 4 Lantai',
                    'status' => 'active',
                ]
            );

            Room::firstOrCreate(
                ['code' => 'LAB-01'],
                [
                    'building_id' => $gedung->id,
                    'name' => 'Laboratorium Komputer Dasar',
                    'capacity' => 40,
                ]
            );

            Room::firstOrCreate(
                ['code' => 'RK-201'],
                [
                    'building_id' => $gedung->id,
                    'name' => 'Ruang Kuliah Teori 201',
                    'capacity' => 60,
                ]
            );

            // Dosen
            Lecturer::firstOrCreate(
                ['nidn' => '0012058501'],
                [
                    'employee_number' => '198505122010121001',
                    'name' => 'Hendra Wijaya',
                    'academic_title' => 'Dr. Ir. Hendra Wijaya, M.Kom.',
                    'email' => 'hendra@demo.test',
                    'phone' => '081234567890',
                    'status' => 'active',
                ]
            );

            // Mahasiswa
            Student::firstOrCreate(
                ['student_number' => '202655201001'],
                [
                    'name' => 'Ahmad Rizky Pratama',
                    'study_program_id' => $prodiTIF->id,
                    'entry_year' => 2026,
                    'email' => 'rizky@mhs.demo.test',
                    'phone' => '082198765432',
                    'status' => StudentStatus::Active,
                ]
            );

            Student::firstOrCreate(
                ['student_number' => '202657201001'],
                [
                    'name' => 'Nabila Putri Anggraini',
                    'study_program_id' => $prodiSI->id,
                    'entry_year' => 2026,
                    'email' => 'nabila@mhs.demo.test',
                    'phone' => '082198765433',
                    'status' => StudentStatus::Active,
                ]
            );

            $this->command?->info("✓ Data sivitas, fakultas, prodi, dan akademik berhasil disiapkan.");
        } finally {
            $tenantManager->end();
        }

        $this->command?->info('');
        $this->command?->info('================================================================');
        $this->command?->info('🚀 LINGKUNGAN PENGUJIAN LOKAL SIAP DIGUNAKAN!');
        $this->command?->info('================================================================');
        $this->command?->info('1. LANDLORD ADMIN PANEL:');
        $this->command?->info('   - URL: http://localhost:8000/admin (atau http://mysaasmp.test/admin)');
        $this->command?->info('   - Email: admin@siakad.test');
        $this->command?->info('   - Password: password');
        $this->command?->info('');
        $this->command?->info('2. TENANT PANEL (Universitas Demo):');
        $this->command?->info('   - URL: http://localhost:8000/tenant (atau http://demo.mysaasmp.test/tenant)');
        $this->command?->info('   - Login Admin: admin@demo.test / password');
        $this->command?->info('   - Login Staff: staff@demo.test / password');
        $this->command?->info('================================================================');
    }
}
