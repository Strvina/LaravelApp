<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todo', function (Blueprint $table) {
            $table->date('last_generated_at')->nullable()->after('recurrence');
        });
    }

    public function down(): void
    {
        Schema::table('todo', function (Blueprint $table) {
            $table->dropColumn('last_generated_at');
        });
    }
};
