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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->decimal('gross_amount', 15, 2);
            $table->string('payment_type')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'challenge'])->default('pending');
            $table->enum('transaction_status', ['pending', 'capture', 'settlement', 'deny', 'cancel', 'expire', 'failure'])->default('pending');
            $table->string('fraud_status')->nullable();
            $table->string('status_code')->nullable();
            $table->string('status_message')->nullable();
            $table->string('signature_key')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->timestamps();
            
            $table->index(['transaction_id', 'transaction_status']);
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};