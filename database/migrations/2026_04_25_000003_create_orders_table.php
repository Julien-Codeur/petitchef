<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cook_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('delivery_address_id')->constrained('delivery_addresses')->onDelete('cascade');
            $table->date('served_date');
            $table->time('pickup_time'); // Heure souhaitée du client (ex: 12h00)
            $table->string('location_name'); // Copie humanisée: "Entreprise X" ou "Alice - Domicile"
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['reçue', 'en_préparation', 'prête', 'livrée', 'annulée'])->default('reçue');
            $table->enum('payment_method', ['cash', 'ticket_partenaire'])->default('cash');
            $table->text('notes_client')->nullable();
            $table->time('estimated_delivery_time')->nullable(); // Calculé par système
            $table->timestamps();

            $table->index(['cook_id', 'served_date', 'status']);
            $table->index(['client_id', 'served_date']);
            $table->index(['delivery_address_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
