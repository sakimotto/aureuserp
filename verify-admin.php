<?php

/**
 * Zervi Asia MES - Admin User Verification & Login Test
 * This script verifies the admin user and tests login functionality
 */

// Check if running in Docker environment
if (php_sapi_name() === 'cli') {
    echo "🏭 Zervi Asia MES - Admin User Verification\n";
    echo "============================================\n\n";
    
    // Check if Laravel is available
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require __DIR__ . '/vendor/autoload.php';
        $app = require __DIR__ . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
        echo "✅ Laravel environment loaded\n";
        
        // Check admin user
        $user = \App\Models\User::where('email', 'admin@zervi.com')->first();
        
        if ($user) {
            echo "✅ Admin user found:\n";
            echo "   Name: {$user->name}\n";
            echo "   Email: {$user->email}\n";
            echo "   Created: {$user->created_at}\n";
            echo "   Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
            
            // Test password
            echo "\n🔑 Testing password verification...\n";
            if (\Hash::check('admin123', $user->password)) {
                echo "✅ Password 'admin123' is correct!\n";
            } else {
                echo "❌ Password verification failed!\n";
            }
            
            // Check Zervi Asia data
            echo "\n📊 Checking Zervi Asia data...\n";
            $workOrders = \DB::table('zervi_work_orders')->count();
            $tasks = \DB::table('zervi_work_order_tasks')->count();
            $materialLines = \DB::table('zervi_material_lines')->count();
            
            echo "✅ Work Orders: {$workOrders}\n";
            echo "✅ Tasks: {$tasks}\n";
            echo "✅ Material Lines: {$materialLines}\n";
            
            // Show sample work orders
            echo "\n🏭 Sample Work Orders:\n";
            $sampleOrders = \DB::table('zervi_work_orders')->limit(3)->get();
            foreach ($sampleOrders as $order) {
                echo "   - {$order->wo_number}: {$order->customer_name} ({$order->priority} priority)\n";
            }
            
            echo "\n🎉 Admin user verification complete!\n";
            echo "   Login URL: http://localhost:8000/admin\n";
            echo "   Username: admin@zervi.com\n";
            echo "   Password: admin123\n";
            
        } else {
            echo "❌ Admin user not found!\n";
            echo "   Creating new admin user...\n";
            
            $newUser = new \App\Models\User();
            $newUser->name = 'Admin User';
            $newUser->email = 'admin@zervi.com';
            $newUser->password = \Hash::make('admin123');
            $newUser->email_verified_at = now();
            $newUser->save();
            
            echo "✅ New admin user created!\n";
            echo "   Email: admin@zervi.com\n";
            echo "   Password: admin123\n";
            echo "   Login at: http://localhost:8000/admin\n";
        }
        
    } else {
        echo "❌ Laravel not found in current directory\n";
        echo "   Please run this from the Laravel application root\n";
    }
    
} else {
    // Web access - show simple status
    echo "<!DOCTYPE html>\n";
    echo "<html>\n";
    echo "<head><title>Zervi Asia MES - Admin Status</title></head>\n";
    echo "<body>\n";
    echo "<h1>🏭 Zervi Asia MES - Admin User Status</h1>\n";
    echo "<p>Admin verification script is ready. Run via command line for detailed status.</p>\n";
    echo "<p>Login at: <a href='/admin'>http://localhost:8000/admin</a></p>\n";
    echo "<p>Username: admin@zervi.com</p>\n";
    echo "<p>Password: admin123</p>\n";
    echo "</body>\n";
    echo "</html>\n";
}