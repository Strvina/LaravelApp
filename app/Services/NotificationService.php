<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Products;
use App\Models\User;

class NotificationService
{
    public static function notifyAdmins(string $type, string $message): void
    {
        User::where('role', 'admin')->each(function (User $admin) use ($type, $message): void {
            Notification::firstOrCreate([
                'user_id' => $admin->id,
                'type' => $type,
                'message' => $message,
            ]);
        });
    }

    public static function createLowStockAlert(Products $product, int $threshold = 5): void
    {
        if ($product->stock > $threshold) {
            return;
        }

        self::notifyAdmins(
            'low_stock',
            "Product '{$product->name}' has low stock: {$product->stock} remaining."
        );
    }
}
