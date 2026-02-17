<?php

namespace Zervi\Manufacturing\Database\Seeders;

use Illuminate\Database\Seeder;
use Zervi\Manufacturing\Models\WorkOrder;
use Zervi\Manufacturing\Models\WorkOrderTask;
use Zervi\Manufacturing\Models\MaterialLine;
use Zervi\Manufacturing\Models\QualityRecord;

class ManufacturingTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create test work orders with different statuses
        $workOrders = [
            [
                'wo_number' => 'WO-2024-001',
                'barcode' => 'ZRV2024001',
                'product_id' => 1, // Will need to be updated based on actual products
                'quantity_requested' => 50,
                'quantity_completed' => 0,
                'current_department' => 'queued',
                'status' => 'queued',
                'priority' => 'high',
                'planned_start_date' => now()->addDays(1),
                'planned_end_date' => now()->addDays(5),
                'promised_delivery_date' => now()->addDays(7),
                'customer_po_number' => 'TOYOTA-PO-240215',
                'created_by' => 1,
            ],
            [
                'wo_number' => 'WO-2024-002',
                'barcode' => 'ZRV2024002',
                'product_id' => 2,
                'quantity_requested' => 30,
                'quantity_completed' => 15,
                'current_department' => 'cutting',
                'status' => 'in_progress',
                'priority' => 'normal',
                'planned_start_date' => now()->subDays(2),
                'planned_end_date' => now()->addDays(2),
                'promised_delivery_date' => now()->addDays(3),
                'customer_po_number' => 'HILUX-ORDER-089',
                'created_by' => 1,
            ],
            [
                'wo_number' => 'WO-2024-003',
                'barcode' => 'ZRV2024003',
                'product_id' => 3,
                'quantity_requested' => 100,
                'quantity_completed' => 100,
                'current_department' => 'complete',
                'status' => 'completed',
                'priority' => 'urgent',
                'planned_start_date' => now()->subDays(5),
                'planned_end_date' => now()->subDays(1),
                'promised_delivery_date' => now()->subDays(1), // Overdue!
                'customer_po_number' => 'CAMPING-LOT-089',
                'created_by' => 1,
            ],
        ];

        foreach ($workOrders as $orderData) {
            $workOrder = WorkOrder::create($orderData);
            
            // Add some tasks
            $this->createTasks($workOrder);
            
            // Add some material lines
            $this->createMaterialLines($workOrder);
        }
    }

    private function createTasks(WorkOrder $workOrder): void
    {
        $tasks = [
            [
                'operation_name' => 'Cut Foam Base',
                'department' => 'cutting',
                'sequence_order' => 1,
                'estimated_hours' => 2.5,
                'status' => 'completed',
                'requires_qc' => true,
            ],
            [
                'operation_name' => 'Apply PUR Adhesive',
                'department' => 'cutting',
                'sequence_order' => 2,
                'estimated_hours' => 1.5,
                'status' => 'in_progress',
                'requires_qc' => false,
            ],
            [
                'operation_name' => 'Sew Canvas Panels',
                'department' => 'sewing',
                'sequence_order' => 3,
                'estimated_hours' => 4.0,
                'status' => 'pending',
                'requires_qc' => true,
            ],
            [
                'operation_name' => 'Final Quality Check',
                'department' => 'qc',
                'sequence_order' => 4,
                'estimated_hours' => 0.5,
                'status' => 'pending',
                'requires_qc' => true,
            ],
        ];

        foreach ($tasks as $taskData) {
            WorkOrderTask::create(array_merge($taskData, [
                'work_order_id' => $workOrder->id,
            ]));
        }
    }

    private function createMaterialLines(WorkOrder $workOrder): void
    {
        $materials = [
            [
                'product_id' => 10, // Canvas fabric
                'quantity_required' => 50.5,
                'unit_cost' => 25.00,
                'status' => 'picked',
            ],
            [
                'product_id' => 11, // Foam padding
                'quantity_required' => 25.2,
                'unit_cost' => 15.00,
                'status' => 'issued',
                'has_shortage' => true,
                'quantity_shortage' => 5.0,
                'expected_restock_date' => now()->addDays(2),
            ],
            [
                'product_id' => 12, // Thread
                'quantity_required' => 2.0,
                'unit_cost' => 8.50,
                'status' => 'planned',
            ],
        ];

        foreach ($materials as $materialData) {
            MaterialLine::create(array_merge($materialData, [
                'work_order_id' => $workOrder->id,
            ]));
        }
    }
}