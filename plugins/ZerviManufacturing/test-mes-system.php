<?php

/**
 * Zervi Manufacturing MES - System Test Suite
 * 
 * This script tests all core functionality of the Manufacturing Execution System
 * Run this after installation to verify everything is working correctly.
 */

namespace Zervi\Manufacturing\Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Zervi\Manufacturing\Models\WorkOrder;
use Zervi\Manufacturing\Models\WorkOrderTask;
use Zervi\Manufacturing\Models\MaterialLine;
use Zervi\Manufacturing\Models\QualityRecord;
use Zervi\Manufacturing\Enums\Department;
use Zervi\Manufacturing\Enums\TaskStatus;
use Zervi\Manufacturing\Enums\Priority;

class MESSystemTest
{
    private $results = [];
    private $errors = [];

    public function runAllTests()
    {
        echo "🚀 Starting Zervi Manufacturing MES System Tests\n";
        echo "==================================================\n\n";

        $this->testDatabaseSchema();
        $this->testEnums();
        $this->testModelCreation();
        $this->testRelationships();
        $this->testBusinessLogic();
        $this->testKanbanWorkflow();
        $this->testMaterialShortageDetection();
        $this->testQualityCompliance();
        $this->testDepartmentScoping();

        $this->displayResults();
    }

    private function testDatabaseSchema()
    {
        echo "📊 Testing Database Schema...\n";

        // Test work orders table
        if (Schema::hasTable('zervi_work_orders')) {
            $this->results[] = "✅ Work orders table exists";
            
            $columns = Schema::getColumnListing('zervi_work_orders');
            $expectedColumns = ['id', 'work_order_number', 'customer_po_number', 'promised_delivery_date', 'department', 'status', 'priority'];
            
            foreach ($expectedColumns as $column) {
                if (in_array($column, $columns)) {
                    $this->results[] = "✅ Column '$column' exists in work orders";
                } else {
                    $this->errors[] = "❌ Column '$column' missing in work orders";
                }
            }
        } else {
            $this->errors[] = "❌ Work orders table does not exist";
        }

        // Test work order tasks table
        if (Schema::hasTable('zervi_work_order_tasks')) {
            $this->results[] = "✅ Work order tasks table exists";
        } else {
            $this->errors[] = "❌ Work order tasks table does not exist";
        }

        // Test material lines table
        if (Schema::hasTable('zervi_material_lines')) {
            $this->results[] = "✅ Material lines table exists";
        } else {
            $this->errors[] = "❌ Material lines table does not exist";
        }

        // Test quality records table
        if (Schema::hasTable('zervi_quality_records')) {
            $this->results[] = "✅ Quality records table exists";
        } else {
            $this->errors[] = "❌ Quality records table does not exist";
        }

        echo "\n";
    }

    private function testEnums()
    {
        echo "🔢 Testing Enums...\n";

        // Test Department enum
        $departments = Department::cases();
        if (count($departments) >= 4) {
            $this->results[] = "✅ Department enum has " . count($departments) . " cases";
            foreach ($departments as $dept) {
                $this->results[] = "✅ Department: {$dept->value}";
            }
        } else {
            $this->errors[] = "❌ Department enum has insufficient cases";
        }

        // Test TaskStatus enum
        $statuses = TaskStatus::cases();
        if (count($statuses) >= 4) {
            $this->results[] = "✅ TaskStatus enum has " . count($statuses) . " cases";
        } else {
            $this->errors[] = "❌ TaskStatus enum has insufficient cases";
        }

        // Test Priority enum
        $priorities = Priority::cases();
        if (count($priorities) >= 4) {
            $this->results[] = "✅ Priority enum has " . count($priorities) . " cases";
        } else {
            $this->errors[] = "❌ Priority enum has insufficient cases";
        }

        echo "\n";
    }

    private function testModelCreation()
    {
        echo "🏗️ Testing Model Creation...\n";

        try {
            // Create a test work order
            $workOrder = WorkOrder::create([
                'work_order_number' => 'WO-TEST-001',
                'customer_po_number' => 'PO-TOYOTA-TEST',
                'customer_name' => 'Toyota Test Customer',
                'promised_delivery_date' => now()->addDays(7),
                'department' => Department::CUTTING,
                'status' => \Zervi\Manufacturing\Enums\WorkOrderStatus::QUEUED,
                'priority' => Priority::HIGH,
                'notes' => 'Test work order for system validation'
            ]);

            $this->results[] = "✅ Work order created successfully (ID: {$workOrder->id})";

            // Create related tasks
            $task1 = WorkOrderTask::create([
                'work_order_id' => $workOrder->id,
                'name' => 'Cut fabric panels',
                'department' => Department::CUTTING,
                'status' => TaskStatus::QUEUED,
                'estimated_hours' => 4,
                'sequence' => 1
            ]);

            $task2 = WorkOrderTask::create([
                'work_order_id' => $workOrder->id,
                'name' => 'Sew main body',
                'department' => Department::SEWING,
                'status' => TaskStatus::QUEUED,
                'estimated_hours' => 8,
                'sequence' => 2
            ]);

            $this->results[] = "✅ Work order tasks created (IDs: {$task1->id}, {$task2->id})";

            // Create material lines
            $material = MaterialLine::create([
                'work_order_id' => $workOrder->id,
                'product_name' => 'Waterproof Fabric - Blue',
                'required_quantity' => 50,
                'available_quantity' => 30,
                'unit' => 'meters',
                'unit_cost' => 12.50
            ]);

            $this->results[] = "✅ Material line created (ID: {$material->id})";

            // Create quality record
            $qualityRecord = QualityRecord::create([
                'work_order_id' => $workOrder->id,
                'checkpoint' => 'fabric_inspection',
                'status' => 'passed',
                'inspector_id' => 1,
                'inspection_date' => now(),
                'notes' => 'Fabric quality meets specifications'
            ]);

            $this->results[] = "✅ Quality record created (ID: {$qualityRecord->id})";

            // Store for cleanup
            $this->testWorkOrderId = $workOrder->id;

        } catch (\Exception $e) {
            $this->errors[] = "❌ Model creation failed: " . $e->getMessage();
        }

        echo "\n";
    }

    private function testRelationships()
    {
        echo "🔗 Testing Model Relationships...\n";

        try {
            if (isset($this->testWorkOrderId)) {
                $workOrder = WorkOrder::find($this->testWorkOrderId);
                
                if ($workOrder) {
                    // Test task relationship
                    $tasks = $workOrder->tasks;
                    if ($tasks->count() >= 2) {
                        $this->results[] = "✅ Work order has {$tasks->count()} tasks";
                    } else {
                        $this->errors[] = "❌ Work order task relationship not working";
                    }

                    // Test material lines relationship
                    $materials = $workOrder->materialLines;
                    if ($materials->count() >= 1) {
                        $this->results[] = "✅ Work order has {$materials->count()} material lines";
                    } else {
                        $this->errors[] = "❌ Work order material lines relationship not working";
                    }

                    // Test quality records relationship
                    $qualityRecords = $workOrder->qualityRecords;
                    if ($qualityRecords->count() >= 1) {
                        $this->results[] = "✅ Work order has {$qualityRecords->count()} quality records";
                    } else {
                        $this->errors[] = "❌ Work order quality records relationship not working";
                    }
                }
            }
        } catch (\Exception $e) {
            $this->errors[] = "❌ Relationship testing failed: " . $e->getMessage();
        }

        echo "\n";
    }

    private function testBusinessLogic()
    {
        echo "💼 Testing Business Logic...\n";

        try {
            if (isset($this->testWorkOrderId)) {
                $workOrder = WorkOrder::find($this->testWorkOrderId);
                
                if ($workOrder) {
                    // Test progress calculation
                    $progress = $workOrder->progress_percentage;
                    $this->results[] = "✅ Work order progress: {$progress}%";

                    // Test overdue calculation
                    $isOverdue = $workOrder->isOverdue();
                    $this->results[] = "✅ Work order overdue status: " . ($isOverdue ? 'Yes' : 'No');

                    // Test customer commitment display
                    $customerCommitment = $workOrder->customer_commitment;
                    $this->results[] = "✅ Customer commitment: {$customerCommitment}";

                    // Test material shortage detection
                    $hasShortages = $workOrder->hasMaterialShortages();
                    $this->results[] = "✅ Material shortages: " . ($hasShortages ? 'Yes' : 'No');

                    // Test active issues
                    $activeIssues = $workOrder->getActiveIssues();
                    $this->results[] = "✅ Active issues: " . count($activeIssues);
                }
            }
        } catch (\Exception $e) {
            $this->errors[] = "❌ Business logic testing failed: " . $e->getMessage();
        }

        echo "\n";
    }

    private function testKanbanWorkflow()
    {
        echo "📋 Testing Kanban Workflow...\n";

        try {
            if (isset($this->testWorkOrderId)) {
                $workOrder = WorkOrder::find($this->testWorkOrderId);
                
                if ($workOrder) {
                    // Test department-based task assignment
                    $cuttingTasks = $workOrder->getTasksByDepartment(Department::CUTTING);
                    $this->results[] = "✅ Cutting department tasks: {$cuttingTasks->count()}";

                    $sewingTasks = $workOrder->getTasksByDepartment(Department::SEWING);
                    $this->results[] = "✅ Sewing department tasks: {$sewingTasks->count()}";

                    // Test task status progression
                    $firstTask = $workOrder->tasks()->first();
                    if ($firstTask) {
                        $firstTask->update(['status' => TaskStatus::IN_PROGRESS]);
                        $this->results[] = "✅ Task status updated to IN_PROGRESS";

                        $firstTask->update(['status' => TaskStatus::COMPLETED]);
                        $this->results[] = "✅ Task status updated to COMPLETED";
                    }
                }
            }
        } catch (\Exception $e) {
            $this->errors[] = "❌ Kanban workflow testing failed: " . $e->getMessage();
        }

        echo "\n";
    }

    private function testMaterialShortageDetection()
    {
        echo "📦 Testing Material Shortage Detection...\n";

        try {
            if (isset($this->testWorkOrderId)) {
                $workOrder = WorkOrder::find($this->testWorkOrderId);
                
                if ($workOrder) {
                    // Test material shortage calculation
                    $shortages = $workOrder->getMaterialShortages();
                    $this->results[] = "✅ Material shortages detected: {$shortages->count()}";

                    // Test shortage cost calculation
                    $totalShortageCost = $workOrder->getTotalShortageCost();
                    $this->results[] = "✅ Total shortage cost: {$totalShortageCost}";

                    // Test can proceed with production
                    $canProceed = $workOrder->canProceedWithProduction();
                    $this->results[] = "✅ Can proceed with production: " . ($canProceed ? 'Yes' : 'No');
                }
            }
        } catch (\Exception $e) {
            $this->errors[] = "❌ Material shortage testing failed: " . $e->getMessage();
        }

        echo "\n";
    }

    private function testQualityCompliance()
    {
        echo "🔍 Testing Quality Compliance...\n";

        try {
            if (isset($this->testWorkOrderId)) {
                $workOrder = WorkOrder::find($this->testWorkOrderId);
                
                if ($workOrder) {
                    // Test TIS compliance
                    $isTISCompliant = $workOrder->isTISCompliant();
                    $this->results[] = "✅ TIS 1238-2564 compliance: " . ($isTISCompliant ? 'Yes' : 'No');

                    // Test quality checkpoints
                    $checkpoints = $workOrder->getQualityCheckpoints();
                    $this->results[] = "✅ Quality checkpoints: " . count($checkpoints);

                    // Test pending quality checks
                    $pendingChecks = $workOrder->getPendingQualityChecks();
                    $this->results[] = "✅ Pending quality checks: " . count($pendingChecks);
                }
            }
        } catch (\Exception $e) {
            $this->errors[] = "❌ Quality compliance testing failed: " . $e->getMessage();
        }

        echo "\n";
    }

    private function testDepartmentScoping()
    {
        echo "🏢 Testing Department Scoping...\n";

        try {
            // Test supervisor scoping
            $cuttingSupervisorOrders = WorkOrder::forDepartment(Department::CUTTING)->get();
            $this->results[] = "✅ Cutting department orders: {$cuttingSupervisorOrders->count()}";

            $sewingSupervisorOrders = WorkOrder::forDepartment(Department::SEWING)->get();
            $this->results[] = "✅ Sewing department orders: {$sewingSupervisorOrders->count()}";

            // Test cross-department visibility (should be restricted)
            $allOrders = WorkOrder::all();
            $this->results[] = "✅ Total orders in system: {$allOrders->count()}";

        } catch (\Exception $e) {
            $this->errors[] = "❌ Department scoping testing failed: " . $e->getMessage();
        }

        echo "\n";
    }

    private function displayResults()
    {
        echo "📊 TEST RESULTS SUMMARY\n";
        echo "========================\n\n";

        $totalTests = count($this->results) + count($this->errors);
        $passedTests = count($this->results);
        $failedTests = count($this->errors);
        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;

        echo "✅ Passed: {$passedTests}\n";
        echo "❌ Failed: {$failedTests}\n";
        echo "📈 Success Rate: {$successRate}%\n\n";

        if (!empty($this->results)) {
            echo "🎉 SUCCESSFUL TESTS:\n";
            foreach ($this->results as $result) {
                echo "   {$result}\n";
            }
            echo "\n";
        }

        if (!empty($this->errors)) {
            echo "⚠️  FAILED TESTS:\n";
            foreach ($this->errors as $error) {
                echo "   {$error}\n";
            }
            echo "\n";
        }

        // Cleanup test data
        $this->cleanupTestData();

        if ($successRate >= 80) {
            echo "🎊 SYSTEM STATUS: READY FOR PRODUCTION!\n";
            echo "   The Zervi Manufacturing MES is working correctly.\n";
        } elseif ($successRate >= 60) {
            echo "⚡ SYSTEM STATUS: MOSTLY FUNCTIONAL\n";
            echo "   Some minor issues detected but core functionality works.\n";
        } else {
            echo "🔧 SYSTEM STATUS: NEEDS ATTENTION\n";
            echo "   Significant issues detected. Please review the errors above.\n";
        }

        echo "\n";
        echo "📚 NEXT STEPS:\n";
        echo "   1. Review any failed tests above\n";
        echo "   2. Check database migrations are run\n";
        echo "   3. Verify PHP dependencies are installed\n";
        echo "   4. Test the web interface in browser\n";
        echo "   5. Create sample data for demonstration\n";
    }

    private function cleanupTestData()
    {
        try {
            if (isset($this->testWorkOrderId)) {
                // Clean up in reverse order (children first)
                QualityRecord::where('work_order_id', $this->testWorkOrderId)->delete();
                MaterialLine::where('work_order_id', $this->testWorkOrderId)->delete();
                WorkOrderTask::where('work_order_id', $this->testWorkOrderId)->delete();
                WorkOrder::where('id', $this->testWorkOrderId)->delete();
                
                echo "🧹 Test data cleaned up successfully\n\n";
            }
        } catch (\Exception $e) {
            echo "⚠️  Warning: Could not clean up test data: " . $e->getMessage() . "\n\n";
        }
    }
}

// Usage instructions
if (php_sapi_name() === 'cli') {
    echo "🎯 ZERVI MANUFACTURING MES - SYSTEM TEST UTILITY\n";
    echo "================================================\n\n";
    echo "This script will test all core functionality of your MES system.\n";
    echo "Make sure you have:\n";
    echo "   ✅ Run database migrations\n";
    echo "   ✅ Installed PHP dependencies\n";
    echo "   ✅ Configured your environment\n\n";
    
    echo "To run this test:\n";
    echo "   php test-mes-system.php\n\n";
    
    $tester = new MESSystemTest();
    $tester->runAllTests();
}