<?php

/**
 * Zervi Asia Company Setup - Fix UI Rendering
 * Creates proper partner-company relationship for UI display
 */

echo "🏭 Setting up Zervi Asia Company for UI display...\n\n";

// Step 1: Create Partner first
echo "1️⃣ Creating partner record...\n";
\$partnerId = DB::table('partners_partners')->insertGetId([
    'name' => 'Zervi Asia Co., Ltd',
    'email' => 'info@zervi-asia.com',
    'phone' => '+66-2-123-4567',
    'address' => '123 Industrial Park Road, Bang Phli District, Samut Prakan 10540, Thailand',
    'city' => 'Samut Prakan',
    'country' => 'TH',
    'tax_number' => '1234567890123',
    'description' => 'Leading tent and outdoor equipment manufacturer in Thailand, specializing in automotive and camping solutions with TIS 1238-2564 quality compliance.',
    'is_customer' => false,
    'is_supplier' => false,
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "✅ Partner created with ID: " . \$partnerId . "\n";

// Step 2: Create Company with partner relationship
echo "\n2️⃣ Creating company record...\n";
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
    'zip' => '10540',
    'country_id' => 1, // Thailand
    'state_id' => 1,   // Samut Prakan
    'partner_id' => \$partnerId, // Critical: Link to partner
    'currency_id' => 1, // THB
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "✅ Company created with ID: " . \$companyId . "\n";

// Step 3: Verify the setup
echo "\n3️⃣ Verifying setup...\n";
\$partner = DB::table('partners_partners')->find(\$partnerId);
\$company = DB::table('companies')->find(\$companyId);

echo "📋 Partner Details:\n";
echo "   Name: " . \$partner->name . "\n";
echo "   Email: " . \$partner->email . "\n";
echo "   Tax Number: " . \$partner->tax_number . "\n";

echo "\n🏢 Company Details:\n";
echo "   Name: " . \$company->name . "\n";
echo "   Company ID: " . \$company->company_id . "\n";
echo "   Tax ID: " . \$company->tax_id . "\n";
echo "   Partner ID: " . \$company->partner_id . "\n";

// Step 4: Check current system status
echo "\n4️⃣ System Status Check:\n";
echo "   Partners: " . DB::table('partners_partners')->count() . "\n";
echo "   Companies: " . DB::table('companies')->count() . "\n";
echo "   Work Orders: " . DB::table('zervi_work_orders')->count() . "\n";
echo "   Tasks: " . DB::table('zervi_work_order_tasks')->count() . "\n";

echo "\n🎉 Zervi Asia Company Setup Complete!\n";
echo "🌐 Access: http://localhost:8000/admin\n";
echo "📧 Login: admin@zervi.com / admin123\n";
echo "\nThe company should now appear in the UI! 🏭✨\n";