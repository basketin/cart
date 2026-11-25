<?php

namespace Obelaw\Basketin\Cart\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CartActionUpdated
{
    use Dispatchable, SerializesModels;

    public string $eventId;

    public string $cartIdentifier;

    public string $action;

    public array $payload;

    public int $timestamp;

    public function __construct(string $cartIdentifier, string $action, array $payload = [])
    {
        $this->eventId = (string) Str::uuid();
        $this->cartIdentifier = $cartIdentifier;
        $this->action = $action;
        $this->payload = $payload;
        $this->timestamp = now()->timestamp;
    }
}
