<?php

/**
 * Zervi Asia MES - Comprehensive Testing Data Setup
 * This script populates the system with complete manufacturing data for testing
 */

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

echo "🏭 Zervi Asia MES - Comprehensive Testing Data Setup\n";
echo "====================================================\n\n";

// Clear existing data first (optional for clean setup)
echo "🧹 Clearing existing test data...\n";
DB::table('zervi_quality_records')->truncate();
DB::table('zervi_material_lines')->truncate();
DB::table('zervi_work_order_tasks')->truncate();
DB::table('zervi_work_orders')->truncate();
echo "✅ Existing data cleared\n\n";

// =============================================================================
// COMPANY SETUP - Zervi Asia Co., Ltd
// =============================================================================

echo "🏢 Setting up Zervi Asia Company...\n";
\$company = DB::table('companies')->where('name', 'Zervi Asia Co., Ltd')->first();
if (!\$company) {
    \$companyId = DB::table('companies')->insertGetId([
        'name' => 'Zervi Asia Co., Ltd',
        'company_id' => 'ZERVI-TH-001',
        'tax_id' => '1234567890123',
        'registration_number' => 'TH-2024-001',
        'email' => 'info@zervi-asia.com',
        'phone' => '+66-2-123-4567',
        'website' => 'https://www.zervi-asia.com',
        'street1' => '123 Industrial Park Road',
        'street2' => 'Bang Phli District',
        'city' => 'Samut Prakan',
        'state' => 'Samut Prakan',
        'country_id' => 1,
        'postal_code' => '10540',
        'currency' => 'THB',
        'timezone' => 'Asia/Bangkok',
        'business_type' => 'Manufacturing',
        'description' => 'Leading tent and outdoor equipment manufacturer in Thailand, specializing in automotive and camping solutions with TIS 1238-2564 quality compliance.',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ Zervi Asia Company created\n";
} else {
    \$companyId = \$company->id;
    echo "✅ Zervi Asia Company already exists\n";
}

// =============================================================================
// CUSTOMER SETUP - Major Thai Automotive & Retail Companies
// =============================================================================

echo "👥 Setting up major Thai customers...\n";
\$customers = [
    [
        'name' => 'Toyota Motor Thailand Co., Ltd',
        'email' => 'procurement@toyota.co.th',
        'phone' => '+66-2-386-0000',
        'address' => '186/1 Moo 1, Old Railway Road, Samrong Tai, Phra Pradaeng, Samut Prakan 10130',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000012',
        'description' => 'Major automotive manufacturer requiring premium camping tents for employee outdoor activities and corporate events',
        'industry' => 'Automotive',
        'credit_limit' => 5000000,
        'payment_terms' => 'NET30',
        'is_customer' => true,
        'is_supplier' => false,
    ],
    [
        'name' => 'Isuzu Motors (Thailand) Co., Ltd',
        'email' => 'parts@isuzu.co.th',
        'phone' => '+66-2-900-9000',
        'address' => '1 Isuzu Avenue, Samrong Nua, Mueang Samut Prakan, Samut Prakan 10270',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000013',
        'description' => 'Leading commercial vehicle manufacturer requiring heavy-duty camping tents for team building and dealer events',
        'industry' => 'Automotive',
        'credit_limit' => 4500000,
        'payment_terms' => 'NET30',
        'is_customer' => true,
        'is_supplier' => false,
    ],
    [
        'name' => 'Mitsubishi Motors (Thailand) Co., Ltd',
        'email' => 'supply@mitsubishi-motors.co.th',
        'phone' => '+66-2-365-8888',
        'address' => '700 Moo 3, Bangna-Trad Km. 23, Bang Sao Thong, Samut Prakan 10540',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000014',
        'description' => 'Automotive manufacturer with outdoor team building activities requiring premium adventure camping tents',
        'industry' => 'Automotive',
        'credit_limit' => 4000000,
        'payment_terms' => 'NET30',
        'is_customer' => true,
        'is_supplier' => false,
    ],
    [
        'name' => 'Central Department Store Co., Ltd',
        'email' => 'outdoor@central.co.th',
        'phone' => '+66-2-793-7777',
        'address' => '306 Silom Road, Bangrak, Bangkok 10500',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000015',
        'description' => 'Major retail chain requiring seasonal camping tents for holiday promotions and outdoor sales events',
        'industry' => 'Retail',
        'credit_limit' => 8000000,
        'payment_terms' => 'NET15',
        'is_customer' => true,
        'is_supplier' => false,
    ],
    [
        'name' => 'Decathlon Thailand',
        'email' => 'procurement@decathlon.co.th',
        'phone' => '+66-2-019-1999',
        'address' => '622 Sukhumvit Road, Khlong Tan, Khlong Toei, Bangkok 10110',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000016',
        'description' => 'International sports retailer requiring high-quality camping equipment with global quality standards',
        'industry' => 'Retail',
        'credit_limit' => 6000000,
        'payment_terms' => 'NET30',
        'is_customer' => true,
        'is_supplier' => false,
    ],
    [
        'name' => 'Honda Automobile (Thailand) Co., Ltd',
        'email' => 'purchasing@honda.co.th',
        'phone' => '+66-2-700-7000',
        'address' => '50 Moo 9, Navanakorn Industrial Estate, Klong Luang, Pathum Thani 12120',
        'city' => 'Pathum Thani',
        'country' => 'TH',
        'tax_number' => '0105530000017',
        'description' => 'Automotive manufacturer requiring family camping tents for employee family day events',
        'industry' => 'Automotive',
        'credit_limit' => 5500000,
        'payment_terms' => 'NET30',
        'is_customer' => true,
        'is_supplier' => false,
    ],
    [
        'name' => 'Nissan Motor Thailand Co., Ltd',
        'email' => 'supply-chain@nissan.co.th',
        'phone' => '+66-2-900-9999',
        'address' => '201 Moo 1, Eastern Seaboard Industrial Estate, Rayong 21140',
        'city' => 'Rayong',
        'country' => 'TH',
        'tax_number' => '0105530000018',
        'description' => 'Automotive manufacturer requiring corporate camping tents for dealer network events',
        'industry' => 'Automotive',
        'credit_limit' => 4800000,
        'payment_terms' => 'NET30',
        'is_customer' => true,
        'is_supplier' => false,
    ],
];

\$customerIds = [];
foreach (\$customers as \$customer) {
    \$customerId = DB::table('partners_partners')->insertGetId([
        'name' => \$customer['name'],
        'email' => \$customer['email'],
        'phone' => \$customer['phone'],
        'address' => \$customer['address'],
        'city' => \$customer['city'],
        'country' => \$customer['country'],
        'tax_number' => \$customer['tax_number'],
        'description' => \$customer['description'],
        'industry' => \$customer['industry'],
        'credit_limit' => \$customer['credit_limit'],
        'payment_terms' => \$customer['payment_terms'],
        'is_customer' => \$customer['is_customer'],
        'is_supplier' => \$customer['is_supplier'],
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    \$customerIds[] = \$customerId;
    echo "✅ Customer: " . \$customer['name'] . "\n";
}

// =============================================================================
// SUPPLIER SETUP - Premium Textile & Materials Companies
// =============================================================================

echo "\n🏗️ Setting up premium suppliers...\n";
\$suppliers = [
    [
        'name' => 'Thai Toray Textile Mills Co., Ltd',
        'email' => 'sales@toray.co.th',
        'phone' => '+66-2-365-0700',
        'address' => '233 Moo 4, Bangna-Trad Km. 21, Bang Sao Thong, Samut Prakan 10540',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000021',
        'description' => 'Leading manufacturer of high-performance textiles for automotive and outdoor applications with TIS certification',
        'industry' => 'Textile Manufacturing',
        'credit_limit' => 3000000,
        'payment_terms' => 'NET30',
        'is_customer' => false,
        'is_supplier' => true,
    ],
    [
        'name' => 'Indorama Ventures Public Company Limited',
        'email' => 'procurement@indorama.com',
        'phone' => '+66-2-661-6661',
        'address' => '75/104 Ocean Tower 2, 17th Floor, Sukhumvit Road, Klongtoey, Bangkok 10110',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000022',
        'description' => 'Global leader in PET and polyester materials for automotive applications with international quality standards',
        'industry' => 'Chemical Manufacturing',
        'credit_limit' => 5000000,
        'payment_terms' => 'NET30',
        'is_customer' => false,
        'is_supplier' => true,
    ],
    [
        'name' => 'Thai Acrylic Fiber Co., Ltd',
        'email' => 'sales@acrylic.co.th',
        'phone' => '+66-2-661-7070',
        'address' => '75/121 Ocean Tower 2, 20th Floor, Sukhumvit Road, Klongtoey, Bangkok 10110',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000023',
        'description' => 'Premium acrylic fiber manufacturer for outdoor and automotive textiles with advanced technology',
        'industry' => 'Fiber Manufacturing',
        'credit_limit' => 2500000,
        'payment_terms' => 'NET30',
        'is_customer' => false,
        'is_supplier' => true,
    ],
    [
        'name' => 'Saha-Union Public Company Limited',
        'email' => 'textile@sahaunion.com',
        'phone' => '+66-2-365-8888',
        'address' => '1 Saha-Union Tower, 23rd Floor, 182 Surawong Road, Bangrak, Bangkok 10500',
        'city' => 'Bangkok',
        'country' => 'TH',
        'tax_number' => '0105530000024',
        'description' => 'Integrated textile manufacturer with automotive and outdoor fabric expertise and sustainability focus',
        'industry' => 'Textile Manufacturing',
        'credit_limit' => 3500000,
        'payment_terms' => 'NET30',
        'is_customer' => false,
        'is_supplier' => true,
    ],
    [
        'name' => 'Thai Nam Plastic Public Company Limited',
        'email' => 'sales@tainam.com',
        'phone' => '+66-2-223-0020',
        'address' => '99 Moo 1, Bangna-Trad Km. 36, Bang Sao Thong, Samut Prakan 10540',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000025',
        'description' => 'Specialized in technical textiles and coating materials for outdoor equipment with waterproof technology',
        'industry' => 'Technical Textiles',
        'credit_limit' => 2000000,
        'payment_terms' => 'NET30',
        'is_customer' => false,
        'is_supplier' => true,
    ],
    [
        'name' => 'YKK Thailand Co., Ltd',
        'email' => 'industrial@ykk.co.th',
        'phone' => '+66-2-365-0800',
        'address' => '500 Moo 4, Bangna-Trad Km. 23, Bang Sao Thong, Samut Prakan 10540',
        'city' => 'Samut Prakan',
        'country' => 'TH',
        'tax_number' => '0105530000026',
        'description' => 'Premium zipper and fastening solutions for outdoor equipment with international quality certification',
        'industry' => 'Manufacturing',
        'credit_limit' => 1800000,
        'payment_terms' => 'NET30',
        'is_customer' => false,
        'is_supplier' => true,
    ],
];

\$supplierIds = [];
foreach (\$suppliers as \$supplier) {
    \$supplierId = DB::table('partners_partners')->insertGetId([
        'name' => \$supplier['name'],
        'email' => \$supplier['email'],
        'phone' => \$supplier['phone'],
        'address' => \$supplier['address'],
        'city' => \$supplier['city'],
        'country' => \$supplier['country'],
        'tax_number' => \$supplier['tax_number'],
        'description' => \$supplier['description'],
        'industry' => \$supplier['industry'],
        'credit_limit' => \$supplier['credit_limit'],
        'payment_terms' => \$supplier['payment_terms'],
        'is_customer' => \$supplier['is_customer'],
        'is_supplier' => \$supplier['is_supplier'],
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    \$supplierIds[] = \$supplierId;
    echo "✅ Supplier: " . \$supplier['name'] . "\n";
}

// =============================================================================
// PRODUCT SETUP - Tent Manufacturing Materials
// =============================================================================

echo "\n📦 Setting up tent manufacturing materials...\n";
\$products = [
    // Fabrics
    ['name' => 'Waterproof Nylon Fabric 420D - Blue', 'sku' => 'FAB-NYL-420D-BLUE', 'category' => 'Fabric', 'unit' => 'meter', 'cost' => 45.00, 'supplier_id' => \$supplierIds[0]],
    ['name' => 'Waterproof Nylon Fabric 420D - Green', 'sku' => 'FAB-NYL-420D-GREEN', 'category' => 'Fabric', 'unit' => 'meter', 'cost' => 45.00, 'supplier_id' => \$supplierIds[0]],
    ['name' => 'Waterproof Nylon Fabric 420D - Orange', 'sku' => 'FAB-NYL-420D-ORANGE', 'category' => 'Fabric', 'unit' => 'meter', 'cost' => 45.00, 'supplier_id' => \$supplierIds[0]],
    ['name' => 'Ripstop Polyester Fabric 210D - Gray', 'sku' => 'FAB-POLY-210D-GRAY', 'category' => 'Fabric', 'unit' => 'meter', 'cost' => 38.00, 'supplier_id' => \$supplierIds[1]],
    ['name' => 'Canvas Fabric 600D - Beige', 'sku' => 'FAB-CANVAS-600D-BEIGE', 'category' => 'Fabric', 'unit' => 'meter', 'cost' => 52.00, 'supplier_id' => \$supplierIds[2]],
    
    // Zippers and Fasteners
    ['name' => 'YKK Waterproof Zipper 8mm - Black', 'sku' => 'ZIP-YKK-8MM-BLACK', 'category' => 'Hardware', 'unit' => 'piece', 'cost' => 85.00, 'supplier_id' => \$supplierIds[5]],
    ['name' => 'YKK Waterproof Zipper 10mm - Blue', 'sku' => 'ZIP-YKK-10MM-BLUE', 'category' => 'Hardware', 'unit' => 'piece', 'cost' => 95.00, 'supplier_id' => \$supplierIds[5]],
    ['name' => 'Plastic Buckles 25mm - Black', 'sku' => 'BUCKLE-25MM-BLACK', 'category' => 'Hardware', 'unit' => 'piece', 'cost' => 12.50, 'supplier_id' => \$supplierIds[5]],
    ['name' => 'Aluminum Grommets 12mm', 'sku' => 'GROMMET-12MM-ALU', 'category' => 'Hardware', 'unit' => 'piece', 'cost' => 3.20, 'supplier_id' => \$supplierIds[5]],
    
    // Tent Poles and Frames
    ['name' => 'Aluminum Tent Poles 7001 Series - 8.5mm', 'sku' => 'POLE-ALU-7001-8.5MM', 'category' => 'Frame', 'unit' => 'set', 'cost' => 120.00, 'supplier_id' => \$supplierIds[3]],
    ['name' => 'Aluminum Tent Poles 7001 Series - 9.5mm', 'sku' => 'POLE-ALU-7001-9.5MM', 'category' => 'Frame', 'unit' => 'set', 'cost' => 135.00, 'supplier_id' => \$supplierIds[3]],
    ['name' => 'Fiberglass Tent Poles - 11mm', 'sku' => 'POLE-FIBER-11MM', 'category' => 'Frame', 'unit' => 'set', 'cost' => 85.00, 'supplier_id' => \$supplierIds[3]],
    
    // Groundsheets and Flooring
    ['name' => 'PE Groundsheet 180g/m2 - Clear', 'sku' => 'GROUND-PE-180G-CLEAR', 'category' => 'Flooring', 'unit' => 'meter', 'cost' => 28.00, 'supplier_id' => \$supplierIds[4]],
    ['name' => 'Oxford Fabric Groundsheet 300D - Black', 'sku' => 'GROUND-OXF-300D-BLACK', 'category' => 'Flooring', 'unit' => 'meter', 'cost' => 35.00, 'supplier_id' => \$supplierIds[4]],
    
    // Accessories
    ['name' => 'Guy Ropes 3mm x 3m - Reflective', 'sku' => 'ROPE-3MM-3M-REFLECT', 'category' => 'Accessories', 'unit' => 'piece', 'cost' => 8.50, 'supplier_id' => \$supplierIds[4]],
    ['name' => 'Tent Stakes Aluminum 18cm', 'sku' => 'STAKE-ALU-18CM', 'category' => 'Accessories', 'unit' => 'piece', 'cost' => 15.00, 'supplier_id' => \$supplierIds[4]],
    ['name' => 'Carry Bag 600D Polyester - Black', 'sku' => 'BAG-600D-BLACK', 'category' => 'Accessories', 'unit' => 'piece', 'cost' => 45.00, 'supplier_id' => \$supplierIds[2]],
];

\$productIds = [];
foreach (\$products as \$product) {
    \$productId = DB::table('products')->insertGetId([
        'name' => \$product['name'],
        'sku' => \$product['sku'],
        'description' => \$product['name'] . ' for tent manufacturing',
        'category' => \$product['category'],
        'unit' => \$product['unit'],
        'cost' => \$product['cost'],
        'price' => \$product['cost'] * 1.5, // 50% markup
        'supplier_id' => \$product['supplier_id'],
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    \$productIds[] = \$productId;
    echo "✅ Product: " . \$product['name'] . "\n";
}

// =============================================================================
// WORK ORDERS SETUP - Realistic Manufacturing Orders
// =============================================================================

echo "\n📋 Creating realistic work orders...\n";
\$workOrders = [
    // URGENT Orders (Red Priority)
    [
        'wo_number' => 'WO-ZERVI-2024-001',
        'barcode' => 'BC-ZERVI-2024-001',
        'customer_id' => \$customerIds[1], // Isuzu (URGENT)
        'customer_po_number' => 'PO-ISUZU-URGENT-001',
        'customer_name' => 'Isuzu Motors (Thailand) Co., Ltd',
        'promised_delivery_date' => now()->addDays(7), // URGENT - 7 days
        'department' => 'cutting',
        'status' => 'queued',
        'priority' => 'urgent',
        'notes' => 'URGENT: Heavy-duty camping tents for Isuzu dealer conference - weather resistant specification required',
        'quantity_requested' => 75,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(6),
        'estimated_material_cost' => 187500.00,
        'actual_material_cost' => 0,
        'labor_cost' => 25000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'wo_number' => 'WO-ZERVI-2024-002',
        'barcode' => 'BC-ZERVI-2024-002',
        'customer_id' => \$customerIds[3], // Central (Holiday Rush)
        'customer_po_number' => 'PO-CENTRAL-HOLIDAY-002',
        'customer_name' => 'Central Department Store Co., Ltd',
        'promised_delivery_date' => now()->addDays(10), // Holiday season rush
        'department' => 'cutting',
        'status' => 'queued',
        'priority' => 'urgent',
        'notes' => 'Holiday season rush: 200 premium camping tents for Christmas sales - premium packaging required',
        'quantity_requested' => 200,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(9),
        'estimated_material_cost' => 500000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 60000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // HIGH Priority Orders (Orange Priority)
    [
        'wo_number' => 'WO-ZERVI-2024-003',
        'barcode' => 'BC-ZERVI-2024-003',
        'customer_id' => \$customerIds[0], // Toyota
        'customer_po_number' => 'PO-TOYOTA-CAMPING-003',
        'customer_name' => 'Toyota Motor Thailand Co., Ltd',
        'promised_delivery_date' => now()->addDays(14), // 2 weeks
        'department' => 'cutting',
        'status' => 'queued',
        'priority' => 'high',
        'notes' => 'Premium camping tents for Toyota employee family day - TIS 1238-2564 quality compliance required',
        'quantity_requested' => 50,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(13),
        'estimated_material_cost' => 125000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 18000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'wo_number' => 'WO-ZERVI-2024-004',
        'barcode' => 'BC-ZERVI-2024-004',
        'customer_id' => \$customerIds[2], // Mitsubishi
        'customer_po_number' => 'PO-MITSUBISHI-ADVENTURE-004',
        'customer_name' => 'Mitsubishi Motors (Thailand) Co., Ltd',
        'promised_delivery_date' => now()->addDays(21), // 3 weeks
        'department' => 'cutting',
        'status' => 'queued',
        'priority' => 'high',
        'notes' => 'Adventure camping tents for Mitsubishi dealer network outdoor events - all-weather specification',
        'quantity_requested' => 100,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(20),
        'estimated_material_cost' => 250000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 35000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // NORMAL Priority Orders (Green Priority)
    [
        'wo_number' => 'WO-ZERVI-2024-005',
        'barcode' => 'BC-ZERVI-2024-005',
        'customer_id' => \$customerIds[4], // Decathlon
        'customer_po_number' => 'PO-DECATHLON-PREMIUM-005',
        'customer_name' => 'Decathlon Thailand',
        'promised_delivery_date' => now()->addDays(28), // 4 weeks
        'department' => 'cutting',
        'status' => 'queued',
        'priority' => 'normal',
        'notes' => 'Premium camping tents for Decathlon sports retailers - international quality standards and eco-friendly materials',
        'quantity_requested' => 150,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(27),
        'estimated_material_cost' => 300000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 45000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'wo_number' => 'WO-ZERVI-2024-006',
        'barcode' => 'BC-ZERVI-2024-006',
        'customer_id' => \$customerIds[5], // Honda
        'customer_po_number' => 'PO-HONDA-FAMILY-006',
        'customer_name' => 'Honda Automobile (Thailand) Co., Ltd',
        'promised_delivery_date' => now()->addDays(35), // 5 weeks
        'department' => 'cutting',
        'status' => 'queued',
        'priority' => 'normal',
        'notes' => 'Family camping tents for Honda employee family day events - family-friendly design with safety features',
        'quantity_requested' => 80,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(34),
        'estimated_material_cost' => 160000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 24000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // LOW Priority Orders (Gray Priority)
    [
        'wo_number' => 'WO-ZERVI-2024-007',
        'barcode' => 'BC-ZERVI-2024-007',
        'customer_id' => \$customerIds[6], // Nissan
        'customer_po_number' => 'PO-NISSAN-CORPORATE-007',
        'customer_name' => 'Nissan Motor Thailand Co., Ltd',
        'promised_delivery_date' => now()->addDays(42), // 6 weeks
        'department' => 'cutting',
        'status' => 'queued',
        'priority' => 'low',
        'notes' => 'Corporate camping tents for Nissan internal events - standard specification with company branding',
        'quantity_requested' => 60,
        'quantity_completed' => 0,
        'quantity_scrap' => 0,
        'planned_start_date' => now(),
        'planned_end_date' => now()->addDays(41),
        'estimated_material_cost' => 120000.00,
        'actual_material_cost' => 0,
        'labor_cost' => 18000.00,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

\$workOrderIds = [];
foreach (\$workOrders as \$order) {
    \$workOrderId = DB::table('zervi_work_orders')->insertGetId(\$order);
    \$workOrderIds[] = \$workOrderId;
    echo "✅ Work Order: " . \$order['wo_number'] . " (" . \$order['priority'] . " priority)\n";
}

// =============================================================================
// WORK ORDER TASKS SETUP - Production Tasks for Each Order
// =============================================================================

echo "\n🔧 Creating production tasks...\n";
\$tasks = [
    // Tasks for WO-ZERVI-2024-001 (Isuzu - URGENT)
    [
        'work_order_id' => \$workOrderIds[0],
        'operation_name' => 'Cut Waterproof Fabric Panels',
        'department' => 'cutting',
        'sequence_order' => 1,
        'estimated_hours' => 12,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Cut 75 sets of fabric panels using automated cutting machine. Ensure precise dimensions for heavy-duty specification.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[0],
        'operation_name' => 'Sew Main Tent Body',
        'department' => 'sewing',
        'sequence_order' => 2,
        'estimated_hours' => 18,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Sew main tent body with reinforced seams for heavy-duty use. Use double-stitching on stress points.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[0],
        'operation_name' => 'Install Zippers and Fasteners',
        'department' => 'sewing',
        'sequence_order' => 3,
        'estimated_hours' => 6,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Install YKK waterproof zippers and heavy-duty buckles. Ensure smooth operation and weather resistance.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[0],
        'operation_name' => 'Quality Inspection - TIS 1238-2564',
        'department' => 'qc',
        'sequence_order' => 4,
        'estimated_hours' => 3,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => true,
        'work_instructions' => 'Conduct comprehensive quality inspection per TIS 1238-2564 standards. Check waterproofing, seam strength, and overall construction.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // Tasks for WO-ZERVI-2024-002 (Central - Holiday Rush)
    [
        'work_order_id' => \$workOrderIds[1],
        'operation_name' => 'Cut Premium Fabric Panels',
        'department' => 'cutting',
        'sequence_order' => 1,
        'estimated_hours' => 24,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Cut 200 sets of premium fabric panels with precision for retail quality. Extra care for holiday packaging.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[1],
        'operation_name' => 'Sew Premium Tent Body',
        'department' => 'sewing',
        'sequence_order' => 2,
        'estimated_hours' => 36,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Sew premium tent bodies with retail-quality finishing. Ensure perfect appearance for store display.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[1],
        'operation_name' => 'Install Premium Hardware',
        'department' => 'sewing',
        'sequence_order' => 3,
        'estimated_hours' => 12,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Install premium zippers, buckles, and hardware. Focus on aesthetic appeal for retail market.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[1],
        'operation_name' => 'Final Quality Control',
        'department' => 'qc',
        'sequence_order' => 4,
        'estimated_hours' => 6,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => true,
        'work_instructions' => 'Final quality control for retail market. Check appearance, functionality, and packaging readiness.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // Tasks for WO-ZERVI-2024-003 (Toyota - HIGH Priority)
    [
        'work_order_id' => \$workOrderIds[2],
        'operation_name' => 'Cut Toyota Specification Fabric',
        'department' => 'cutting',
        'sequence_order' => 1,
        'estimated_hours' => 8,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Cut fabric panels to Toyota specification. Ensure precision for automotive-grade quality.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[2],
        'operation_name' => 'Sew Toyota Quality Tent Body',
        'department' => 'sewing',
        'sequence_order' => 2,
        'estimated_hours' => 12,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Sew tent body with Toyota quality standards. Extra attention to durability and finish.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[2],
        'operation_name' => 'Install Premium Toyota Hardware',
        'department' => 'sewing',
        'sequence_order' => 3,
        'estimated_hours' => 4,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => false,
        'work_instructions' => 'Install premium hardware with Toyota branding. Ensure corporate quality standards.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[2],
        'operation_name' => 'TIS 1238-2564 Quality Inspection',
        'department' => 'qc',
        'sequence_order' => 4,
        'estimated_hours' => 2,
        'actual_hours' => 0,
        'status' => 'pending',
        'is_blocked' => false,
        'requires_qc' => true,
        'work_instructions' => 'Conduct TIS 1238-2564 quality inspection for automotive applications. Document all findings.',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach (\$tasks as \$task) {
    DB::table('zervi_work_order_tasks')->insert(\$task);
}
echo "✅ " . count(\$tasks) . " production tasks created\n";

// =============================================================================
// MATERIAL LINES SETUP - Realistic Material Requirements with Shortages
// =============================================================================

echo "\n📦 Creating material lines with realistic shortages...\n";
\$materialLines = [
    // Material lines for WO-ZERVI-2024-001 (Isuzu - URGENT)
    [
        'work_order_id' => \$workOrderIds[0],
        'product_id' => \$productIds[0], // Waterproof Nylon Fabric 420D - Blue
        'quantity_required' => 262.5, // 75 tents × 3.5 meters per tent
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 52.5, // 52.5 meters shortage!
        'has_shortage' => true,
        'expected_restock_date' => now()->addDays(3),
        'shortage_notes' => 'Critical shortage due to high demand from automotive sector. Supplier Thai Toray Textile experiencing production delays.',
        'shortage_reported_at' => now(),
        'shortage_reported_by' => 1,
        'unit_cost' => 45.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'work_order_id' => \$workOrderIds[0],
        'product_id' => \$productIds[5], // YKK Waterproof Zipper 8mm - Black
        'quantity_required' => 150, // 75 tents × 2 zippers per tent
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
        'work_order_id' => \$workOrderIds[0],
        'product_id' => \$productIds[10], // Aluminum Tent Poles 7001 Series - 8.5mm
        'quantity_required' => 300, // 75 tents × 4 pole sets per tent
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 75, // 75 pole sets shortage!
        'has_shortage' => true,
        'expected_restock_date' => now()->addDays(5),
        'shortage_notes' => 'Aluminum pole shortage due to global supply chain issues. Supplier Saha-Union working on expedited delivery.',
        'shortage_reported_at' => now(),
        'shortage_reported_by' => 1,
        'unit_cost' => 120.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // Material lines for WO-ZERVI-2024-002 (Central - Holiday Rush)
    [
        'work_order_id' => \$workOrderIds[1],
        'product_id' => \$productIds[1], // Waterproof Nylon Fabric 420D - Green
        'quantity_required' => 700, // 200 tents × 3.5 meters per tent
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
        'work_order_id' => \$workOrderIds[1],
        'product_id' => \$productIds[6], // YKK Waterproof Zipper 10mm - Blue
        'quantity_required' => 400, // 200 tents × 2 zippers per tent
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
    
    // Material lines for WO-ZERVI-2024-003 (Toyota - HIGH Priority)
    [
        'work_order_id' => \$workOrderIds[2],
        'product_id' => \$productIds[2], // Waterproof Nylon Fabric 420D - Orange
        'quantity_required' => 175, // 50 tents × 3.5 meters per tent
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
        'work_order_id' => \$workOrderIds[2],
        'product_id' => \$productIds[11], // Aluminum Tent Poles 7001 Series - 9.5mm
        'quantity_required' => 200, // 50 tents × 4 pole sets per tent
        'quantity_reserved' => 0,
        'quantity_issued' => 0,
        'quantity_consumed' => 0,
        'quantity_returned' => 0,
        'quantity_shortage' => 25, // Minor shortage
        'has_shortage' => true,
        'expected_restock_date' => now()->addDays(2),
        'shortage_notes' => 'Minor shortage of premium 9.5mm poles. Standard 8.5mm poles can be substituted if needed.',
        'shortage_reported_at' => now(),
        'shortage_reported_by' => 1,
        'unit_cost' => 135.00,
        'total_cost' => 0,
        'status' => 'planned',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach (\$materialLines as \$material) {
    DB::table('zervi_material_lines')->insert(\$material);
}
echo "✅ " . count(\$materialLines) . " material lines created with realistic shortages\n";

// =============================================================================
// QUALITY RECORDS SETUP - TIS 1238-2564 Compliance Records
// =============================================================================

echo "\n🔍 Creating TIS 1238-2564 quality compliance records...\n";
\$qualityRecords = [
    [
        'work_order_id' => \$workOrderIds[2], // Toyota order
        'task_id' => 11, // Toyota QC task
        'product_id' => \$productIds[2], // Orange fabric
        'inspected_by' => 1,
        'qc_number' => 'QC-2024-TY-001',
        'inspection_type' => 'incoming',
        'department' => 'qc',
        'result' => 'pass',
        'notes' => 'Incoming inspection of Toyota specification orange fabric. All TIS 1238-2564 requirements met.',
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
        'work_order_id' => \$workOrderIds[2], // Toyota order
        'task_id' => 11, // Toyota QC task
        'product_id' => \$productIds[11], // Premium poles
        'inspected_by' => 1,
        'qc_number' => 'QC-2024-TY-002',
        'inspection_type' => 'in_process',
        'department' => 'qc',
        'result' => 'conditional',
        'notes' => 'In-process inspection found minor surface imperfections on 3 pole sets. Requires rework before final approval.',
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
    [
        'work_order_id' => \$workOrderIds[0], // Isuzu order
        'task_id' => 4, // Isuzu QC task
        'product_id' => \$productIds[0], // Blue fabric
        'inspected_by' => 1,
        'qc_number' => 'QC-2024-IS-001',
        'inspection_type' => 'final',
        'department' => 'qc',
        'result' => 'fail',
        'notes' => 'Final inspection failed due to insufficient waterproof coating on fabric samples. Material does not meet heavy-duty specification.',
        'standard_reference' => 'TIS 1238-2564',
        'requires_resolution' => true,
        'fail_description' => 'Waterproof coating below specification requirements for heavy-duty automotive application',
        'disposition' => 'reject',
        'defect_quantity' => 75,
        'inspected_at' => now(),
        'due_date' => now()->addDays(2),
        'resolved_at' => null,
        'resolved_by' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach (\$qualityRecords as \$record) {
    DB::table('zervi_quality_records')->insert(\$record);
}
echo "✅ " . count(\$qualityRecords) . " quality records created with TIS compliance\n";

// =============================================================================
// SUMMARY AND FINAL STATUS
// =============================================================================

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎉 ZERVI ASIA MES - COMPREHENSIVE TESTING DATA COMPLETE!\n";
echo str_repeat("=", 60) . "\n\n";

// Final statistics
echo "📊 FINAL STATISTICS:\n";
echo "✅ Company: Zervi Asia Co., Ltd (Thailand)\n";
echo "✅ Customers: " . count(\$customers) . " major Thai companies\n";
echo "✅ Suppliers: " . count(\$suppliers) . " premium material suppliers\n";
echo "✅ Products: " . count(\$products) . " tent manufacturing materials\n";
echo "✅ Work Orders: " . count(\$workOrders) . " realistic manufacturing orders\n";
echo "✅ Production Tasks: " . count(\$tasks) . " detailed production tasks\n";
echo "✅ Material Lines: " . count(\$materialLines) . " material requirements with shortages\n";
echo "✅ Quality Records: " . count(\$qualityRecords) . " TIS 1238-2564 compliance records\n\n";

// Priority breakdown
echo "🎯 PRIORITY BREAKDOWN:\n";
\$priorityBreakdown = DB::table('zervi_work_orders')
    ->select('priority', DB::raw('count(*) as count'))
    ->groupBy('priority')
    ->get();

foreach (\$priorityBreakdown as \$priority) {
    \$color = match(\$priority->priority) {
        'urgent' => '🔴',
        'high' => '🟠',
        'normal' => '🟢',
        'low' => '⚪',
        default => '⚪'
    };
    echo \$color . " " . strtoupper(\$priority->priority) . ": " . \$priority->count . " orders\n";
}

// Shortage summary
echo "\n📦 SHORTAGE SUMMARY:\n";
\$shortageCount = DB::table('zervi_material_lines')->where('has_shortage', true)->count();
\$totalShortages = DB::table('zervi_material_lines')->sum('quantity_shortage');
echo "🔴 Active Shortages: " . \$shortageCount . " material lines\n";
echo "📏 Total Shortage Quantity: " . number_format(\$totalShortages, 1) . " units\n";

// Department workload
echo "\n🏭 DEPARTMENT WORKLOAD:\n";
\$departmentWorkload = DB::table('zervi_work_order_tasks')
    ->select('department', DB::raw('count(*) as task_count'), DB::raw('sum(estimated_hours) as total_hours'))
    ->groupBy('department')
    ->get();

foreach (\$departmentWorkload as \$dept) {
    echo "⚙️  " . strtoupper(\$dept->department) . ": " . \$dept->task_count . " tasks, " . \$dept->total_hours . " hours\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🚀 READY FOR SUPERVISOR TESTING!\n";
echo "🌐 Access: http://localhost:8000/admin\n";
echo "📧 Login: admin@zervi.com / admin123\n";
echo "📋 Test: Customer context, material shortages, Kanban workflow, TIS compliance\n";
echo str_repeat("=", 60) . "\n";

echo "\n🎊 Happy Manufacturing! 🏭✨\n";