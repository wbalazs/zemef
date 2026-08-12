<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billingo_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billingo_id')->unique();
            $table->string('bill_number')->nullable()->index();
            $table->enum('type', ['outgoing', 'incoming', 'draft', 'proforma', 'advance'])->default('outgoing');
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->string('partner_name');
            $table->decimal('net_total', 15, 2)->default(0);
            $table->decimal('gross_total', 15, 2)->default(0);
            $table->string('currency', 3)->default('HUF');
            $table->date('fulfillment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('payment_status', ['not_paid', 'partially_paid', 'paid', 'overdue'])->default('not_paid');
            $table->string('payment_method')->nullable();
            $table->boolean('electronic')->default(false);
            $table->boolean('paid')->default(false);
            $table->date('paid_date')->nullable();
            $table->text('comment')->nullable();
            $table->json('billingo_data')->nullable(); // Full Billingo API response
            $table->json('additional_data')->nullable(); // Additional custom data
            $table->string('pdf_path')->nullable(); // Local PDF storage path
            $table->string('public_url')->nullable(); // Billingo public URL
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['type', 'fulfillment_date']);
            $table->index(['partner_id', 'payment_status']);
            $table->index(['due_date', 'payment_status']);
        });
        
        // Create bill items table
        Schema::create('billingo_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billingo_bill_id')->constrained('billingo_bills')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->string('unit_price_type')->default('net');
            $table->decimal('quantity', 10, 3);
            $table->string('unit')->default('db');
            $table->string('vat')->default('27%');
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('net_total', 15, 2)->default(0);
            $table->decimal('gross_total', 15, 2)->default(0);
            $table->json('billingo_item_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billingo_bill_items');
        Schema::dropIfExists('billingo_bills');
    }
};