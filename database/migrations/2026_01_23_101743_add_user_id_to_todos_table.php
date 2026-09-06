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
                if (!Schema::hasColumn('todos', 'user_id')) {
                    $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('todos') && Schema::hasColumn('todos', 'user_id')) {
            Schema::table('todos', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
