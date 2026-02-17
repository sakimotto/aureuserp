<x-filament::page>
    <div class="kanban-board">
        <div class="grid grid-cols-5 gap-4 h-full">
            @foreach($columns as $columnKey => $column)
                <div class="kanban-column bg-gray-50 rounded-lg p-4"
                     ondrop="drop(event, '{{ $columnKey }}')"
                     ondragover="allowDrop(event)">
                    
                    {{-- Column Header --}}
                    <div class="column-header mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $column['title'] }}
                            <span class="ml-2 bg-{{ $column['color'] }}-100 text-{{ $column['color'] }}-800 text-sm px-2 py-1 rounded-full">
                                {{ count($column['work_orders']) }}
                            </span>
                        </h3>
                    </div>

                    {{-- Work Orders Cards --}}
                    <div class="space-y-3 min-h-96">
                        @foreach($column['work_orders'] as $workOrder)
                            <div class="kanban-card bg-white rounded-lg shadow-sm border border-gray-200 p-4 cursor-move"
                                 draggable="true"
                                 ondragstart="drag(event, {{ $workOrder->id }}, '{{ $columnKey }}')"
                                 wire:click="viewWorkOrderDetails({{ $workOrder->id }})">
                                
                                {{-- Card Header --}}
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-mono text-sm font-semibold text-gray-700">
                                        {{ $workOrder->wo_number }}
                                    </span>
                                    <span class="priority-badge bg-{{ $workOrder->priority->getColor() }}-100 text-{{ $workOrder->priority->getColor() }}-800 text-xs px-2 py-1 rounded">
                                        {{ $workOrder->priority->getLabel() }}
                                    </span>
                                </div>

                                {{-- Product Info --}}
                                <div class="mb-2">
                                    <h4 class="font-semibold text-gray-800 text-sm">
                                        {{ $workOrder->product->name }}
                                    </h4>
                                    <p class="text-xs text-gray-600">
                                        {{ $workOrder->quantity_requested }} pcs
                                    </p>
                                </div>

                                {{-- Customer Commitment --}}
                                @if($workOrder->customer_commitment['customer_name'] !== 'Stock')
                                    <div class="mb-2 p-2 bg-blue-50 rounded text-xs">
                                        <div class="font-semibold text-blue-800">
                                            🏢 {{ $workOrder->customer_commitment['customer_name'] }}
                                        </div>
                                        <div class="text-blue-600">
                                            📅 Due: {{ $workOrder->customer_commitment['delivery_date']?->format('M d') }}
                                        </div>
                                        @if($workOrder->customer_commitment['is_overdue'])
                                            <div class="text-red-600 font-semibold">
                                                ⚠️ OVERDUE
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Progress Bar --}}
                                <div class="mb-2">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ number_format($workOrder->progress_percentage, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $workOrder->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                {{-- Issue Flags --}}
                                @if($workOrder->active_issues['material_shortage'] || 
                                     $workOrder->active_issues['qc_failed'] || 
                                     $workOrder->active_issues['blocked_tasks'])
                                    <div class="space-y-1">
                                        @if($workOrder->active_issues['material_shortage'])
                                            <div class="text-xs text-red-600 font-semibold">
                                                🚨 Material Shortage
                                            </div>
                                        @endif
                                        @if($workOrder->active_issues['qc_failed'])
                                            <div class="text-xs text-red-600 font-semibold">
                                                ❌ QC Failed
                                            </div>
                                        @endif
                                        @if($workOrder->active_issues['blocked_tasks'])
                                            <div class="text-xs text-red-600 font-semibold">
                                                ⏸️ Blocked Tasks
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="flex space-x-2 mt-3">
                                    @if($columnKey === 'queued' && $workOrder->status === 'queued')
                                        <button 
                                            wire:click="startWorkOrder({{ $workOrder->id }})"
                                            class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                                            Start
                                        </button>
                                    @endif
                                    <button 
                                        wire:click="viewWorkOrderDetails({{ $workOrder->id }})"
                                        class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                        Details
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Drag & Drop JavaScript --}}
    <script>
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev, workOrderId, fromColumn) {
            ev.dataTransfer.setData("workOrderId", workOrderId);
            ev.dataTransfer.setData("fromColumn", fromColumn);
            ev.target.style.opacity = "0.5";
        }

        function drop(ev, toColumn) {
            ev.preventDefault();
            
            const workOrderId = ev.dataTransfer.getData("workOrderId");
            const fromColumn = ev.dataTransfer.getData("fromColumn");
            
            if (fromColumn !== toColumn) {
                // Dispatch Livewire event
                Livewire.dispatch('work-order-moved', {
                    workOrderId: workOrderId,
                    fromColumn: fromColumn,
                    toColumn: toColumn
                });
            }
            
            // Reset opacity
            const draggedElement = document.querySelector(`[draggable][ondragstart*="${workOrderId}"]`);
            if (draggedElement) {
                draggedElement.style.opacity = "1";
            }
        }
    </script>

    <style>
        .kanban-column {
            min-height: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .kanban-card {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }
        
        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-left-color: #3b82f6;
        }
        
        .kanban-card[draggable]:hover {
            cursor: grab;
        }
        
        .kanban-card[draggable]:active {
            cursor: grabbing;
        }
        
        .priority-badge {
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .column-header {
            position: sticky;
            top: 0;
            background: #f9fafb;
            z-index: 10;
            padding-bottom: 1rem;
        }
    </style>
</x-filament::page>