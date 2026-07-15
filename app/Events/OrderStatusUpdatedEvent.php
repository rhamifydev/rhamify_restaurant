<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $orderId;
    public int $userId;
    public string $orderStatus;
    public string $paymentStatus;
    public string $label;
    public string $className;

    public function __construct(Order $order)
    {
        $this->orderId = $order->id;
        $this->userId = $order->user_id;
        $this->orderStatus = $order->order_status;
        $this->paymentStatus = $order->payment_status;
        $this->label = match ($order->order_status) {
            'in_process' => 'In Process',
            'delivered' => 'Delivered',
            'declined' => 'Declined',
            default => 'Pending',
        };
        $this->className = match ($order->order_status) {
            'delivered' => 'complete',
            'declined' => 'cancel',
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
