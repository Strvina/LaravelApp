<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\Notification;
use App\Models\Products;
use App\Models\ToDo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Disable foreign key constraints during seeding
        if (\DB::getDriverName() === 'pgsql') {
            \DB::statement('SET CONSTRAINTS ALL DEFERRED');
        } else {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        // Brišemo postojeće podatke i kreiramo nove svaki put
        ActivityLog::truncate();
        Notification::truncate();
        Expense::truncate();
        ToDo::truncate();
        Products::truncate();
        User::truncate();

        // Re-enable foreign key constraints
        if (\DB::getDriverName() !== 'pgsql') {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $admin = User::create([
            'name' => 'OpsDesk Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $manager = User::create([
            'name' => 'Operations Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $assistant = User::create([
            'name' => 'Inventory Assistant',
            'email' => 'assistant@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $products = collect([
            [
                'name' => 'Wireless Scanner',
                'price' => 12990,
                'stock' => 12,
                'category' => 'Hardware',
                'brand' => 'LogiTech',
                'description' => 'Portable warehouse scanner for inventory intake and shelf checks.',
            ],
            [
                'name' => 'Office Coffee Beans',
                'price' => 1890,
                'stock' => 3,
                'category' => 'Supplies',
                'brand' => 'RoastLab',
                'description' => 'Recurring office supply used in the kitchen and lounge area.',
            ],
            [
                'name' => 'Shipping Labels',
                'price' => 990,
                'stock' => 24,
                'category' => 'Supplies',
                'brand' => 'PaperGrid',
                'description' => 'Thermal label packs for outgoing packages and internal tracking.',
            ],
            [
                'name' => 'Monitor Arm',
                'price' => 7490,
                'stock' => 5,
                'category' => 'Office',
                'brand' => 'ErgoLift',
                'description' => 'Adjustable desk mount used in workstation upgrades.',
            ],
            [
                'name' => 'Team Notebook Pack',
                'price' => 1590,
                'stock' => 18,
                'category' => 'Stationery',
                'brand' => 'WriteLine',
                'description' => 'Weekly-use notebooks for meetings, planning, and warehouse checklists.',
            ],
        ])->map(fn (array $product) => Products::create($product));

        Expense::factory()->count(6)->create(['user_id' => $manager->id, 'type' => 'expense']);
        Expense::factory()->count(4)->create(['user_id' => $manager->id, 'type' => 'income']);
        Expense::factory()->count(4)->create(['user_id' => $assistant->id, 'type' => 'expense']);

        $tasks = collect([
            ToDo::create([
                'task' => 'Review low stock items',
                'status' => 'pending',
                'priority' => 'high',
                'user_id' => $manager->id,
                'is_recurring' => true,
                'recurrence' => 'weekly',
            ]),
            ToDo::create([
                'task' => 'Prepare monthly expense report',
                'status' => 'in_progress',
                'priority' => 'medium',
                'user_id' => $manager->id,
                'is_recurring' => true,
                'recurrence' => 'monthly',
            ]),
            ToDo::create([
                'task' => 'Organize packing station',
                'status' => 'completed',
                'priority' => 'low',
                'user_id' => $assistant->id,
                'is_recurring' => false,
                'recurrence' => null,
            ]),
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'type' => 'low_stock',
            'message' => "Product 'Office Coffee Beans' has low stock: 3 remaining.",
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'seeded',
            'model_type' => Products::class,
            'model_id' => $products->first()->id,
            'old_values' => null,
            'new_values' => ['name' => $products->first()->name],
            'ip_address' => '127.0.0.1',
            'description' => 'Demo inventory records created.',
        ]);

        ActivityLog::create([
            'user_id' => $manager->id,
            'action' => 'seeded',
            'model_type' => ToDo::class,
            'model_id' => $tasks->first()->id,
            'old_values' => null,
            'new_values' => ['task' => $tasks->first()->task],
            'ip_address' => '127.0.0.1',
            'description' => 'Demo task data created.',
        ]);

        ActivityLog::create([
            'user_id' => $assistant->id,
            'action' => 'seeded',
            'model_type' => Expense::class,
            'model_id' => Expense::latest('id')->first()?->id,
            'old_values' => null,
            'new_values' => ['message' => 'Demo finance data created.'],
            'ip_address' => '127.0.0.1',
            'description' => 'Demo expense data created.',
        ]);
    }
}
