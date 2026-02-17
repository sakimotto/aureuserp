<?php

/**
 * Zervi Asia Mock Company Setup Script
 * This script creates comprehensive mock data for Zervi Asia tent manufacturing
 */

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Zervi\Manufacturing\Models\WorkOrder;
use Zervi\Manufacturing\Models\WorkOrderTask;
use Zervi\Manufacturing\Models\MaterialLine;
use Zervi\Manufacturing\Models\QualityRecord;
use Zervi\Manufacturing\Enums\Department;
use Zervi\Manufacturing\Enums\TaskStatus;
use Zervi\Manufacturing\Enums\Priority;
use Zervi\Manufacturing\Enums\WorkOrderStatus;

// Create Zervi Asia Company
$company = DB::table('companies')->insertGetId([
    'name' => 'Zervi Asia Co., Ltd',
    'code' => 'ZERVI-TH',
    'tax_number' => '1234567890123',
    'phone' => '+66-2-123-4567',
    'email' => 'info@zervi-asia.com',
    'website' => 'https://www.zervi-asia.com',
    'address' => '123 Industrial Park Road, Bang Phli District, Samut Prakan 10540, Thailand',
    'city' => 'Samut Prakan',
    'state' => 'Samut Prakan',
    'country' => 'TH',
    'postal_code' => '10540',
    'currency' => 'THB',
    'timezone' => 'Asia/Bangkok',
    'business_type' => 'Manufacturing',
    'description' => 'Leading tent and outdoor equipment manufacturer in Thailand, specializing in automotive and camping solutions with TIS 1238-2564 quality compliance.',
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✅ Zervi Asia Company created successfully!\n";

// Create major customers (Thai automotive and camping companies)
$customers = [
    [
        'name' => 'Toyota Motor Thailand Co., Ltd',
        'email' => 'procurement@toyota.co.th',
        'phone' => '+66-2-386-0000',
        'address' => '186/1 Moo 1, Old Railway Road, Samrong Tai, Phra Pradaeng, Samut Prakan 10130',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000012',
        'description' => 'Major automotive manufacturer in Thailand requiring high-quality camping tents for employee outdoor activities',
    ],
    [
        'name' => 'Isuzu Motors (Thailand) Co., Ltd',
        'email' => 'parts@isuzu.co.th',
        'phone' => '+66-2-900-9000',
        'address' => '1 Isuzu Avenue, Samrong Nua, Mueang Samut Prakan, Samut Prakan 10270',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000013',
        'description' => 'Leading commercial vehicle manufacturer requiring durable outdoor equipment',
    ],
    [
        'name' => 'Mitsubishi Motors (Thailand) Co., Ltd',
        'email' => 'supply@mitsubishi-motors.co.th',
        'phone' => '+66-2-365-8888',
        'address' => '700 Moo 3, Bangna-Trad Km. 23, Bang Sao Thong, Samut Prakan 10540',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000014',
        'description' => 'Automotive manufacturer with outdoor team building activities requiring premium tents',
    ],
    [
        'name' => 'Central Department Store Co., Ltd',
        'email' => 'outdoor@central.co.th',
        'phone' => '+66-2-793-7777',
        'address' => '306 Silom Road, Bangrak, Bangkok 10500',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000015',
        'description' => 'Major retail chain requiring camping tents for seasonal outdoor sales',
    ],
    [
        'name' => 'Decathlon Thailand',
        'email' => 'procurement@decathlon.co.th',
        'phone' => '+66-2-019-1999',
        'address' => '622 Sukhumvit Road, Khlong Tan, Khlong Toei, Bangkok 10110',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000016',
        'description' => 'International sports retailer requiring high-quality camping equipment',
    ],
];

foreach ($customers as $customer) {
    DB::table('partners_partners')->insert([
        'name' => $customer['name'],
        'email' => $customer['email'],
        'phone' => $customer['phone'],
        'address' => $customer['address'],
        'city' => $customer['city'],
        'country' => $customer['country'],
        'tax_number' => $customer['tax_number'],
        'description' => $customer['description'],
        'is_customer' => true,
        'is_supplier' => false,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo "✅ 5 Major customers created (Toyota, Isuzu, Mitsubishi, Central, Decathlon)!\n";

// Create key suppliers (Thai textile and materials companies)
$suppliers = [
    [
        'name' => 'Thai Toray Textile Mills Co., Ltd',
        'email' => 'sales@toray.co.th',
        'phone' => '+66-2-365-0700',
        'address' => '233 Moo 4, Bangna-Trad Km. 21, Bang Sao Thong, Samut Prakan 10540',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000021',
        'description' => 'Leading manufacturer of high-performance textiles for automotive and outdoor applications',
    ],
    [
        'name' => 'Indorama Ventures Public Company Limited',
        'email' => 'procurement@indorama.com',
        'phone' => '+66-2-661-6661',
        'address' => '75/104 Ocean Tower 2, 17th Floor, Sukhumvit Road, Klongtoey, Bangkok 10110',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000022',
        'description' => 'Global leader in PET and polyester materials for automotive applications',
    ],
    [
        'name' => 'Thai Acrylic Fiber Co., Ltd',
        'email' => 'sales@acrylic.co.th',
        'phone' => '+66-2-661-7070',
        'address' => '75/121 Ocean Tower 2, 20th Floor, Sukhumvit Road, Klongtoey, Bangkok 10110',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000023',
        'description' => 'Premium acrylic fiber manufacturer for outdoor and automotive textiles',
    ],
    [
        'name' => 'Saha-Union Public Company Limited',
        'email' => 'textile@sahaunion.com',
        'phone' => '+66-2-365-8888',
        'address' => '1 Saha-Union Tower, 23rd Floor, 182 Surawong Road, Bangrak, Bangkok 10500',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000024',
        'description' => 'Integrated textile manufacturer with automotive and outdoor fabric expertise',
    ],
    [
        'name' => 'Thai Nam Plastic Public Company Limited',
        'email' => 'sales@tainam.com',
        'phone' => '+66-2-223-0020',
        'address' => '99 Moo 1, Bangna-Trad Km. 36, Bang Sao Thong, Samut Prakan 10540',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000025',
        'description' => 'Specialized in technical textiles and coating materials for outdoor equipment',
    ],
];

foreach ($suppliers as $supplier) {
    DB::table('partners_partners')->insert([
        'name' => $supplier['name'],
        'email' => $supplier['email'],
        'phone' => $supplier['phone'],
        'address' => $supplier['address'],
        'city' => $supplier['city'],
        'country' => $supplier['country'],
        'tax_number' => $supplier['tax_number'],
        'description' => $supplier['description'],
        'is_customer' => false,
        'is_supplier' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo "✅ 5 Major suppliers created (Toray, Indorama, Acrylic, Saha-Union, Thai Nam)!\n";

// Create realistic work orders for tent manufacturing
$workOrders = [
    [
        'work_order_number' => 'WO-2024-001',
        'customer_po_number' => 'PO-TOYOTA-CAMPING-001',
        'customer_name' => 'Toyota Motor Thailand Co., Ltd',
        'promised_delivery_date' => now()->addDays(14),
        'department' => Department::CUTTING,
        'status' => WorkOrderStatus::QUEUED,
        'priority' => Priority::HIGH,
        'notes' => 'Premium camping tent for employee outdoor activities - Toyota quality standards required',
        'quantity_requested' => 50,
        'quantity_completed' => 0,
        'estimated_material_cost' => 125000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_number' => 'WO-2024-002',
        'customer_po_number' => 'PO-ISUZU-OUTDOOR-002',
        'customer_name' => 'Isuzu Motors (Thailand) Co., Ltd',
        'promised_delivery_date' => now()->addDays(10),
        'department' => Department::CUTTING,
        'status' => WorkOrderStatus::QUEUED,
        'priority' => Priority::URGENT,
        'notes' => 'Heavy-duty camping tents for team building events - weather resistant specification',
        'quantity_requested' => 75,
        'quantity_completed' => 0,
        'estimated_material_cost' => 187500.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_number' => 'WO-2024-003',
        'customer_po_number' => 'PO-MITSUBISHI-ADVENTURE-003',
        'customer_name' => 'Mitsubishi Motors (Thailand) Co., Ltd',
        'promised_delivery_date' => now()->addDays(21),
        'department' => Department::CUTTING,
        'status' => WorkOrderStatus::QUEUED,
        'priority' => Priority::NORMAL,
        'notes' => 'Adventure camping tents for dealer network outdoor events - premium materials',
        'quantity_requested' => 100,
        'quantity_completed' => 0,
        'estimated_material_cost' => 250000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_number' => 'WO-2024-004',
        'customer_po_number' => 'PO-CENTRAL-SEASONAL-004',
        'customer_name' => 'Central Department Store Co., Ltd',
        'promised_delivery_date' => now()->addDays(7),
        'department' => Department::CUTTING,
        'status' => WorkOrderStatus::QUEUED,
        'priority' => Priority::HIGH,
        'notes' => 'Seasonal camping tents for retail promotion - fast delivery required for holiday season',
        'quantity_requested' => 200,
        'quantity_completed' => 0,
        'estimated_material_cost' => 400000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_number' => 'WO-2024-005',
        'customer_po_number' => 'PO-DECATHLON-PREMIUM-005',
        'customer_name' => 'Decathlon Thailand',
        'promised_delivery_date' => now()->addDays(28),
        'department' => Department::CUTTING,
        'status' => WorkOrderStatus::QUEUED,
        'priority' => Priority::NORMAL,
        'notes' => 'Premium camping tents for sports retailer - international quality standards',
        'quantity_requested' => 150,
        'quantity_completed' => 0,
        'estimated_material_cost' => 300000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach ($workOrders as $order) {
    $workOrderId = DB::table('zervi_work_orders')->insertGetId($order);
    
    // Create tasks for each work order
    $tasks = [
        [
            'work_order_id' => $workOrderId,
            'name' => 'Cut Waterproof Fabric Panels',
            'department' => Department::CUTTING,
            'status' => TaskStatus::QUEUED,
            'estimated_hours' => 8,
            'sequence' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'work_order_id' => $workOrderId,
            'name' => 'Sew Main Tent Body',
            'department' => Department::SEWING,
            'status' => TaskStatus::QUEUED,
            'estimated_hours' => 12,
            'sequence' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'work_order_id' => $workOrderId,
            'name' => 'Install Zippers and Fasteners',
            'department' => Department::SEWING,
            'status' => TaskStatus::QUEUED,
            'estimated_hours' => 4,
            'sequence' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'work_order_id' => $workOrderId,
            'name' => 'Quality Inspection - TIS 1238-2564',
            'department' => Department::QC,
            'status' => TaskStatus::QUEUED,
            'estimated_hours' => 2,
            'sequence' => 4,
            'requires_qc' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];
    
    foreach ($tasks as $task) {
        DB::table('zervi_work_order_tasks')->insert($task);
    }
    
    // Create material lines with some shortages
    $materials = [
        [
            'work_order_id' => $workOrderId,
            'product_name' => 'Waterproof Nylon Fabric - Blue 420D',
            'required_quantity' => $order['quantity_requested'] * 3.5,
            'available_quantity' => $order['quantity_requested'] * 3.2, // Shortage!
            'unit' => 'meters',
            'unit_cost' => 45.00,
            'expected_restock_date' => now()->addDays(3),
            'has_shortage' => true,
            'shortage_notes' => 'Material shortage due to high demand from automotive sector',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'work_order_id' => $workOrderId,
            'product_name' => 'YKK Waterproof Zippers - 8mm',
            'required_quantity' => $order['quantity_requested'] * 2,
            'available_quantity' => $order['quantity_requested'] * 2.5, // Sufficient
            'unit' => 'pieces',
            'unit_cost' => 85.00,
            'has_shortage' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'work_order_id' => $workOrderId,
            'product_name' => 'Aluminum Tent Poles - 7001 Series',
            'required_quantity' => $order['quantity_requested'] * 4,
            'available_quantity' => $order['quantity_requested'] * 3.8, // Shortage!
            'unit' => 'sets',
            'unit_cost' => 120.00,
            'expected_restock_date' => now()->addDays(5),
            'has_shortage' => true,
            'shortage_notes' => 'Supplier delay from overseas shipment',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];
    
    foreach ($materials as $material) {
        DB::table('zervi_material_lines')->insert($material);
    }
}

echo "✅ 5 Realistic work orders created with tasks and materials!\n";

// Create quality records for TIS 1238-2564 compliance
$qualityRecords = [
    [
        'work_order_id' => 1,
        'task_id' => 4,
        'product_id' => 1,
        'inspected_by' => 1,
        'qc_number' => 'QC-2024-001',
        'inspection_type' => 'incoming',
        'department' => 'qc',
        'result' => 'pass',
        'notes' => 'Waterproof fabric meets TIS 1238-2564 specifications for automotive applications',
        'standard_reference' => 'TIS 1238-2564',
        'checklist_results' => json_encode([
            'water_resistance' => 'pass',
            'tear_strength' => 'pass',
            'uv_protection' => 'pass',
            'color_fastness' => 'pass',
        ]),
        'inspected_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => 2,
        'task_id' => 8,
        'product_id' => 1,
        'inspected_by' => 1,
        'qc_number' => 'QC-2024-002',
        'inspection_type' => 'in_process',
        'department' => 'qc',
        'result' => 'conditional',
        'notes' => 'Minor stitching irregularity detected, requires rework before final approval',
        'standard_reference' => 'TIS 1238-2564',
        'requires_resolution' => true,
        'fail_description' => 'Uneven seam spacing in critical stress areas',
        'disposition' => 'rework',
        'defect_quantity' => 5,
        'inspected_at' => now(),
        'due_date' => now()->addDays(2),
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach ($qualityRecords as $record) {
    DB::table('zervi_quality_records')->insert($record);
}

echo "✅ Quality records created with TIS 1238-2564 compliance!\n";

echo "\n🎉 MOCK COMPANY SETUP COMPLETE! 🎉\n";
echo "=====================================\n";
echo "✅ Zervi Asia Co., Ltd - Thailand\n";
echo "✅ 5 Major Automotive Customers\n";
echo "✅ 5 Premium Textile Suppliers\n";
echo "✅ 5 Realistic Work Orders\n";
echo "✅ 20 Production Tasks\n";
echo "✅ 15 Material Lines with Shortages\n";
echo "✅ TIS 1238-2564 Quality Records\n";
echo "=====================================\n";
echo "🚀 Ready for supervisor testing!\n";