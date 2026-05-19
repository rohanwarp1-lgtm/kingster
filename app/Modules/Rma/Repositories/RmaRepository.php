<?php

namespace App\Modules\Rma\Repositories;

use App\Modules\Rma\Models\RmaTicket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RmaRepository
{
    public function all(array $filters = []): Collection|LengthAwarePaginator
    {
        $query = RmaTicket::with(['assignee', 'activities', 'comments'])
            ->filter($filters)
            ->orderBy('created_at', 'desc');

        if (isset($filters['paginate']) && $filters['paginate']) {
            return $query->paginate($filters['paginate'] ?? 10);
        }

        return $query->get();
    }

    public function find(int $id): ?RmaTicket
    {
        return RmaTicket::with(['assignee', 'activities', 'comments', 'attachments'])->find($id);
    }

    public function create(array $data): RmaTicket
    {
        return RmaTicket::create($data);
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
        $record = RmaTicket::withTrashed()->find($id);
        if (!$record || !$record->trashed()) {
            return false;
        }
        return $record->restore();
    }

    public function findByTicketId(string $ticketId): ?RmaTicket
    {
        return RmaTicket::where('ticket_id', $ticketId)->first();
    }

    public function getOverdueTickets(): Collection
    {
        return RmaTicket::where('status', '!=', 'closed')
            ->where('sla_deadline', '<', now())
            ->get();
    }

    public function getUnassignedTickets(): Collection
    {
        return RmaTicket::whereNull('assigned_to')
            ->whereNotIn('status', ['closed', 'rejected'])
            ->get();
    }

    public function getStats(): array
    {
        return [
            'total' => RmaTicket::count(),
            'open' => RmaTicket::where('status', 'open')->count(),
            'under_review' => RmaTicket::where('status', 'under_review')->count(),
            'approved' => RmaTicket::where('status', 'approved')->count(),
            'closed' => RmaTicket::where('status', 'closed')->count(),
            'overdue' => $this->getOverdueTickets()->count(),
            'unassigned' => $this->getUnassignedTickets()->count(),
        ];
    }
}
