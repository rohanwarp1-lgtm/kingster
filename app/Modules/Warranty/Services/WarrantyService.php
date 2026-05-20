<?php

namespace App\Modules\Warranty\Services;

use App\Modules\Warranty\Models\WarrantyRegistration;
use App\Modules\Warranty\Models\WarrantyApproval;
use App\Modules\Warranty\Repositories\WarrantyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Exception;

class WarrantyService
{
    public function __construct(
        private WarrantyRepository $repository
    ) {}

    public function register(array $data): WarrantyRegistration
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['invoice_file']) && $data['invoice_file'] instanceof UploadedFile) {
                $data['invoice_file'] = $this->uploadInvoice($data['invoice_file']);
            }

            $warranty = $this->repository->create($data);

            WarrantyApproval::create([
                'warranty_id' => $warranty->id,
                'approver_id' => auth()->id(),
                'action' => 'submitted',
                'notes' => 'Warranty registration submitted',
                'ip_address' => request()->ip(),
            ]);

            activity()
                ->performedOn($warranty)
                ->causedBy(auth()->user())
                ->withProperties([
                    'ticket_no' => $warranty->ticket_no,
                    'customer_name' => $warranty->customer_name,
                ])
                ->log('Warranty registration submitted');

            $this->notifyCustomer($warranty, 'registration_submitted');

            return $warranty;
        });
    }

    public function startReview(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $warranty = $this->repository->find($id);
            
            if (!$warranty || $warranty->status !== 'pending') {
                throw new Exception('Cannot start review for this warranty');
            }

            $updated = $this->repository->update($id, ['status' => 'under_review']);

            if ($updated) {
                WarrantyApproval::create([
                    'warranty_id' => $warranty->id,
                    'approver_id' => auth()->id(),
                    'action' => 'under_review',
                    'notes' => 'Review started',
                    'ip_address' => request()->ip(),
                ]);

                activity()
                    ->performedOn($warranty)
                    ->causedBy(auth()->user())
                    ->log('Warranty review started');
            }

            return $updated;
        });
    }

    public function approve(int $id, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($id, $notes) {
            $warranty = $this->repository->find($id);
            
            if (!$warranty) {
                throw new Exception('Warranty not found');
            }

            $updated = $this->repository->update($id, [
                'status' => 'approved',
                'approval_notes' => $notes,
                'approved_by' => auth()->id(),
            ]);

            if ($updated) {
                WarrantyApproval::create([
                    'warranty_id' => $warranty->id,
                    'approver_id' => auth()->id(),
                    'action' => 'approved',
                    'notes' => $notes,
                    'ip_address' => request()->ip(),
                ]);

                activity()
                    ->performedOn($warranty)
                    ->causedBy(auth()->user())
                    ->log('Warranty approved');

                $this->notifyCustomer($warranty, 'approved');
            }

            return $updated;
        });
    }

    public function reject(int $id, string $reason): bool
    {
        return DB::transaction(function () use ($id, $reason) {
            $warranty = $this->repository->find($id);
            
            if (!$warranty) {
                throw new Exception('Warranty not found');
            }

            $updated = $this->repository->update($id, [
                'status' => 'rejected',
                'approval_notes' => $reason,
            ]);

            if ($updated) {
                WarrantyApproval::create([
                    'warranty_id' => $warranty->id,
                    'approver_id' => auth()->id(),
                    'action' => 'rejected',
                    'notes' => $reason,
                    'ip_address' => request()->ip(),
                ]);

                activity()
                    ->performedOn($warranty)
                    ->causedBy(auth()->user())
                    ->withProperties(['reason' => $reason])
                    ->log('Warranty rejected');

                $this->notifyCustomer($warranty, 'rejected', $reason);
            }

            return $updated;
        });
    }

    public function cancel(int $id, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($id, $reason) {
            $warranty = $this->repository->find($id);
            
            if (!$warranty) {
                throw new Exception('Warranty not found');
            }

            $updated = $this->repository->update($id, [
                'status' => 'cancelled',
                'approval_notes' => $reason,
            ]);

            if ($updated) {
                activity()
                    ->performedOn($warranty)
                    ->causedBy(auth()->user())
                    ->log('Warranty cancelled');
            }

            return $updated;
        });
    }

    public function markExpired(): int
    {
        $expired = $this->repository->getExpiredWarranties();
        $count = 0;

        foreach ($expired as $warranty) {
            if ($this->repository->update($warranty->id, ['status' => 'expired'])) {
                $count++;
            }
        }

        return $count;
    }

    public function getDashboardStats(): array
    {
        $counts = \App\Modules\Warranty\Models\WarrantyRegistration::query()
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $expiringSoon = $this->repository->getExpiringSoon()->count();

        return [
            'total'        => (int) $counts->sum(),
            'pending'      => (int) ($counts['pending']      ?? 0),
            'under_review' => (int) ($counts['under_review'] ?? 0),
            'approved'     => (int) ($counts['approved']     ?? 0),
            'expiring_soon' => $expiringSoon,
        ];
    }

    protected function uploadInvoice(UploadedFile $file): string
    {
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '.' . $file->getClientOriginalExtension();

        $path = 'warranties/invoices';
        $file->storeAs($path, $filename, 'public');

        return "storage/{$path}/{$filename}";
    }

    protected function notifyCustomer(WarrantyRegistration $warranty, string $event, ?string $reason = null): void
    {
        try {
            $notificationClass = match($event) {
                'registration_submitted' => \App\Modules\Warranty\Notifications\WarrantySubmittedNotification::class,
                'approved' => \App\Modules\Warranty\Notifications\WarrantyApprovedNotification::class,
                'rejected' => \App\Modules\Warranty\Notifications\WarrantyRejectedNotification::class,
                default => null,
            };

            if ($notificationClass) {
                $warranty->notify(new $notificationClass($warranty, $reason));
            }
        } catch (Exception $e) {
            // Log but don't fail the transaction
            \Log::error('Failed to send warranty notification: ' . $e->getMessage());
        }
    }
}
