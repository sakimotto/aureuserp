# Zervi-MRP-Dash-aureuserp

**ZERVI MANUFACTURING ERP - DEVELOPMENT SCOPE & STRICT INSTRUCTIONS**

**Project**: Extend AureusERP with Manufacturing Modules\n**Foundation**: AureusERP (Laravel 11 + FilamentPHP 4)\n**Approach**: Modular Extension (Do Not Modify Core)


---

## SCOPE BOUNDARIES

**What We Build**:


1. **Work Order Management** (manufacturing jobs with routing)
2. **Shop Floor Tasks** (department operations with time tracking)
3. **Material Planning** (MRP lines linked to Aureus Inventory)
4. **Quality Control** (inspections, TIS compliance, NCRs)
5. **Simple CRM** (Leads/Quotes bridging to Projects)

**What We DO NOT Build**:

* Authentication (use Aureus)
* User management (use Aureus)
* Inventory management (use Aureus Inventories plugin)
* Accounting (use Aureus Accounting)
* HR/Payroll (use Aureus Employees)


---

## STRICT DEVELOPMENT RULES

**Rule 1: Core is Sacred**

* NEVER edit `/vendor` or Aureus core files
* NEVER modify existing Aureus migrations
* Use Extension patterns: ServiceProviders, Events, Contracts
* All code lives in `/plugins/ZerviManufacturing/`

**Rule 2: Copy-Paste-Modify Pattern**

* For every feature, find an existing Aureus equivalent first
* Copy their pattern exactly, then modify for manufacturing
* Examples:
  * Work Order CRUD → Copy Projects resource structure
  * Task relations → Copy Timesheet entry patterns
  * Material lines → Copy Purchase Order line patterns

**Rule 3: One Module = One Branch = One Test**

* Complete Work Orders fully before touching Quality
* Each module must be demo-able before next starts
* If stuck >30 minutes, STOP and ask

**Rule 4: Database Discipline**

* All tables prefixed: `zervi_` (e.g., `zervi_work_orders`)
* Foreign keys must use `constrained()` with proper table names
* Soft deletes on ALL manufacturing tables
* Timestamps mandatory

**Rule 5: No Frontend Creativity**

* Use Filament components ONLY (Forms\\Components\*, Tables\\Columns\*)
* No custom Blade templates unless approved
* No custom CSS/JS
* Mobile responsiveness is Filament's job, not yours

**Rule 6: Business Logic in Models**

* Controllers must be thin (resource routes only)
* Validation in FormRequest classes
* Complex logic in Model methods or Actions
* Reuse Aureus services (InventoryService, etc.)


---

## PHASED EXECUTION PLAN

**Phase 1: Foundation (Days 1-2)**

* Install Aureus + Plugins (Inventory, Projects, Employees, Timesheets)
* Create plugin structure: `php artisan make:plugin ZerviManufacturing`
* Setup migrations folder
* Create base ServiceProvider
* **Deliverable**: Admin panel loads, plugin registered, no errors

**Phase 2: Work Orders (Days 3-5)**

* Migration: `zervi_work_orders` table
* Model: WorkOrder with relations to Project/Product
* Filament Resource: WorkOrderResource (CRUD only)
* Relation Manager: Simple Tasks list (no logic yet)
* **Deliverable**: Can create/edit Work Orders, link to Projects

**Phase 3: Task Operations (Days 6-8)**

* Migration: `zervi_work_order_tasks`
* Model: Task with time tracking fields
* Department enum (cutting, lamination, sewing, etc.)
* Relation manager with sequence ordering
* **Deliverable**: Can add tasks to Work Order, reorder sequence

**Phase 4: Material Lines (Days 9-10)**

* Migration: `zervi_material_lines`
* Link to Aureus Products (raw materials)
* Basic allocation logic
* **Deliverable**: Material list visible on Work Order

**Phase 5: Quality Module (Days 11-13)**

* Migration: `zervi_quality_records`
* Inspection types (incoming, in-process, final)
* TIS 1238-2564 checklist template
* **Deliverable**: Can record QC results against Work Orders

**Phase 6: CRM Bridge (Days 14-16)**

* Leads table (extends Contacts)
* Quote generation
* "Convert to Work Order" action button
* **Deliverable**: Lead → Quote → Work Order flow works

**Phase 7: Shop Floor UI (Days 17-18)**

* Simple barcode scan endpoint
* Shop floor dashboard (tablet view)
* Start/Stop task buttons
* **Deliverable**: Operator can scan barcode, see tasks, log time


---

## FILE STRUCTURE (Strict)

```
/plugins/ZerviManufacturing/
├── database/
│   └── migrations/ (timestamped, prefixed zervi_)
├── src/
│   ├── Models/ (WorkOrder, Task, MaterialLine, QualityRecord)
│   ├── Filament/
│   │   ├── Resources/ (WorkOrderResource, QualityResource)
│   │   └── Resources/RelationManagers/ (Tasks, Materials, Quality)
│   ├── Http/Controllers/ (BarcodeController only)
│   └── Providers/
│       └── ZerviManufacturingServiceProvider.php
├── routes/
│   └── web.php (minimal, shop floor routes only)
└── config/
    └── zervi-manufacturing.php
```


---

## WHEN TO STOP & ASK

**STOP if**:

* You need to modify Aureus core files to make it work (you're doing it wrong)
* A migration fails and you don't know why (don't delete migrations, ask)
* You find yourself writing more than 50 lines of custom JavaScript
* You can't figure out how to link to existing Aureus models in 15 minutes
* You want to create a "base class" or "utility trait" (YAGNI, keep it simple)

**PROCEED if**:

* You're copying an existing Aureus pattern exactly
* You're extending via ServiceProvider hooks
* Test data saves successfully in Tinker
* Filament forms load without errors


---

## ACCEPTANCE CRITERIA

**Definition of Done per Phase**:


1. Code committed to Git with clear message
2. Migrations run successfully on fresh install
3. Can demonstrate feature with test data
4. No console errors (PHP or JS)
5. Mobile view works (Filament responsive)
6. Passes `php artisan pint` (Laravel code style)

**Final Success Metrics**:

* Create Work Order from Project in <30 seconds
* Add 5 tasks with different departments
* Record material consumption
* Complete QC checklist
* Convert Lead to Work Order in one click


---

**Begin Phase 1. Do not proceed to Phase 2 until Phase 1 is committed and tested.**