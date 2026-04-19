<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('reported_user_id')->nullable();
            $table->unsignedBigInteger('reported_dish_id')->nullable();
            $table->unsignedBigInteger('reported_order_id')->nullable();
            $table->enum('type', ['cook', 'client', 'dish', 'order', 'platform'])->default('cook');
            $table->enum('category', [
                'quality', 'hygiene', 'behavior', 'abusive', 'fraud', 'late', 
                'quality_issue', 'missing_items', 'expired', 'other'
            ]);
            $table->text('description');
            $table->enum('status', ['pending', 'investigating', 'resolved', 'rejected', 'action_taken'])->default('pending');
            $table->text('admin_comment')->nullable();
            $table->integer('priority')->default(1); // 1: low, 2: medium, 3: high, 4: critical
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable(); // admin who resolved
            $table->timestamps();

            // Indexes
            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reported_dish_id')->references('id')->on('dishes')->onDelete('cascade');
            $table->foreign('reported_order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('reporter_id');
            $table->index('reported_user_id');
            $table->index('status');
            $table->index('type');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
