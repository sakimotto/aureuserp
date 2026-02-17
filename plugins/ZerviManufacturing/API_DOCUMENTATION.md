# Zervi Manufacturing MES - API Documentation

## 🚀 API Overview

The Zervi Manufacturing MES provides a comprehensive REST API for managing manufacturing workflows, Kanban operations, and production tracking.

## 🔑 Authentication

All API requests require authentication using Laravel Sanctum tokens.

### Getting API Token
```bash
POST /api/login
{
    "email": "supervisor@zervi.com",
    "password": "your_password"
}
```

### Using API Token
Include the token in the Authorization header:
```
Authorization: Bearer your_api_token
```

## 📋 API Endpoints

### Work Orders

#### Get All Work Orders
```http
GET /api/manufacturing/work-orders
```

**Parameters:**
- `department` (optional): Filter by department (cutting, sewing, qc)
- `status` (optional): Filter by status (queued, in_progress, completed)
- `priority` (optional): Filter by priority (low, medium, high, urgent)
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
    "data": [
        {
            "id": 123,
            "work_order_number": "WO-2024-001",
            "customer_po_number": "PO-TOYOTA-456",
            "customer_name": "Toyota Camping Equipment",
            "promised_delivery_date": "2024-02-25",
            "department": "cutting",
            "status": "in_progress",
            "priority": "urgent",
            "progress_percentage": 45,
            "total_tasks": 8,
            "completed_tasks": 3,
            "material_shortages": 2,
            "created_at": "2024-02-15T10:30:00Z",
            "updated_at": "2024-02-15T14:45:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 73
    }
}
```

#### Get Single Work Order
```http
GET /api/manufacturing/work-orders/{id}
```

**Response:**
```json
{
    "data": {
        "id": 123,
        "work_order_number": "WO-2024-001",
        "customer_po_number": "PO-TOYOTA-456",
        "customer_name": "Toyota Camping Equipment",
        "customer_contact": "John Smith",
        "promised_delivery_date": "2024-02-25",
        "department": "cutting",
        "status": "in_progress",
        "priority": "urgent",
        "progress_percentage": 45,
        "total_tasks": 8,
        "completed_tasks": 3,
        "material_shortages": 2,
        "notes": "Customer requested expedited delivery",
        "tasks": [
            {
                "id": 456,
                "name": "Cut fabric panels",
                "department": "cutting",
                "status": "completed",
                "assigned_to": "Jane Doe",
                "started_at": "2024-02-15T08:00:00Z",
                "completed_at": "2024-02-15T12:00:00Z",
                "duration_hours": 4
            }
        ],
        "material_lines": [
            {
                "id": 789,
                "product_name": "Waterproof Fabric - Blue",
                "required_quantity": 50,
                "available_quantity": 30,
                "shortage": 20,
                "expected_restock_date": "2024-02-18",
                "unit": "meters"
            }
        ],
        "created_at": "2024-02-15T10:30:00Z",
        "updated_at": "2024-02-15T14:45:00Z"
    }
}
```

#### Create Work Order
```http
POST /api/manufacturing/work-orders
```

**Request Body:**
```json
{
    "sales_order_line_id": 456,
    "customer_po_number": "PO-TOYOTA-789",
    "promised_delivery_date": "2024-03-01",
    "department": "cutting",
    "priority": "high",
    "notes": "Rush order for trade show",
    "tasks": [
        {
            "name": "Cut fabric panels",
            "department": "cutting",
            "estimated_hours": 4
        },
        {
            "name": "Sew main body",
            "department": "sewing",
            "estimated_hours": 8
        }
    ]
}
```

#### Update Work Order
```http
PUT /api/manufacturing/work-orders/{id}
```

**Request Body:**
```json
{
    "promised_delivery_date": "2024-03-05",
    "priority": "urgent",
    "notes": "Updated delivery date per customer request"
}
```

#### Delete Work Order
```http
DELETE /api/manufacturing/work-orders/{id}
```

### Tasks

#### Get Work Order Tasks
```http
GET /api/manufacturing/work-orders/{work_order_id}/tasks
```

**Response:**
```json
{
    "data": [
        {
            "id": 456,
            "name": "Cut fabric panels",
            "department": "cutting",
            "status": "in_progress",
            "assigned_to": "Jane Doe",
            "assigned_user_id": 23,
            "estimated_hours": 4,
            "actual_hours": 2.5,
            "started_at": "2024-02-15T08:00:00Z",
            "completed_at": null,
            "blocked_by": [],
            "blocking": [789],
            "notes": "Using new cutting machine",
            "created_at": "2024-02-15T07:30:00Z",
            "updated_at": "2024-02-15T08:30:00Z"
        }
    ]
}
```

#### Update Task Status
```http
PUT /api/manufacturing/tasks/{id}/status
```

**Request Body:**
```json
{
    "status": "completed",
    "notes": "All panels cut successfully",
    "actual_hours": 3.5
}
```

**Status Options:** `queued`, `in_progress`, `blocked`, `completed`

#### Assign Task to User
```http
PUT /api/manufacturing/tasks/{id}/assign
```

**Request Body:**
```json
{
    "user_id": 23,
    "notes": "Assigned to most experienced cutter"
}
```

### Kanban Operations

#### Get Kanban Board Data
```http
GET /api/manufacturing/kanban/board
```

**Parameters:**
- `department` (optional): Filter by department

**Response:**
```json
{
    "data": {
        "columns": [
            {
                "id": "queued",
                "name": "Queued",
                "color": "#6B7280",
                "work_orders": [
                    {
                        "id": 123,
                        "work_order_number": "WO-2024-001",
                        "customer_name": "Toyota Camping Equipment",
                        "customer_po_number": "PO-TOYOTA-456",
                        "promised_delivery_date": "2024-02-25",
                        "priority": "urgent",
                        "progress_percentage": 0,
                        "material_shortages": 0,
                        "days_until_due": 5
                    }
                ]
            },
            {
                "id": "cutting",
                "name": "Cutting Department",
                "color": "#3B82F6",
                "work_orders": [
                    {
                        "id": 124,
                        "work_order_number": "WO-2024-002",
                        "customer_name": "Outdoor Adventures",
                        "customer_po_number": "PO-OA-789",
                        "promised_delivery_date": "2024-02-28",
                        "priority": "high",
                        "progress_percentage": 25,
                        "material_shortages": 1,
                        "days_until_due": 8
                    }
                ]
            }
        ]
    }
}
```

#### Move Task Between Departments
```http
POST /api/manufacturing/kanban/move-task
```

**Request Body:**
```json
{
    "work_order_id": 123,
    "from_department": "queued",
    "to_department": "cutting",
    "user_id": 23,
    "notes": "Started production process"
}
```

### Materials

#### Check Material Availability
```http
GET /api/manufacturing/work-orders/{work_order_id}/materials
```

**Response:**
```json
{
    "data": [
        {
            "id": 789,
            "product_id": 456,
            "product_name": "Waterproof Fabric - Blue",
            "product_sku": "FAB-BLU-001",
            "required_quantity": 50,
            "available_quantity": 30,
            "shortage": 20,
            "shortage_percentage": 40,
            "expected_restock_date": "2024-02-18",
            "unit": "meters",
            "unit_cost": 12.50,
            "total_cost": 625.00,
            "supplier": "Thai Textile Co.",
            "notes": "Order placed, awaiting delivery"
        }
    ],
    "summary": {
        "total_materials": 8,
        "materials_with_shortages": 2,
        "total_shortage_cost": 1250.00,
        "can_proceed": false
    }
}
```

#### Update Material Status
```http
PUT /api/manufacturing/materials/{id}/status
```

**Request Body:**
```json
{
    "expected_restock_date": "2024-02-20",
    "notes": "Supplier confirmed delivery date",
    "supplier_id": 123
}
```

### Quality Records

#### Get Quality Records
```http
GET /api/manufacturing/work-orders/{work_order_id}/quality-records
```

**Response:**
```json
{
    "data": [
        {
            "id": 456,
            "checkpoint": "fabric_inspection",
            "checkpoint_name": "Fabric Quality Check",
            "status": "passed",
            "inspector": "Quality Inspector Jane",
            "inspector_id": 34,
            "inspection_date": "2024-02-15",
            "notes": "Fabric meets all specifications",
            "photos": [
                "https://example.com/photos/fabric-check-1.jpg"
            ],
            "measurements": {
                "tensile_strength": "45.2 MPa",
                "water_resistance": "5000mm"
            },
            "compliance_standard": "TIS 1238-2564",
            "created_at": "2024-02-15T09:30:00Z",
            "updated_at": "2024-02-15T09:30:00Z"
        }
    ]
}
```

#### Create Quality Record
```http
POST /api/manufacturing/quality-records
```

**Request Body:**
```json
{
    "work_order_id": 123,
    "checkpoint": "seam_strength",
    "status": "passed",
    "inspector_id": 34,
    "notes": "All seams meet strength requirements",
    "measurements": {
        "seam_strength": "85 N/cm"
    },
    "photos": [
        "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQ..."
    ]
}
```

### Dashboard

#### Get Dashboard Statistics
```http
GET /api/manufacturing/dashboard/stats
```

**Response:**
```json
{
    "data": {
        "overview": {
            "active_orders": 45,
            "overdue_orders": 3,
            "completed_today": 8,
            "material_shortages": 12
        },
        "department_performance": {
            "cutting": {
                "active_orders": 12,
                "completed_today": 3,
                "average_cycle_time": 2.5,
                "efficiency": 87
            },
            "sewing": {
                "active_orders": 18,
                "completed_today": 4,
                "average_cycle_time": 4.2,
                "efficiency": 92
            },
            "qc": {
                "active_orders": 8,
                "completed_today": 2,
                "average_cycle_time": 1.1,
                "efficiency": 95
            }
        },
        "priority_breakdown": {
            "urgent": 5,
            "high": 12,
            "medium": 23,
            "low": 5
        }
    }
}
```

## 📊 Real-time Updates

### WebSocket Integration
The API supports real-time updates via WebSocket connections for:
- Kanban board changes
- Task status updates
- Material shortage alerts
- Quality record updates

### WebSocket Events
```javascript
// Subscribe to work order updates
Echo.channel('manufacturing.work-orders')
    .listen('WorkOrderUpdated', (e) => {
        console.log('Work order updated:', e.work_order);
    });

// Subscribe to task updates
Echo.channel('manufacturing.tasks')
    .listen('TaskStatusChanged', (e) => {
        console.log('Task status changed:', e.task);
    });
```

## 🔧 Error Handling

### Standard Error Response
```json
{
    "message": "Validation error",
    "errors": {
        "promised_delivery_date": ["The promised delivery date must be a date after today."]
    }
}
```

### Common HTTP Status Codes
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error

## 📈 Rate Limiting

API requests are rate-limited to:
- **Standard endpoints**: 60 requests per minute
- **File upload endpoints**: 10 requests per minute
- **Export endpoints**: 5 requests per minute

## 🔍 Pagination

All list endpoints support pagination:

**Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

**Response includes:**
```json
{
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 73,
        "from": 1,
        "to": 15
    }
}
```

## 🧪 Testing Examples

### Using cURL
```bash
# Get work orders
curl -X GET "http://localhost:8000/api/manufacturing/work-orders" \
  -H "Authorization: Bearer your_api_token" \
  -H "Accept: application/json"

# Update task status
curl -X PUT "http://localhost:8000/api/manufacturing/tasks/123/status" \
  -H "Authorization: Bearer your_api_token" \
  -H "Content-Type: application/json" \
  -d '{"status": "completed", "notes": "Task finished"}'
```

### Using JavaScript (Fetch)
```javascript
// Get Kanban board data
fetch('/api/manufacturing/kanban/board', {
    headers: {
        'Authorization': 'Bearer ' + apiToken,
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data));

// Move task in Kanban
fetch('/api/manufacturing/kanban/move-task', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + apiToken,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        work_order_id: 123,
        from_department: 'queued',
        to_department: 'cutting',
        user_id: 23
    })
});
```

### Using PHP (Guzzle)
```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'http://localhost:8000/api/',
    'headers' => [
        'Authorization' => 'Bearer ' . $apiToken,
        'Accept' => 'application/json'
    ]
]);

// Get work orders
$response = $client->get('manufacturing/work-orders');
$workOrders = json_decode($response->getBody(), true);

// Update task status
$response = $client->put('manufacturing/tasks/123/status', [
    'json' => [
        'status' => 'completed',
        'notes' => 'Task finished successfully'
    ]
]);
```

## 📚 SDK and Libraries

### PHP SDK (Coming Soon)
```php
use Zervi\Manufacturing\ApiClient;

$client = new ApiClient('your_api_token');
$workOrders = $client->workOrders()->getAll();
$client->tasks()->updateStatus(123, 'completed');
```

### JavaScript SDK (Coming Soon)
```javascript
import { ManufacturingClient } from '@zervi/manufacturing-api';

const client = new ManufacturingClient('your_api_token');
const workOrders = await client.workOrders.getAll();
await client.tasks.updateStatus(123, 'completed');
```

---

**API Version**: 1.0.0  
**Last Updated**: February 2024  
**Support**: api-support@zervi.com