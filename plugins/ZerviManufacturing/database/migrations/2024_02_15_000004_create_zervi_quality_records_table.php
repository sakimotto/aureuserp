<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zervi_quality_records', function (Blueprint $table) {
            $table->id();
            
            // Core relationships
            $table->foreignId('work_order_id')->constrained('zervi_work_orders');
            $table->foreignId('task_id')->nullable()->constrained('zervi_work_order_tasks');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('inspected_by')->constrained('employees');
            
            // Inspection details
            $table->string('qc_number')->unique(); // QC-2024-00001
            $table->enum('inspection_type', ['incoming', 'in_process', 'final', 'laboratory']);
            $table->enum('department', ['cutting', 'sewing', 'final_qc', 'lab']);
            
            // Results with resolution tracking
            $table->enum('result', ['pass', 'fail', 'conditional', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('measurements')->nullable(); // Specific test data
            
            // Fail resolution tracking (critical for supervisors)
            $table->boolean('requires_resolution')->default(false);
            $table->text('fail_description')->nullable();
            $table->enum('disposition', [
                'use_as_is', 'rework', 'scrap', 'return_to_vendor'
            ])->nullable();
            $table->integer('defect_quantity')->default(0);
            
            // Resolution tracking
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->text('resolution_notes')->nullable();
            $table->enum('resolution_action', [
                'rework_completed', 'scrap_approved', 'use_as_is_approved', 'returned_to_vendor'
            ])->nullable();
            
            // Standards compliance (TIS 1238-2564)
            $table->string('standard_reference')->nullable(); // "TIS 1238-2564"
            $table->json('checklist_results')->nullable(); // Array of check items
            
            // NCR (Non-Conformance Report) tracking
            $table->string('ncr_number')->nullable();
            $table->enum('ncr_status', ['open', 'in_progress', 'closed'])->default('open');
            
            // Inspector and timing
            $table->timestamp('inspected_at')->nullable();
            $table->timestamp('due_date')->nullable(); // When resolution is due
            
            $table->timestamps();
            
            // Indexes for supervisor queries
            $table->index(['work_order_id', 'result']);
            $table->index(['department', 'result']);
            $table->index(['requires_resolution', 'department']);
            $table->index(['due_date', 'requires_resolution']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zervi_quality_records');
    }
};