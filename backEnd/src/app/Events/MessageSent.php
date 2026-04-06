<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $idSender;
    public $idReceiver;

    public function __construct($message, $idSender, $idReceiver)
    {
        $this->message = $message;
        $this->idSender = $idSender;
        $this->idReceiver = $idReceiver;
    }

    public function broadcastOn(): array
    {
        // السمية هي SporeLink.{id}
        return [
            new PrivateChannel('SporeLink.' . $this->idReceiver),
        ];
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
}