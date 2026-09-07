<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'KTP', 'name' => 'Kartu Tanda Penduduk', 'description' => 'Identitas KTP Mahasiswa', 'is_required' => true],
            ['code' => 'KK', 'name' => 'Kartu Keluarga', 'description' => 'Kartu Keluarga Mahasiswa', 'is_required' => true],
            ['code' => 'IJAZAH', 'name' => 'Ijazah Pendidikan Terakhir', 'description' => 'Ijazah SMA/SMK/D3/S1', 'is_required' => true],
            ['code' => 'TRANSKRIP', 'name' => 'Transkrip Nilai Terakhir', 'description' => 'Transkrip nilai jenjang sebelumnya', 'is_required' => false],
            ['code' => 'AKTA', 'name' => 'Akta Kelahiran', 'description' => 'Akta Kelahiran resmi', 'is_required' => true],
        ];

        foreach ($types as $type) {
            DocumentType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'entity_type' => 'student',
                    'is_required' => $type['is_required'],
                    'status' => 'active',
                ]
            );
        }
    }
}
