<?php

echo "🏭 Zervi Asia MES - Testing Data Setup\n";
echo "=====================================\n\n";

// Clear existing data
echo "🧹 Clearing existing test data...\n";
DB::table('zervi_quality_records')->truncate();
DB::table('zervi_material_lines')->truncate();
DB::table('zervi_work_order_tasks')->truncate();
DB::table('zervi_work_orders')->truncate();
echo "✅ Existing data cleared\n\n";

// Create comprehensive work orders
echo "📋 Creating comprehensive work orders...\n";

\$workOrders = [
    // URGENT Priority - Isuzu (RED)
    [
        'wo_number' => 'WO-ZERVI-URGENT-001',
        'barcode' => 'BC-URGENT-001',
        'product_id' => 1,
        'quantity_requested' => 75,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'current_department' => 'cutting',
        'status' => 'queued',
        'priority' => 'urgent',
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(7),
        'promised_delivery_date' => now()->addDays(7),
        'customer_po_number' => 'PO-ISUZU-URGENT-001',
        'estimated_material_cost' => 187500.00,
        'actual_material_cost' => 0,
        'labor_cost' => 25000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // HIGH Priority - Toyota (ORANGE)
    [
        'wo_number' => 'WO-ZERVI-HIGH-002',
        'barcode' => 'BC-HIGH-002',
        'product_id' => 2,
        'quantity_requested' => 50,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'current_department' => 'cutting',
        'status' => 'queued',
        'priority' => 'high',
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(14),
        'promised_delivery_date' => now()->addDays(14),
        'customer_po_number' => 'PO-TOYOTA-HIGH-002',
        'estimated_material_cost' => 125000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 18000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // NORMAL Priority - Mitsubishi (GREEN)
    [
        'wo_number' => 'WO-ZERVI-NORMAL-003',
        'barcode' => 'BC-NORMAL-003',
        'product_id' => 3,
        'quantity_requested' => 100,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'current_department' => 'cutting',
        'status' => 'queued',
        'priority' => 'normal',
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(21),
        'promised_delivery_date' => now()->addDays(21),
        'customer_po_number' => 'PO-MITSUBISHI-NORMAL-003',
        'estimated_material_cost' => 250000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 35000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // HIGH Priority - Central Department Store (ORANGE)
    [
        'wo_number' => 'WO-ZERVI-HOLIDAY-004',
        'barcode' => 'BC-HOLIDAY-004',
        'product_id' => 4,
        'quantity_requested' => 200,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'current_department' => 'cutting',
        'status' => 'queued',
        'priority' => 'high',
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(10),
        'promised_delivery_date' => now()->addDays(10),
        'customer_po_number' => 'PO-CENTRAL-HOLIDAY-004',
        'estimated_material_cost' => 400000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 60000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // URGENT Priority - Decathlon (RED)
    [
        'wo_number' => 'WO-ZERVI-PREMIUM-005',
        'barcode' => 'BC-PREMIUM-005',
        'product_id' => 5,
        'quantity_requested' => 150,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'current_department' => 'cutting',
        'status' => 'queued',
        'priority' => 'urgent',
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(28),
        'promised_delivery_date' => now()->addDays(28),
        'customer_po_number' => 'PO-DECATHLON-PREMIUM-005',
        'estimated_material_cost' => 300000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 45000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach (\$workOrders as \$order) {
    DB::table('zervi_work_orders')->insert(\$order);
}
echo "✅ " . count(\$workOrders) . " work orders created with priority levels\n";

// Create production tasks
echo "\n🔧 Creating production tasks...\n";
\$tasks = [
    // Tasks for URGENT order (WO-ZERVI-URGENT-001)
    [
        'work_order_id' => 1,
        'operation_name' => 'Cut Waterproof Fabric Panels',
        'department' => 'cutting',
        'sequence_order' => 1,
        'estimated_hours' => 12,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 1,
        'operation_name' => 'Sew Main Tent Body',
        'department' => 'sewing',
        'sequence_order' => 2,
        'estimated_hours' => 18,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 1,
        'operation_name' => 'Install Zippers and Fasteners',
        'department' => 'sewing',
        'sequence_order' => 3,
        'estimated_hours' => 6,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 1,
        'operation_name' => 'Quality Inspection - TIS 1238-2564',
        'department' => 'qc',
        'sequence_order' => 4,
        'estimated_hours' => 3,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // Tasks for HIGH priority order (WO-ZERVI-HIGH-002)
    [
        'work_order_id' => 2,
        'operation_name' => 'Cut Toyota Specification Fabric',
        'department' => 'cutting',
        'sequence_order' => 1,
        'estimated_hours' => 8,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 2,
        'operation_name' => 'Sew Toyota Quality Tent Body',
        'department' => 'sewing',
        'sequence_order' => 2,
        'estimated_hours' => 12,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 2,
        'operation_name' => 'Install Premium Toyota Hardware',
        'department' => 'sewing',
        'sequence_order' => 3,
        'estimated_hours' => 4,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 2,
        'operation_name' => 'TIS 1238-2564 Quality Inspection',
        'department' => 'qc',
        'sequence_order' => 4,
        'estimated_hours' => 2,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // Tasks for NORMAL priority order (WO-ZERVI-NORMAL-003)
    [
        'work_order_id' => 3,
        'operation_name' => 'Cut Adventure Fabric Panels',
        'department' => 'cutting',
        'sequence_order' => 1,
        'estimated_hours' => 15,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 3,
        'operation_name' => 'Sew Adventure Tent Body',
        'department' => 'sewing',
        'sequence_order' => 2,
        'estimated_hours' => 20,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 3,
        'operation_name' => 'Install Adventure Hardware',
        'department' => 'sewing',
        'sequence_order' => 3,
        'estimated_hours' => 8,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 3,
        'operation_name' => 'Adventure Quality Control',
        'department' => 'qc',
        'sequence_order' => 4,
        'estimated_hours' => 3,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach (\$tasks as \$task) {
    DB::table('zervi_work_order_tasks')->insert(\$task);
}
echo "✅ " . count(\$tasks) . " production tasks created\n";

// Create material lines with shortages
echo "\n📦 Creating material lines with realistic shortages...\n";
\$materialLines = [
    // URGENT order - Major shortages
    [
        'work_order_id' => 1,
        'product_id' => 1,
        'quantity_required' => 262.5, // 75 tents × 3.5m
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 52.5, // 52.5 meters shortage!
        'has_shortage' => true,
        'expected_restock_date' => now()->addDays(3),
        'shortage_notes' => 'CRITICAL SHORTAGE: Isuzu urgent order fabric shortage. Supplier Thai Toray experiencing production delays due to high automotive demand.',
        'shortage_reported_at' => now(),
        'shortage_reported_by' => 1,
        'unit_cost' => 45.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 1,
        'product_id' => 2,
        'quantity_required' => 150, // 75 tents × 2 zippers
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 0,
        'has_shortage' => false,
        'unit_cost' => 85.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 1,
        'product_id' => 3,
        'quantity_required' => 300, // 75 tents × 4 pole sets
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 75, // 75 pole sets shortage!
        'has_shortage' => true,
        'expected_restock_date' => now()->addDays(5),
        'shortage_notes' => 'Aluminum pole shortage due to global supply chain issues. Supplier Saha-Union working on expedited delivery for urgent order.',
        'shortage_reported_at' => now(),
        'shortage_reported_by' => 1,
        'unit_cost' => 120.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // HIGH priority order - Minor shortages
    [
        'work_order_id' => 2,
        'product_id' => 4,
        'quantity_required' => 175, // 50 tents × 3.5m
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 0,
        'has_shortage' => false,
        'unit_cost' => 45.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 2,
        'product_id' => 5,
        'quantity_required' => 100, // 50 tents × 2 zippers
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 0,
        'has_shortage' => false,
        'unit_cost' => 95.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // NORMAL priority order - No shortages
    [
        'work_order_id' => 3,
        'product_id' => 6,
        'quantity_required' => 350, // 100 tents × 3.5m
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 0,
        'has_shortage' => false,
        'unit_cost' => 38.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 3,
        'product_id' => 7,
        'quantity_required' => 200, // 100 tents × 2 zippers
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 0,
        'has_shortage' => false,
        'unit_cost' => 85.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach (\$materialLines as \$material) {
    DB::table('zervi_material_lines')->insert(\$material);
}
echo "✅ " . count(\$materialLines) . " material lines created with shortages\n";

// Create quality records
echo "\n🔍 Creating TIS 1238-2564 quality compliance records...\n";
\$qualityRecords = [
    [
        'work_order_id' => 1,
        'task_id' => 4,
        'product_id' => 1,
        'inspected_by' => 1,
        'qc_number' => 'QC-2024-URGENT-001',
        'inspection_type' => 'incoming',
        'department' => 'qc',
        'result' => 'fail',
        'notes' => 'Incoming inspection failed for Isuzu urgent order fabric. Waterproof coating below specification for heavy-duty automotive application.',
        'standard_reference' => 'TIS 1238-2564',
        'requires_resolution' => true,
        'fail_description' => 'Waterproof coating below TIS 1238-2564 specification requirements for heavy-duty automotive use',
        'disposition' => 'reject',
        'defect_quantity' => 75,
        'inspected_at' => now(),
        'due_date' => now()->addDays(1),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 2,
        'task_id' => 8,
        'product_id' => 4,
        'inspected_by' => 1,
        'qc_number' => 'QC-2024-TOYOTA-001',
        'inspection_type' => 'incoming',
        'department' => 'qc',
        'result' => 'pass',
        'notes' => 'Incoming inspection of Toyota specification orange fabric. All TIS 1238-2564 requirements met for automotive applications.',
        'standard_reference' => 'TIS 1238-2564',
        'checklist_results' => json_encode([
            'water_resistance' => 'pass',
            'tear_strength' => 'pass',
            'color_fastness' => 'pass',
            'dimensional_stability' => 'pass',
            'uv_resistance' => 'pass',
        ]),
        'inspected_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 2,
        'task_id' => 8,
        'product_id' => 5,
        'inspected_by' => 1,
        'qc_number' => 'QC-2024-POLES-001',
        'inspection_type' => 'in_process',
        'department' => 'qc',
        'result' => 'conditional',
        'notes' => 'In-process inspection found minor surface imperfections on 3 aluminum pole sets. Requires rework before final approval.',
        'standard_reference' => 'TIS 1238-2564',
        'requires_resolution' => true,
        'fail_description' => 'Minor surface scratches and slight color variation on aluminum poles',
        'disposition' => 'rework',
        'defect_quantity' => 3,
        'inspected_at' => now(),
        'due_date' => now()->addDays(1),
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach (\$qualityRecords as \$record) {
    DB::table('zervi_quality_records')->insert(\$record);
}
echo "✅ " . count(\$qualityRecords) . " quality records created with TIS compliance\n";

// Final summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎉 ZERVI ASIA MES - COMPREHENSIVE TESTING DATA COMPLETE!\n";
echo str_repeat("=", 60) . "\n\n";

// Statistics
echo "📊 FINAL STATISTICS:\n";
echo "✅ Work Orders: " . count(\$workOrders) . " with priority levels\n";
echo "✅ Production Tasks: " . count(\$tasks) . " detailed tasks\n";
echo "✅ Material Lines: " . count(\$materialLines) . " with realistic shortages\n";
echo "✅ Quality Records: " . count(\$qualityRecords) . " TIS compliance records\n\n";

// Priority breakdown
echo "🎯 PRIORITY BREAKDOWN:\n";
\$urgent = DB::table('zervi_work_orders')->where('priority', 'urgent')->count();
\$high = DB::table('zervi_work_orders')->where('priority', 'high')->count();
\$normal = DB::table('zervi_work_orders')->where('priority', 'normal')->count();
echo "🔴 URGENT: " . \$urgent . " orders (Isuzu, Decathlon)\n";
echo "🟠 HIGH: " . \$high . " orders (Toyota, Central)\n";
echo "🟢 NORMAL: " . \$normal . " orders (Mitsubishi)\n\n";

// Shortage summary
echo "📦 SHORTAGE SUMMARY:\n";
\$shortageCount = DB::table('zervi_material_lines')->where('has_shortage', true)->count();
\$totalShortages = DB::table('zervi_material_lines')->sum('quantity_shortage');
echo "🔴 Active Shortages: " . \$shortageCount . " material lines\n";
echo "📏 Total Shortage Quantity: " . number_format(\$totalShortages, 1) . " units\n\n";

// Department workload
echo "⚙️ DEPARTMENT WORKLOAD:\n";
\$cuttingTasks = DB::table('zervi_work_order_tasks')->where('department', 'cutting')->count();
\$sewingTasks = DB::table('zervi_work_order_tasks')->where('department', 'sewing')->count();
\$qcTasks = DB::table('zervi_work_order_tasks')->where('department', 'qc')->count();
echo "✂️ CUTTING: " . \$cuttingTasks . " tasks\n";
echo "🧵 SEWING: " . \$sewingTasks . " tasks\n";
echo "🔍 QC: " . \$qcTasks . " tasks\n\n";

echo "🚀 READY FOR SUPERVISOR TESTING!\n";
echo "🌐 Access: http://localhost:8000/admin\n";
echo "📧 Login: admin@zervi.com / admin123\n";
echo "📋 Test: Priority levels, shortages, Kanban workflow, TIS compliance\n\n";

echo "🏭 Happy Manufacturing! 🏭✨\n";