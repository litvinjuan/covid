<?php

namespace Store\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Store\Models\CartItem;

class CartItemProductNotEnoughStock
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var CartItem */
    private $item;
    /** @var integer */
    private $quantity;
    /** @var integer */
    private $stock;

    public function __construct(CartItem $item)
    {
        $this->item = $item;
        $this->quantity = $item->quantity;
        $this->stock = $item->product->stock;
    }

//    public function broadcastOn()
//    {
//        return new PrivateChannel('channel-name');
//    }
}
