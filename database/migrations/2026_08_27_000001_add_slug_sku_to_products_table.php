<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('products', 'sku')) {
                $table->string('sku', 64)->nullable()->unique()->after('slug');
            }
        });

        DB::table('products')
            ->whereNull('slug')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->each(function ($product): void {
                $baseSlug = Str::slug($product->name) ?: 'product-'.$product->id;
                $slug = $baseSlug;
                $counter = 2;

                while (DB::table('products')->where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                DB::table('products')->where('id', $product->id)->update(['slug' => $slug]);
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            if (Schema::hasColumn('products', 'sku')) {
                $table->dropUnique(['sku']);
                $table->dropColumn('sku');
            }

            if (Schema::hasColumn('products', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
