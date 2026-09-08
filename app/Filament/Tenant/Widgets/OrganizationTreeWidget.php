<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Tenant\OrganizationUnit;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class OrganizationTreeWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    public function render(): View
    {
        $tree = $this->buildTree();

        return view('filament.widgets.organization-tree', [
            'tree' => $tree,
        ]);
    }

    private function buildTree(): ?array
    {
        $root = OrganizationUnit::query()
            ->whereNull('parent_id')
            ->with('children.children')
            ->first();

        return $this->recursiveToArray($root);
    }

    private function recursiveToArray(?OrganizationUnit $unit): ?array
    {
        if (! $unit) {
            return null;
        }

        return [
            'id' => $unit->id,
            'name' => $unit->name,
            'code' => $unit->code,
            'type' => $unit->type->value,
            'children' => $unit->children
                ->map(fn(OrganizationUnit $child): ?array => $this->recursiveToArray($child))
                ->filter()
                ->values()
                ->all(),
        ];
    }
}
