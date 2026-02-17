<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zervi_work_orders', function (Blueprint $table) {
            $table->id();
            
            // Core work order information
            $table->string('wo_number')->unique(); // ZERVI-WO-2024-00001
            $table->string('barcode')->unique();
            
            // Product and quantity tracking
            $table->unsignedBigInteger('product_id')->nullable(); // Will be linked later
            $table->integer('quantity_requested');
            $table->integer('quantity_completed')->default(0);
            $table->integer('quantity_scrap')->default(0);
            
            // Department workflow (Kanban columns)
            $table->enum('current_department', [
                'queued', 'cutting', 'sewing', 'qc', 'complete'
            ])->default('queued');
            
            // Status workflow
            $table->enum('status', [
                'draft', 'queued', 'in_progress', 'qc_hold', 'completed', 'cancelled'
            ])->default('draft');
            
            // Priority for supervisor visibility
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Scheduling
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->timestamp('actual_start')->nullable();
            $table->timestamp('actual_end')->nullable();
            
            // Customer commitment tracking (for supervisor context)
            $table->unsignedBigInteger('sales_order_line_id')->nullable(); // Link to Aureus sales
            $table->date('promised_delivery_date')->nullable();
            $table->string('customer_po_number')->nullable();
            
            // Department assignment for supervisor scoping
            $table->unsignedBigInteger('owning_department_id')->nullable(); // Will be linked later
            $table->unsignedBigInteger('assigned_to')->nullable(); // Supervisor - will be linked later
            
            // Cost tracking
            $table->decimal('estimated_material_cost', 12, 2)->default(0);
            $table->decimal('actual_material_cost', 12, 2)->default(0);
            $table->decimal('labor_cost', 12, 2)->default(0);
            
            // Batch/Lot tracking
            $table->string('batch_number')->nullable();
            $table->string('lot_number')->nullable();
            
            // Tracking and audit
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for supervisor queries
            $table->index(['current_department', 'status']);
            $table->index(['promised_delivery_date', 'priority']);
            $table->index(['owning_department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zervi_work_orders');
    }
};