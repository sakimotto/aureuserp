<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zervi_work_order_tasks', function (Blueprint $table) {
            $table->id();
            
            // Core relationships
            $table->foreignId('work_order_id')->constrained('zervi_work_orders')->cascadeOnDelete();
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees');
            
            // Task information
            $table->string('operation_name'); // "Cut Foam Base", "Apply PUR Adhesive"
            $table->enum('department', ['cutting', 'sewing', 'qc']);
            
            // Workflow sequencing
            $table->integer('sequence_order')->default(0);
            $table->foreignId('predecessor_task_id')->nullable()->constrained('zervi_work_order_tasks');
            
            // Time tracking
            $table->decimal('estimated_hours', 8, 2);
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Status workflow
            $table->enum('status', [
                'pending', 'ready', 'in_progress', 'qc_required', 'completed', 'blocked'
            ])->default('pending');
            
            // Blocking and issue tracking (for supervisor visibility)
            $table->boolean('is_blocked')->default(false);
            $table->enum('block_reason', [
                'material_shortage', 'machine_maintenance', 'qc_failure', 'operator_unavailable'
            ])->nullable();
            $table->text('block_notes')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->foreignId('blocked_by')->nullable()->constrained('users');
            
            // Quality requirements
            $table->boolean('requires_qc')->default(false);
            $table->foreignId('qc_record_id')->nullable()->constrained('zervi_quality_records');
            
            // Work instructions and attachments
            $table->text('work_instructions')->nullable();
            $table->json('attachments')->nullable(); // Drawing files, specs
            
            // Machine/equipment tracking
            $table->string('machine_id')->nullable(); // Links to machine inventory
            $table->string('tooling_required')->nullable();
            
            $table->timestamps();
            
            // Indexes for supervisor queries
            $table->index(['work_order_id', 'sequence_order']);
            $table->index(['assigned_employee_id', 'status']);
            $table->index(['department', 'status']);
            $table->index(['is_blocked', 'department']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zervi_work_order_tasks');
    }
};