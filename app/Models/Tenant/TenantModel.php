<?php

namespace App\Models\Tenant;

use App\Models\Concerns\HasUuidV7;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Exceptions\TenantContextMissingException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class TenantModel extends Model
{
    use HasFactory;
    use HasUuidV7;

    protected $connection = 'tenant';

    protected static function newFactory()
    {
        $modelName = class_basename(static::class);
        $factoryClass = 'Database\\Factories\\Tenant\\'.$modelName.'Factory';

        if (class_exists($factoryClass)) {
            return $factoryClass::new();
        }

        return null;
    }

    public function getConnection()
    {
        if (config('tenancy.strict', true)) {
            /** @var TenantManager|null $manager */
            $manager = app()->bound(TenantManager::class)
                ? app(TenantManager::class)
                : null;

            if ($manager && ! $manager->isInitialized()) {
                throw new TenantContextMissingException(
                    'Cannot query tenant model ['.static::class.'] without an active tenant context.'
                );
            }
        }

        return parent::getConnection();
    }
}
