# SIAKAD SaaS

Sistem Informasi Akademik multi-tenant (database-per-tenant).

Satu institusi = satu tenant = satu database. Isolasi data di level database, bukan `tenant_id` di setiap tabel.

## Stack (V1)

- PHP 8.3+
- Laravel 13
- MySQL 8
- Filament 5 (admin UI)
- UUID v7
- Storage V1: Google Shared Drive (Sprint 17)
- Redis / Laravel Queue untuk pekerjaan provisioning yang idempotent

## Arsitektur singkat

| Layer    | Connection                  | Isi                                   |
| -------- | --------------------------- | ------------------------------------- |
| Landlord | `landlord`                  | tenant, domain, billing, provisioning |
| Tenant   | `tenant` (database dinamis) | data akademik institusi               |

Tidak ada FK lintas database. Tidak ada `tenant_id` di Tenant DB.

## Local (Laragon)

Lihat [docs/local-setup.md](docs/local-setup.md).

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan siakad:health
php artisan test
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

## Landlord provisioning

`CreateTenantAction` hanya membuat metadata di landlord database (tenant, primary
database, domain, dan subscription opsional), lalu mengantrikan
`ProvisionTenantJob` setelah transaksi commit. Job ini membuat database tenant,
menjalankan migration pada koneksi `tenant`, melakukan health check, dan baru
kemudian mengaktifkan tenant. Credential database tenant disimpan menggunakan
encrypted Eloquent cast.

Jalankan worker terpisah untuk queue provisioning:

```bash
php artisan queue:work --queue=provisioning --tries=5 --backoff=10,30,120,300,900
```

Domain tenant hanya dapat di-resolve jika tercatat di landlord, terverifikasi,
dan tenant berstatus `active`. Middleware `tenant` selalu membersihkan context
dan koneksi tenant pada akhir request untuk mencegah kebocoran antar tenant.

## Identity dan authorization tenant (Sprint 6–7)

Identity tenant sepenuhnya berada di database tenant. Guard `tenant` menggunakan
`App\Models\Tenant\User`, sedangkan guard `platform` tetap menggunakan
`App\Models\Landlord\PlatformUser`. Tabel tenant mencakup user, password reset,
session, invitation, student profile, lecturer profile, staff profile, dan
organization membership.

Role dan permission tenant menggunakan guard `tenant` dan model Spatie yang
secara eksplisit memakai koneksi `tenant`. Role bawaan yang diseed adalah
`tenant_admin`, `academic_admin`, `baak`, `finance`, `dosen`, `kaprodi`,
`dekan`, `rektor`, dan `mahasiswa`. `OrganizationAccessService` memberikan
otorisasi tambahan berbasis membership organisasi; `tenant_admin` memiliki akses
platform tenant penuh.
