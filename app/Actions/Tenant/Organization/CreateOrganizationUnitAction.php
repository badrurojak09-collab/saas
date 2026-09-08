<?php

namespace App\Actions\Tenant\Organization;

use App\DTOs\Tenant\Organization\CreateOrganizationUnitData;
use App\Models\Tenant\OrganizationUnit;
use App\Models\Tenant\PerguruanTinggi;
use App\Services\Tenant\OrganizationHierarchyService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CreateOrganizationUnitAction
{
    public function __construct(private OrganizationHierarchyService $hierarchy) {}

    public function execute(CreateOrganizationUnitData $data): OrganizationUnit
    {
        return DB::connection('tenant')->transaction(function () use ($data): OrganizationUnit {
            if (! PerguruanTinggi::query()->whereKey($data->perguruanTinggiId)->exists()) {
                throw new InvalidArgumentException('The selected perguruan tinggi does not exist.');
            }

            $parent = $data->parentId ? OrganizationUnit::query()->findOrFail($data->parentId) : null;
            if ($parent) {
                $this->hierarchy->validateParent($parent, $data->type);
                if ($parent->perguruan_tinggi_id !== $data->perguruanTinggiId) {
                    throw new InvalidArgumentException('Organization parent must belong to the same perguruan tinggi.');
                }
            }

            if (OrganizationUnit::query()->where('perguruan_tinggi_id', $data->perguruanTinggiId)->where('code', $data->code)->exists()) {
                throw new InvalidArgumentException('Organization code already exists for this perguruan tinggi.');
            }

            return OrganizationUnit::query()->create([
                'perguruan_tinggi_id' => $data->perguruanTinggiId,
                'parent_id' => $data->parentId,
                'code' => $data->code,
                'name' => $data->name,
                'short_name' => $data->shortName,
                'type' => $data->type,
                'description' => $data->description,
                'sort_order' => $data->sortOrder,
                'status' => 'active',
            ]);
        });
    }
}
