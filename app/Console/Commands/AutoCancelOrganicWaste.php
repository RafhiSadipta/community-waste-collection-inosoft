<?php

namespace App\Console\Commands;

use App\Models\Waste;
use Illuminate\Console\Command;

class AutoCancelOrganicWaste extends Command
{
    protected $signature = 'wastes:auto-cancel-organic';

    protected $description = 'Auto-cancel pending pickups that exceeded their type-specific auto-cancel window (e.g. organic after 3 days)';

    public function handle(): int
    {
        $canceled = 0;

        foreach (Waste::where('status', 'pending')->get() as $waste) {
            $days = $waste->getAutoCancelAfterDays();

            if ($days === null) {
                continue;
            }

            if ($waste->created_at->diffInDays(now()) >= $days) {
                $waste->cancel();
                $canceled++;
                $this->line("Canceled: {$waste->id} ({$waste->type}, created {$waste->created_at->toDateTimeString()})");
            }
        }

        $this->info("Auto-cancel complete. {$canceled} waste(s) canceled.");

        return self::SUCCESS;
    }
}
