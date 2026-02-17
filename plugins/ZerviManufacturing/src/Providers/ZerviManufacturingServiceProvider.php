<?php

namespace Zervi\Manufacturing\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;
use Zervi\Manufacturing\Filament\Resources\WorkOrderResource;
use Zervi\Manufacturing\Filament\Pages\WorkOrderKanban;
use Zervi\Manufacturing\Filament\Pages\SupervisorDashboard;

class ZerviManufacturingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any additional services here
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'zervi-manufacturing');
        
        // Register department-scoped navigation
        $this->registerDepartmentNavigation();
    }

    protected function registerDepartmentNavigation(): void
    {
        Filament::serving(function () {
            $user = auth()->user();
            
            if (!$user) {
                return;
            }

            // Manufacturing Supervisor sees only 4 modules
            if ($user->hasRole('dept_supervisor')) {
                // Hide other Aureus modules from supervisors
                $this->hideNonManufacturingModules();
                
                // Register manufacturing-specific navigation
                Filament::registerNavigationGroups([
                    NavigationGroup::make('Operations')
                        ->items([
                            SupervisorDashboard::class,
                            WorkOrderResource::class,
                            // InventoryResource::class, // Will add when we create it
                            // IssueResource::class,     // Will add when we create it
                        ]),
                ]);
            }
        });
    }

    protected function hideNonManufacturingModules(): void
    {
        // Hide Sales modules
        if (class_exists('\Webkul\Sales\Filament\Resources\OrderResource')) {
            \Webkul\Sales\Filament\Resources\OrderResource::shouldRegisterNavigation(false);
        }
        
        // Hide Accounting modules
        if (class_exists('\Webkul\Accounting\Filament\Resources\InvoiceResource')) {
            \Webkul\Accounting\Filament\Resources\InvoiceResource::shouldRegisterNavigation(false);
        }
        
        // Hide HR modules (except what supervisors need)
        if (class_exists('\Webkul\Employees\Filament\Resources\EmployeeResource')) {
            // Keep employee access for supervisors but hide admin functions
            // This will be refined later based on specific needs
        }
    }
}