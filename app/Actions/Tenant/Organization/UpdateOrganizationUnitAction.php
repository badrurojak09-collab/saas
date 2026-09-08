<?php

namespace App\Actions\Tenant\Organization;

use App\DTOs\Tenant\Organization\CreateOrganizationUnitData;
use App\Models\Tenant\OrganizationUnit;
use App\Services\Tenant\OrganizationHierarchyService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class UpdateOrganizationUnitAction
{
    public function __construct(private OrganizationHierarchyService $hierarchy) {}

    public function execute(OrganizationUnit $unit, CreateOrganizationUnitData $data): OrganizationUnit
    {
        return DB::connection('tenant')->transaction(function () use ($unit, $data): OrganizationUnit {
            $parent = $data->parentId ? OrganizationUnit::query()->findOrFail($data->parentId) : null;
            if ($parent) {
                $this->hierarchy->validateParent($parent, $data->type);
                $this->hierarchy->validateNoCycle($unit, $parent);
                if ($parent->perguruan_tinggi_id !== $data->perguruanTinggiId) {
                    throw new InvalidArgumentException('Organization parent must belong to the same perguruan tinggi.');
                }
            }

            $duplicate = OrganizationUnit::query()
                ->where('perguruan_tinggi_id', $data->perguruanTinggiId)
                ->where('code', $data->code)
                ->where($unit->getQualifiedKeyName(), '!=', $unit->getKey())
                ->exists();
            if ($duplicate) {
                throw new InvalidArgumentException('Organization code already exists for this perguruan tinggi.');
            }

            $unit->update([
                'perguruan_tinggi_id' => $data->perguruanTinggiId,
                'parent_id' => $data->parentId,
                'code' => $data->code,
                'name' => $data->name,
                'short_name' => $data->shortName,
                'type' => $data->type,
                'description' => $data->description,
                'sort_order' => $data->sortOrder,
            ]);

            return $unit->refresh();
        });
    }
}
