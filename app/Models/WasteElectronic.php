<?php

namespace App\Models;

class WasteElectronic extends Waste
{
    public function canSchedule(): bool
    {
        return $this->status === 'pending' && $this->safety_check === true;
    }

    public function getCompletionAmount(): int
    {
        return 100000;
    }
}
