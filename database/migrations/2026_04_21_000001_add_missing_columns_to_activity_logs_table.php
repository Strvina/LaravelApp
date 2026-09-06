<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('action')->after('user_id');
            $table->string('model_type')->nullable()->after('action');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            $table->json('old_values')->nullable()->after('model_id');
            $table->json('new_values')->nullable()->after('old_values');
            $table->string('ip_address', 45)->nullable()->after('new_values');
            $table->text('description')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'action',
                'model_type',
                'model_id',
                'old_values',
                'new_values',
                'ip_address',
                'description',
            ]);
        });
    }
};
