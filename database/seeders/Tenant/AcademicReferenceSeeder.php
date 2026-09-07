<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AcademicReferenceSeeder extends Seeder
{
    public function run(): void
    {
        if (Schema::connection('tenant')->hasTable('system_metadata')) {
            $existingSchema = DB::connection('tenant')->table('system_metadata')->where('key', 'schema_version')->first();
            if ($existingSchema) {
                DB::connection('tenant')->table('system_metadata')->where('key', 'schema_version')->update([
                    'value' => '1.0.0',
                    'updated_at' => now(),
                ]);
            } else {
                DB::connection('tenant')->table('system_metadata')->insert([
                    'id' => (string) Str::uuid(),
                    'key' => 'schema_version',
                    'value' => '1.0.0',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $existingInstalled = DB::connection('tenant')->table('system_metadata')->where('key', 'installed_at')->first();
            if ($existingInstalled) {
                DB::connection('tenant')->table('system_metadata')->where('key', 'installed_at')->update([
                    'value' => now()->toIso8601String(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::connection('tenant')->table('system_metadata')->insert([
                    'id' => (string) Str::uuid(),
                    'key' => 'installed_at',
                    'value' => now()->toIso8601String(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
