<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('purchase_date');
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['PENDIENTE', 'PAGADA', 'ANULADA'])->default('PENDIENTE');
            $table->enum('payment_method', ['EFECTIVO', 'TRANSFERENCIA', 'TARJETA', 'OTROS'])->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'purchase_date']);
            $table->index(['user_id', 'purchase_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
