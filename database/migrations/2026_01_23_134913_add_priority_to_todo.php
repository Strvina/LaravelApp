<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('todos')) {
            Schema::table('todos', function (Blueprint $table) {
                if (!Schema::hasColumn('todos', 'priority')) {
                    $table->enum('priority', ['low', 'medium', 'high'])->default('low');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('todos') && Schema::hasColumn('todos', 'priority')) {
            Schema::table('todos', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }
    }

};
