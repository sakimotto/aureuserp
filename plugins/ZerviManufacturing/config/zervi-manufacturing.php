<?php

return [
    'name' => 'Zervi Manufacturing',
    'description' => 'Manufacturing Execution System (MES) for tent production with supervisor-focused Kanban workflow',
    'version' => '1.0.0',
    'author' => 'Zervi Manufacturing Team',
    
    // Department configuration
    'departments' => [
        'queued' => ['name' => 'Queued', 'color' => 'gray'],
        'cutting' => ['name' => 'Cutting (CNC)', 'color' => 'blue'],
        'sewing' => ['name' => 'Sewing (Tent)', 'color' => 'green'],
        'qc' => ['name' => 'Quality Control', 'color' => 'yellow'],
        'complete' => ['name' => 'Complete', 'color' => 'success'],
    ],
    
    // Kanban configuration
    'kanban' => [
        'columns' => ['queued', 'cutting', 'sewing', 'qc', 'complete'],
        'refresh_interval' => 30000, // 30 seconds
        'drag_drop_enabled' => true,
    ],
    
    // Supervisor dashboard widgets
    'dashboard_widgets' => [
        'active_jobs' => true,
        'overdue_jobs' => true,
        'blocked_tasks' => true,
        'material_shortages' => true,
    ],
    
    // Quality standards
    'quality_standards' => [
        'TIS_1238_2564' => 'TIS 1238-2564 - Textile Standards',
    ],
    
    // Block reasons for tasks
    'block_reasons' => [
        'material_shortage' => 'Material Shortage',
        'machine_maintenance' => 'Machine Maintenance',
        'qc_failure' => 'QC Failure',
        'operator_unavailable' => 'Operator Unavailable',
    ],
];