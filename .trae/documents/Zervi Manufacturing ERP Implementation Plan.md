## Zervi Manufacturing MES Implementation Plan - EXECUTION MODE

**Focus: Supervisor-first Kanban workflow for tent manufacturing**

### Phase 1: Foundation Setup (Starting Now)
1. **Setup AureusERP** with base plugins (Inventories, Projects, Employees, Timesheets, Purchases, Contacts)
2. **Create ZerviManufacturing plugin** structure following Aureus conventions
3. **Setup ServiceProvider** with department-scoped navigation filtering

### Phase 2: Database Schema - Supervisor-Focused
1. **Work Orders** with customer commitment fields (promised_delivery_date, sales_order_link)
2. **Work Order Tasks** with blocking reasons (machine_maintenance, material_shortage)
3. **Material Lines** with shortage tracking
4. **Quality Records** with pass/fail and resolution tracking

### Phase 3: Kanban-First UI (Priority #1)
1. **WorkOrderKanban page** - Drag-and-drop board with columns: QUEUED, CUTTING, SEWING, QC, COMPLETE
2. **Supervisor Dashboard** - Active jobs, overdue alerts, blocked tasks widgets
3. **Rich Kanban cards** - Customer name, delivery date, progress bar, priority badge

### Phase 4: Department-Scoped Navigation
1. **Role-based menu filtering** - Supervisors see only 4 items: Dashboard, Production, Inventory, Issues
2. **Department context** - Auto-filter work orders by supervisor's department
3. **Hide ERP complexity** - No Sales, Accounting, HR modules visible to supervisors

### Phase 5: Business Logic
1. **Drag-and-drop workflow** - Move work orders between departments
2. **Issue tracking** - Material shortages, QC failures, machine downtime
3. **Customer commitment visibility** - "Toyota order due Thursday" context

**Ready to start building the supervisor's Kanban board!**