<?php

namespace App\Dto;

class BookingRequest
{
    public function __construct(
        public readonly string $slotId,
        public readonly Invitee $invitee,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            slotId: $data['slotId'],
            invitee: Invitee::fromArray($data['invitee']),
        );
    }

    public function toArray(): array
    {
        return [
            'slotId' => $this->slotId,
            'invitee' => $this->invitee->toArray(),
        ];
    }
}
