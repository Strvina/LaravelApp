<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('todos')) {
            Schema::table('todos', function (Blueprint $table) {
                if (! Schema::hasColumn('todos', 'is_recurring')) {
                    $table->boolean('is_recurring')->default(false);
                }
                if (! Schema::hasColumn('todos', 'recurrence')) {
                    $table->enum('recurrence', ['daily', 'weekly', 'monthly'])->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('todos')) {
            Schema::table('todos', function (Blueprint $table) {
                if (Schema::hasColumn('todos', 'is_recurring')) {
                    $table->dropColumn('is_recurring');
                }
                if (Schema::hasColumn('todos', 'recurrence')) {
                    $table->dropColumn('recurrence');
                }
            });
        }
    }
};
