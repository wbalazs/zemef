<?php
// database/migrations/xxxx_create_commission_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
         Schema::dropIfExists('employee_daily_performance');
        Schema::dropIfExists('business_daily_performance');
        Schema::dropIfExists('employee_commissions');
        Schema::dropIfExists('commission_rules');
        Schema::dropIfExists('commission_campaign_business');
        Schema::dropIfExists('commission_campaigns');
        
        Schema::create('commission_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('campaign_type', ['sales', 'gold_purchase', 'expired_ticket', 'pawn', 'other']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('commission_campaign_business', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commission_campaign_id');
            $table->unsignedBigInteger('business_id');
            $table->timestamps();
            
            $table->foreign('commission_campaign_id', 'fk_campaign_business_campaign')
                  ->references('id')
                  ->on('commission_campaigns')
                  ->onDelete('cascade');
            
            // Rövid unique constraint név
            $table->unique(['commission_campaign_id', 'business_id'], 'uk_campaign_business');
        });

        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commission_campaign_id');
            $table->enum('rule_type', ['basic', 'target']);
            $table->enum('commission_type', ['percentage', 'fixed_amount', 'per_gram', 'difference_percentage']);
            $table->decimal('threshold_min', 15, 2)->nullable();
            $table->decimal('threshold_max', 15, 2)->nullable();
            $table->decimal('commission_value', 10, 2);
            $table->enum('period_type', ['weekly', 'monthly'])->default('monthly');
            $table->integer('period_week')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();
            
            $table->foreign('commission_campaign_id', 'fk_rules_campaign')
                  ->references('id')
                  ->on('commission_campaigns')
                  ->onDelete('cascade');
        });

        Schema::create('employee_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('commission_campaign_id');
            $table->unsignedBigInteger('commission_rule_id')->nullable();
            $table->enum('source_type', ['sales', 'gold_purchase', 'expired_ticket', 'pawn', 'other']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('base_amount', 15, 2);
            $table->decimal('quantity', 15, 2)->nullable();
            $table->date('commission_date');
            $table->enum('status', ['calculated', 'paid'])->default('calculated');
            $table->date('paid_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            
            // Indexek rövid névvel
            $table->index('employee_id', 'idx_emp_comm_employee');
            $table->index('business_id', 'idx_emp_comm_business');
            $table->index('commission_campaign_id', 'idx_emp_comm_campaign');
            
            $table->foreign('commission_campaign_id', 'fk_emp_comm_campaign')
                  ->references('id')
                  ->on('commission_campaigns')
                  ->onDelete('cascade');
            
            $table->foreign('commission_rule_id', 'fk_emp_comm_rule')
                  ->references('id')
                  ->on('commission_rules')
                  ->onDelete('set null');
        });

        Schema::create('business_daily_performance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->date('performance_date');
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_gold_purchase', 15, 2)->default(0);
            $table->decimal('total_expired_tickets', 15, 2)->default(0);
            $table->decimal('total_pawn', 15, 2)->default(0);
            $table->decimal('total_other', 15, 2)->default(0);
            $table->timestamps();
            
            $table->index('business_id', 'idx_biz_perf_business');
            $table->unique(['business_id', 'performance_date'], 'uk_biz_perf_date');
        });

        Schema::create('employee_daily_performance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('business_id');
            $table->date('performance_date');
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_gold_purchase', 15, 2)->default(0);
            $table->decimal('total_expired_tickets', 15, 2)->default(0);
            $table->decimal('total_pawn', 15, 2)->default(0);
            $table->decimal('total_other', 15, 2)->default(0);
            $table->timestamps();
            
            $table->index('employee_id', 'idx_emp_perf_employee');
            $table->index('business_id', 'idx_emp_perf_business');
            // Rövid unique constraint név
            $table->unique(['employee_id', 'business_id', 'performance_date'], 'uk_emp_perf_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_daily_performance');
        Schema::dropIfExists('business_daily_performance');
        Schema::dropIfExists('employee_commissions');
        Schema::dropIfExists('commission_rules');
        Schema::dropIfExists('commission_campaign_business');
        Schema::dropIfExists('commission_campaigns');
    }
};