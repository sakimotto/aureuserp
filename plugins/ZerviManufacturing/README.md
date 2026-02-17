# Zervi Manufacturing MES (Manufacturing Execution System)

A supervisor-focused Manufacturing Execution System plugin for AureusERP, designed specifically for tent manufacturing workflows with Kanban-style production management.

## 🎯 Key Features

### Supervisor-First Design
- **4-Item Navigation**: Simplified menu with only essential supervisor tools
- **Department Scoping**: Automatic filtering based on supervisor's assigned department
- **Customer Commitment Focus**: Displays "Toyota order due Thursday" instead of "WO-104"

### Kanban Board Workflow
- **Drag-and-Drop Interface**: Visual production workflow management
- **5-Stage Process**: QUEUED → CUTTING → SEWING → QC → COMPLETE
- **Rich Work Order Cards**: Customer context, delivery dates, priority badges
- **Real-time Updates**: Instant status changes across all users

### Material Shortage Management
- **Smart Alerts**: Automatic detection of material shortages
- **Expected Restock Tracking**: Shows when materials will be available
- **Production Blocking**: Prevents scheduling when materials unavailable

### Quality Compliance (TIS 1238-2564)
- **Built-in Quality Checks**: Automated compliance validation
- **Digital Quality Records**: Paperless quality documentation
- **Traceability**: Complete audit trail for all production steps

## 🏗️ Architecture

### Plugin Structure
```
plugins/ZerviManufacturing/
├── src/
│   ├── Filament/
│   │   ├── Pages/           # Kanban board and dashboards
│   │   └── Resources/       # CRUD resources for work orders
│   ├── Models/             # WorkOrder, WorkOrderTask, MaterialLine
│   ├── Enums/               # Department, TaskStatus, Priority
│   └── Providers/           # Service provider with navigation filtering
├── database/
│   └── migrations/          # Database schema definitions
├── resources/
│   └── views/              # Kanban board Blade templates
└── routes/
    └── web.php              # API routes for drag-drop operations
```

### Core Models

#### WorkOrder
- **Customer Context**: Links to sales orders with customer PO numbers
- **Department Assignment**: Scoped to specific manufacturing departments
- **Progress Tracking**: Automatic calculation based on task completion
- **Due Date Management**: Promised delivery date tracking

#### WorkOrderTask
- **Status Workflow**: QUEUED, IN_PROGRESS, BLOCKED, COMPLETED
- **Department Routing**: Automatic assignment to appropriate departments
- **Time Tracking**: Start/end timestamps with duration calculation
- **Blocking Logic**: Tasks can block other tasks (material shortages, dependencies)

#### MaterialLine
- **Inventory Integration**: Links to existing inventory system
- **Shortage Detection**: Automatic calculation of available vs required
- **Restock Tracking**: Expected availability dates for out-of-stock items

## 🚀 Installation

### Prerequisites
- PHP 8.1+
- Laravel 10.x
- AureusERP Core
- Filament Admin Panel

### Quick Setup
```bash
# Navigate to your AureusERP installation
cd your-aureuserp-directory

# Install the plugin
composer require zervi/manufacturing

# Run migrations
php artisan migrate

# Install assets
php artisan filament:assets

# Clear caches
php artisan cache:clear
php artisan config:clear
```

### Configuration
1. **Department Assignment**: Assign supervisors to specific departments in user management
2. **Navigation Filtering**: Plugin automatically hides non-manufacturing modules
3. **Quality Standards**: Configure TIS 1238-2564 compliance requirements

## 📋 Usage Guide

### For Supervisors

#### Daily Workflow
1. **Login**: Access simplified 4-item navigation
2. **Kanban Board**: View all active work orders in visual columns
3. **Drag Tasks**: Move work orders between departments as they progress
4. **Monitor Issues**: Red badges indicate material shortages or quality issues
5. **Update Status**: Click cards to update task details and add notes

#### Key Features
- **Customer-Friendly Display**: See "Toyota Camping Order" instead of cryptic codes
- **Priority Visualization**: Color-coded cards for urgent orders
- **Progress Tracking**: Visual progress bars show completion percentage
- **Issue Alerts**: Automatic notifications for problems requiring attention

### For Production Staff

#### Task Management
- **My Tasks**: View only tasks assigned to your department
- **Status Updates**: Mark tasks as started, blocked, or completed
- **Time Tracking**: Automatic logging of task duration
- **Notes**: Add production notes and quality observations

#### Quality Control
- **Digital Checklists**: Complete TIS 1238-2564 compliance forms
- **Photo Documentation**: Attach quality photos to work orders
- **Approval Workflow**: Multi-stage quality approval process
- **Non-Conformance**: Document and track quality issues

## 🔧 API Documentation

### Kanban Board Operations

#### Move Task Between Departments
```javascript
POST /api/manufacturing/kanban/move-task
{
    "task_id": 123,
    "from_department": "CUTTING",
    "to_department": "SEWING",
    "user_id": 45
}
```

#### Update Task Status
```javascript
PUT /api/manufacturing/tasks/{id}/status
{
    "status": "IN_PROGRESS",
    "notes": "Started cutting fabric panels"
}
```

#### Get Work Order Details
```javascript
GET /api/manufacturing/work-orders/{id}
```

### Material Shortage API

#### Check Material Availability
```javascript
GET /api/manufacturing/materials/check-availability/{work_order_id}
```

#### Update Material Status
```javascript
PUT /api/manufacturing/materials/{id}/status
{
    "expected_restock_date": "2024-02-20",
    "notes": "Supplier confirmed delivery"
}
```

## 🎨 Customization

### Department Configuration
Modify `Department.php` enum to add new departments:
```php
enum Department: string
{
    case CUTTING = 'cutting';
    case SEWING = 'sewing';
    case QC = 'quality_control';
    case PACKING = 'packing'; // Add new department
}
```

### Kanban Columns
Customize Kanban columns in `WorkOrderKanban.php`:
```php
protected function getColumns(): array
{
    return [
        'QUEUED' => 'Queued',
        'CUTTING' => 'Cutting Department',
        'SEWING' => 'Sewing Department',
        'QC' => 'Quality Control',
        'COMPLETE' => 'Completed',
        // Add custom columns
    ];
}
```

### Quality Checkpoints
Add custom quality checks in `QualityRecord.php`:
```php
public function getQualityCheckpoints(): array
{
    return [
        'fabric_inspection' => 'Fabric Quality Check',
        'seam_strength' => 'Seam Strength Test',
        'waterproof_test' => 'Waterproof Coating Check',
        // Add custom checkpoints
    ];
}
```

## 📊 Dashboard Widgets

### Production Overview
- **Active Orders**: Total work orders in production
- **Overdue Orders**: Orders past promised delivery date
- **Material Shortages**: Items requiring immediate attention
- **Quality Issues**: Non-conformance reports pending

### Department Performance
- **Throughput**: Orders completed per department
- **Cycle Time**: Average time to complete orders
- **Quality Rate**: Percentage of orders passing QC
- **Efficiency**: Resource utilization metrics

## 🔒 Security & Permissions

### Role-Based Access
- **Supervisor**: Full access to department-specific features
- **Production Manager**: Cross-department visibility
- **Quality Manager**: QC-specific permissions
- **Admin**: System-wide configuration access

### Data Protection
- **Audit Trails**: Complete change history
- **User Activity**: Track who made what changes
- **Data Validation**: Input sanitization and validation
- **Backup Strategy**: Automated data backups

## 🧪 Testing

### Unit Tests
```bash
php artisan test --filter=ZerviManufacturing
```

### Feature Tests
```bash
php artisan test --filter=KanbanBoardTest
php artisan test --filter=WorkOrderFlowTest
```

### Browser Tests
```bash
php artisan dusk --filter=ManufacturingDashboardTest
```

## 📈 Performance Optimization

### Database Indexing
- Indexed columns: `work_order_id`, `department`, `status`, `due_date`
- Composite indexes for complex queries
- Full-text search on customer names and PO numbers

### Caching Strategy
- **Query Results**: Cache frequently accessed data
- **View Rendering**: Blade template caching
- **API Responses**: Cache API responses with appropriate TTL

### Queue Management
- **Background Jobs**: Process heavy operations asynchronously
- **Batch Processing**: Handle bulk updates efficiently
- **Priority Queues**: Ensure critical operations complete first

## 🔧 Troubleshooting

### Common Issues

#### Kanban Board Not Loading
1. Check browser console for JavaScript errors
2. Verify API routes are properly registered
3. Ensure Livewire assets are installed

#### Material Shortages Not Showing
1. Verify inventory integration is configured
2. Check material line calculations
3. Ensure expected restock dates are set

#### Department Filtering Not Working
1. Check user department assignment
2. Verify navigation filtering logic
3. Clear application cache

### Debug Mode
Enable debug mode for detailed error messages:
```bash
php artisan config:cache --env=local
```

## 📚 Additional Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [AureusERP Documentation](https://aureuserp.com/docs)

### Community
- [GitHub Issues](https://github.com/zervi/manufacturing/issues)
- [Community Forum](https://community.zervi.com)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/zervi-mes)

## 📄 License

This plugin is licensed under the MIT License. See [LICENSE](LICENSE) file for details.

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📞 Support

- **Email**: support@zervi.com
- **Phone**: +66-2-123-4567
- **Website**: https://zervi.com/support
- **Business Hours**: Mon-Fri 8:00-18:00 ICT

---

**Made with ❤️ by the Zervi Team**