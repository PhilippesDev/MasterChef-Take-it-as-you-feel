<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Commandes passees par les clients.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chef_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', OrderStatus::values())->default(OrderStatus::Pending->value);
            $table->decimal('total', 10, 2);
            $table->string('delivery_address');
            $table->text('note')->nullable();
            $table->timestamps();

            // File d'attente de la cuisine : statut puis anciennete.
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
