<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cook_id')->constrained('users')->onDelete('cascade');
            $table->date('served_date');
            $table->time('planned_time')->default('12:00'); // Heure de départ planifiée
            $table->enum('status', ['en_préparation', 'prête_à_partir', 'en_route', 'complète'])->default('en_préparation');
            $table->json('route_order')->nullable(); // JSON: [addr_id_1, addr_id_2, addr_id_3] = ordre optimisé
            $table->integer('estimated_total_duration')->nullable(); // minutes
            $table->timestamps();

            $table->index(['cook_id', 'served_date']);
            $table->unique(['cook_id', 'served_date']); // Une seule livraison par cuisinier par jour
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
