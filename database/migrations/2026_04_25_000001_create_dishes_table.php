<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cook_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->integer('initial_qty');
            $table->integer('available_qty');
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('served_date'); // Le jour où le plat est servi
            $table->integer('critical_threshold')->default(1); // Seuil d'alerte critique
            $table->boolean('is_critical_notified')->default(false); // Évite spam notifications
            $table->timestamps();

            $table->index(['cook_id', 'served_date', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
