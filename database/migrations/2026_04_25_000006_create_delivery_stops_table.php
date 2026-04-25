<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->foreignId('delivery_address_id')->constrained('delivery_addresses')->onDelete('restrict');
            $table->string('location_name'); // Copie: "Entreprise X" ou "Alice"
            $table->integer('sequence_order'); // 1, 2, 3 = ordre dans la tournée
            $table->json('orders_ids')->nullable(); // JSON: [order_id_1, order_id_2] = commandes du stop
            $table->enum('status', ['en_attente', 'en_préparation', 'prête', 'livrée'])->default('en_attente');
            $table->time('estimated_arrival_time')->nullable();
            $table->time('actual_arrival_time')->nullable();
            $table->timestamps();

            $table->index(['delivery_id', 'sequence_order']);
            $table->unique(['delivery_id', 'sequence_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_stops');
    }
};
