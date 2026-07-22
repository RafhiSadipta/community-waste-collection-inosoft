<?php

namespace App\Models;

class WasteOrganic extends Waste
{
    public function getAutoCancelAfterDays(): ?int
    {
        return 3;
    }
}
