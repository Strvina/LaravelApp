<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Products;
use App\Models\User;

class NotificationService
{
    public static function createLowStockAlert(Products $product, int $threshold = 5): void
    {
        if ($product->stock > $threshold) {
            return;
        }

        $message = self::lowStockMessage($product);

        User::where('role', 'admin')->each(function (User $admin) use ($product, $message): void {
            $notification = Notification::query()
                ->where('user_id', $admin->id)
                ->where('type', 'low_stock')
                ->where('product_id', $product->id)
                ->unresolved()
                ->first();

            if ($notification) {
                if ($notification->message !== $message) {
                    $notification->update(['message' => $message, 'read_at' => null]);
                }

                return;
            }

            Notification::create([
                'user_id' => $admin->id,
                'product_id' => $product->id,
                'type' => 'low_stock',
                'message' => $message,
            ]);
        });
    }

    public static function resolveLowStockAlert(Products $product, int $threshold = 5): void
    {
        if ($product->stock <= $threshold) {
            return;
        }

        Notification::query()
            ->where('type', 'low_stock')
            ->where('product_id', $product->id)
            ->unresolved()
            ->update([
                'read_at' => now(),
                'resolved_at' => now(),
            ]);
    }

    public static function syncLowStockAlerts(int $threshold = 5): void
    {
        Products::query()->lowStock($threshold)->each(
            fn (Products $product) => self::createLowStockAlert($product, $threshold)
        );

        Notification::query()
            ->where('type', 'low_stock')
            ->unresolved()
            ->with('product')
            ->get()
            ->each(function (Notification $notification) use ($threshold): void {
                if ($notification->product && $notification->product->stock > $threshold) {
                    self::resolveLowStockAlert($notification->product, $threshold);
                }
            });
    }

    private static function lowStockMessage(Products $product): string
    {
        $identifier = $product->sku ? " ({$product->sku})" : '';

        return "Product '{$product->name}'{$identifier} has low stock: {$product->stock} remaining.";
    }
}
