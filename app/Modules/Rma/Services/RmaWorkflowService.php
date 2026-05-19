<?php

namespace App\Modules\Rma\Services;

use App\Modules\Rma\Models\RmaTicket;
use App\Modules\Rma\Models\RmaActivity;
use App\Modules\Rma\Models\RmaComment;
use App\Modules\Rma\Models\RmaAttachment;
use App\Modules\Rma\Repositories\RmaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Exception;

class RmaWorkflowService
{
    protected array $workflowTransitions = [
        'open' => ['under_review', 'rejected'],
        'under_review' => ['approved', 'rejected'],
        'approved' => ['pickup_pending'],
        'rejected' => ['closed'],
        'pickup_pending' => ['pickup_completed'],
        'pickup_completed' => ['replacement_shipped'],
        'replacement_shipped' => ['closed'],
        'closed' => [],
    ];

    public function __construct(
        private RmaRepository $repository
    ) {}

    public function createTicket(array $data): RmaTicket
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['bill_file']) && $data['bill_file'] instanceof UploadedFile) {
                $data['bill_file'] = $this->uploadBill($data['bill_file']);
            }

            $data['status'] = 'open';
            $ticket = $this->repository->create($data);

            $this->logActivity($ticket->id, 'created', null, 'open', 'Ticket created');

            activity()
                ->performedOn($ticket)
                ->causedBy(auth()->user())
                ->withProperties([
                    'ticket_id' => $ticket->ticket_id,
                    'customer_name' => $ticket->customer_name,
                    'issue_type' => $ticket->issue_type,
                ])
                ->log('RMA ticket created');

            $this->notifyCustomer($ticket, 'created');

            return $ticket;
        });
    }

    public function canTransition(string $fromStatus, string $toStatus): bool
    {
        $allowedTransitions = $this->workflowTransitions[$fromStatus] ?? [];
        return in_array($toStatus, $allowedTransitions);
    }

    public function transition(RmaTicket $ticket, string $newStatus, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($ticket, $newStatus, $notes) {
            if (!$this->canTransition($ticket->status, $newStatus)) {
                throw new Exception("Invalid status transition from '{$ticket->status}' to '{$newStatus}'");
            }

            $oldStatus = $ticket->status;

            $ticket->update(['status' => $newStatus]);

            $this->logActivity($ticket->id, 'status_changed', $oldStatus, $newStatus, $notes);

            activity()
                ->performedOn($ticket)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ])
                ->log("RMA status changed from {$oldStatus} to {$newStatus}");

            $this->handlePostTransition($ticket, $newStatus);

            return true;
        });
    }

    public function assignTicket(RmaTicket $ticket, int $userId): bool
    {
        return DB::transaction(function () use ($ticket, $userId) {
            $oldAssignee = $ticket->assigned_to;
            
            $ticket->update(['assigned_to' => $userId]);

            $this->logActivity($ticket->id, 'assigned', $oldAssignee, $userId, 'Ticket assigned to staff');

            activity()
                ->performedOn($ticket)
                ->causedBy(auth()->user())
                ->withProperties(['assigned_to' => $userId])
                ->log('RMA ticket assigned');

            return true;
        });
    }

    public function addComment(RmaTicket $ticket, string $content, bool $isInternal = false): RmaComment
    {
        return DB::transaction(function () use ($ticket, $content, $isInternal) {
            $comment = RmaComment::create([
                'rma_ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'content' => $content,
                'is_internal' => $isInternal,
                'ip_address' => request()->ip(),
            ]);

            $this->logActivity($ticket->id, $isInternal ? 'note' : 'comment', null, null, Str::limit($content, 100));

            if (!$isInternal) {
                $this->notifyCustomer($ticket, 'comment_added');
            }

            return $comment;
        });
    }

    public function uploadAttachment(RmaTicket $ticket, UploadedFile $file): RmaAttachment
    {
        return DB::transaction(function () use ($ticket, $file) {
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                . '.' . $file->getClientOriginalExtension();

            $path = "rma/{$ticket->id}/attachments";
            $file->storeAs($path, $filename, 'public');

            $attachment = RmaAttachment::create([
                'rma_ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => "storage/{$path}/{$filename}",
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $this->logActivity($ticket->id, 'attachment', null, null, "Uploaded file: {$attachment->file_name}");

            return $attachment;
        });
    }

    public function getDashboardStats(): array
    {
        return $this->repository->getStats();
    }

    protected function handlePostTransition(RmaTicket $ticket, string $newStatus): void
    {
        match($newStatus) {
            'approved' => $this->schedulePickup($ticket),
            'rejected' => $this->notifyCustomer($ticket, 'rejected'),
            'closed' => $this->notifyCustomer($ticket, 'closed'),
            default => null,
        };
    }

    protected function schedulePickup(RmaTicket $ticket): void
    {
        activity()
            ->performedOn($ticket)
            ->log('Pickup scheduling initiated');
    }

    protected function logActivity(int $ticketId, string $action, ?string $oldValue, ?string $newValue, ?string $notes = null): void
    {
        RmaActivity::create([
            'rma_ticket_id' => $ticketId,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'notes' => $notes,
            'ip_address' => request()->ip(),
        ]);
    }

    protected function uploadBill(UploadedFile $file): string
    {
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '.' . $file->getClientOriginalExtension();

        $path = 'rma/bills';
        $file->storeAs($path, $filename, 'public');

        return "storage/{$path}/{$filename}";
    }

    protected function notifyCustomer(RmaTicket $ticket, string $event): void
    {
        try {
            $notificationClass = match($event) {
                'created' => \App\Modules\Rma\Notifications\RmaCreatedNotification::class,
                'comment_added' => \App\Modules\Rma\Notifications\RmaCommentNotification::class,
                'rejected' => \App\Modules\Rma\Notifications\RmaRejectedNotification::class,
                'closed' => \App\Modules\Rma\Notifications\RmaClosedNotification::class,
                default => null,
            };

            if ($notificationClass) {
                $ticket->notify(new $notificationClass($ticket));
            }
        } catch (Exception $e) {
            \Log::error('Failed to send RMA notification: ' . $e->getMessage());
        }
    }
}
