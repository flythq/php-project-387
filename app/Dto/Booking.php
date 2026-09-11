<?php

namespace App\Dto;

use App\Dto\Enums\BookingState;

class Booking
{
    public function __construct(
        public readonly string $id,
        public readonly string $slotId,
        public readonly Host $host,
        public readonly Invitee $invitee,
        public readonly string $createdAt,
        public readonly BookingState $state,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            slotId: $data['slotId'],
            host: Host::fromArray($data['host']),
            invitee: Invitee::fromArray($data['invitee']),
            createdAt: $data['createdAt'],
            state: BookingState::from($data['state']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slotId' => $this->slotId,
            'host' => $this->host->toArray(),
            'invitee' => $this->invitee->toArray(),
            'createdAt' => $this->createdAt,
            'state' => $this->state->value,
        ];
    }
}
