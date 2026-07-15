<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $reservationId;
    public int $userId;
    public string $status;
    public string $label;
    public string $className;

    public function __construct(Reservation $reservation)
    {
        $this->reservationId = $reservation->id;
        $this->userId = $reservation->user_id;
        $this->status = $reservation->status ?? 'pending';
        $this->label = match ($this->status) {
            'approved' => 'Approved',
            'complete' => 'Complete',
            'cancel' => 'Cancel',
            default => 'Pending',
        };
        $this->className = match ($this->status) {
            'complete' => 'complete',
            'cancel' => 'cancel',
            default => 'active',
        };
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-dashboard.'.$this->userId),
        ];
    }
}
