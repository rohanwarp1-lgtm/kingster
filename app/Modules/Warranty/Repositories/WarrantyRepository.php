<?php

namespace App\Modules\Warranty\Repositories;

use App\Modules\Warranty\Models\WarrantyRegistration;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class WarrantyRepository
{
    public function all(array $filters = []): Collection|LengthAwarePaginator
    {
        $query = WarrantyRegistration::with(['approver', 'approvals'])
            ->filter($filters)
            ->orderBy('created_at', 'desc');

        if (isset($filters['paginate']) && $filters['paginate']) {
            return $query->paginate($filters['paginate'] ?? 10);
        }

        return $query->get();
    }

    public function find(int $id): ?WarrantyRegistration
    {
        return WarrantyRegistration::with(['approver', 'approvals'])->find($id);
    }

    public function create(array $data): WarrantyRegistration
    {
        return WarrantyRegistration::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->update($data);
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->delete();
    }

    public function restore(int $id): bool
    {
        $record = WarrantyRegistration::withTrashed()->find($id);
        if (!$record || !$record->trashed()) {
            return false;
        }
        return $record->restore();
    }

    public function findByTicketNo(string $ticketNo): ?WarrantyRegistration
    {
        return WarrantyRegistration::where('ticket_no', $ticketNo)->first();
    }

    public function getExpiredWarranties(): Collection
    {
        return WarrantyRegistration::where('status', 'approved')
            ->where('expiry_date', '<', now())
            ->get();
    }

    public function getExpiringSoon(int $days = 30): Collection
    {
        return WarrantyRegistration::where('status', 'approved')
            ->whereBetween('expiry_date', [now(), now()->addDays($days)])
            ->get();
    }
}
