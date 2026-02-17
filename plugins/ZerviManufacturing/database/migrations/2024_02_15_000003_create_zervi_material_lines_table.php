<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zervi_material_lines', function (Blueprint $table) {
            $table->id();
            
            // Core relationships
            $table->foreignId('work_order_id')->constrained('zervi_work_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products'); // Raw material from Aureus inventory
            
            // Quantity tracking with shortage visibility
            $table->decimal('quantity_required', 10, 3);
            $table->decimal('quantity_reserved', 10, 3)->default(0);
            $table->decimal('quantity_issued', 10, 3)->default(0);
            $table->decimal('quantity_consumed', 10, 3)->default(0);
            $table->decimal('quantity_returned', 10, 3)->default(0);
            $table->decimal('quantity_shortage', 10, 3)->default(0); // For supervisor visibility
            
            // Shortage tracking (critical for supervisors)
            $table->boolean('has_shortage')->default(false);
            $table->date('expected_restock_date')->nullable();
            $table->text('shortage_notes')->nullable();
            $table->timestamp('shortage_reported_at')->nullable();
            $table->foreignId('shortage_reported_by')->nullable()->constrained('users');
            
            // Batch/Lot tracking
            $table->string('batch_number')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('supplier_lot')->nullable();
            
            // Source location (from Aureus inventory)
            $table->foreignId('location_id')->nullable()->constrained('inventories_locations');
            $table->string('bin_location')->nullable(); // Specific bin/shelf
            
            // Cost tracking
            $table->decimal('unit_cost', 10, 4);
            $table->decimal('total_cost', 12, 2)->storedAs('quantity_consumed * unit_cost');
            
            // Status tracking
            $table->enum('status', [
                'planned', 'reserved', 'picked', 'issued', 'consumed', 'returned', 'shortage'
            ])->default('planned');
            
            // Issuance tracking
            $table->foreignId('picked_by')->nullable()->constrained('employees');
            $table->timestamp('picked_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('employees');
            $table->timestamp('issued_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for supervisor queries
            $table->index(['work_order_id', 'status']);
            $table->index(['has_shortage', 'work_order_id']);
            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zervi_material_lines');
    }
};