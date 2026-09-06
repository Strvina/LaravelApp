<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Products;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_alert_is_created_for_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Products::factory()->create(['stock' => 2]);

        NotificationService::createLowStockAlert($product);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'product_id' => $product->id,
            'type' => 'low_stock',
            'resolved_at' => null,
        ]);
    }

    public function test_low_stock_alert_is_resolved_when_stock_is_replenished(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Products::factory()->create(['stock' => 2]);

        NotificationService::createLowStockAlert($product);

        $product->update(['stock' => 50]);
        NotificationService::resolveLowStockAlert($product);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'product_id' => $product->id,
            'type' => 'low_stock',
        ]);

        $this->assertNotNull(
            Notification::where('product_id', $product->id)->first()->resolved_at
        );
    }

    public function test_renaming_a_product_does_not_break_alert_resolution(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Products::factory()->create(['stock' => 2, 'name' => 'Original name']);

        NotificationService::createLowStockAlert($product);

        $product->update(['name' => 'Renamed product', 'stock' => 50]);
        NotificationService::resolveLowStockAlert($product);

        $this->assertNotNull(
            Notification::where('product_id', $product->id)->where('user_id', $admin->id)->first()->resolved_at
        );
    }

    public function test_unrelated_product_update_does_not_resurface_a_read_alert(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Products::factory()->create(['stock' => 2]);

        NotificationService::createLowStockAlert($product);
        Notification::where('product_id', $product->id)->update(['read_at' => now()]);

        $product->update(['description' => 'Updated description, stock unchanged.']);
        NotificationService::createLowStockAlert($product);

        $this->assertNotNull(
            Notification::where('product_id', $product->id)->where('user_id', $admin->id)->first()->read_at
        );
    }

    public function test_sync_low_stock_alerts_creates_and_resolves_alerts_without_a_product_save(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lowStock = Products::factory()->create(['stock' => 1]);
        $healthy = Products::factory()->create(['stock' => 100]);

        NotificationService::syncLowStockAlerts();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'product_id' => $lowStock->id,
            'type' => 'low_stock',
            'resolved_at' => null,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'product_id' => $healthy->id,
        ]);

        $lowStock->update(['stock' => 100]);
        NotificationService::syncLowStockAlerts();

        $this->assertNotNull(
            Notification::where('product_id', $lowStock->id)->first()->resolved_at
        );
    }
}
