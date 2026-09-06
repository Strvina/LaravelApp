<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('todos')) {
            Schema::table('todos', function (Blueprint $table) {
                if (!Schema::hasColumn('todos', 'last_generated_at')) {
                    $table->date('last_generated_at')->nullable()->after('recurrence');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('todos') && Schema::hasColumn('todos', 'last_generated_at')) {
            Schema::table('todos', function (Blueprint $table) {
                $table->dropColumn('last_generated_at');
            });
        }
    }
};
