<?php

namespace App\Dto;

use App\Dto\Enums\SlotStatus;

class Slot
{
    public function __construct(
        public readonly string $id,
        public readonly string $start,
        public readonly string $end,
        public readonly int $durationMinutes,
        public readonly string $hostId,
        public readonly SlotStatus $status,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            start: $data['start'],
            end: $data['end'],
            durationMinutes: $data['durationMinutes'],
            hostId: $data['hostId'],
            status: SlotStatus::from($data['status']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'start' => $this->start,
            'end' => $this->end,
            'durationMinutes' => $this->durationMinutes,
            'hostId' => $this->hostId,
            'status' => $this->status->value,
        ];
    }
}
