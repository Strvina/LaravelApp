<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SyncLowStockAlerts extends Command
{
    protected $signature = 'products:sync-low-stock-alerts';

    protected $description = 'Create low-stock notifications for products that need one and resolve ones that no longer apply';

    public function handle(): int
    {
        NotificationService::syncLowStockAlerts();

        $this->info('Low-stock alerts synced.');

        return self::SUCCESS;
    }
}
