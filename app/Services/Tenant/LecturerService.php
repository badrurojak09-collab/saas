<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\CreateLecturerAction;
use App\Actions\Tenant\UpdateLecturerAction;
use App\DTOs\Tenant\CreateLecturerData;
use App\Models\Tenant\Lecturer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LecturerService
{
    public function __construct(
        protected CreateLecturerAction $createLecturerAction,
        protected UpdateLecturerAction $updateLecturerAction,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Lecturer::with('user')->latest()->paginate($perPage);
    }

    public function create(CreateLecturerData $data): Lecturer
    {
        return $this->createLecturerAction->execute($data);
    }

    public function update(Lecturer $lecturer, CreateLecturerData $data): Lecturer
    {
        return $this->updateLecturerAction->execute($lecturer, $data);
    }

    public function delete(Lecturer $lecturer): bool
    {
        return (bool) $lecturer->delete();
    }
}
