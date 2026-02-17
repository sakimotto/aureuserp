<?php

namespace Zervi\Manufacturing\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Zervi\Manufacturing\Models\WorkOrder;
use Zervi\Manufacturing\Filament\Resources\WorkOrderResource\Pages;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Work Order Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('wo_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'WO-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))
                            ->disabled(),
                            
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        Forms\Components\TextInput::make('quantity_requested')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                            
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal', 
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('normal')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Customer Commitment')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('sales_order_line_id')
                            ->relationship('salesOrderLine', 'id')
                            ->searchable()
                            ->preload()
                            ->label('Sales Order Line'),
                            
                        Forms\Components\DatePicker::make('promised_delivery_date')
                            ->label('Promised Delivery'),
                            
                        Forms\Components\TextInput::make('customer_po_number')
                            ->label('Customer PO #'),
                    ]),

                Forms\Components\Section::make('Scheduling')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('planned_start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('planned_end_date')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Department Assignment')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('owning_department_id')
                            ->relationship('owningDepartment', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Owning Department'),
                            
                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Assigned Supervisor'),
                    ]),

                Forms\Components\Section::make('Tracking')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('batch_number')
                            ->placeholder('Auto-generated if empty'),
                        Forms\Components\TextInput::make('lot_number'),
                        Forms\Components\TextInput::make('barcode')
                            ->unique(ignoreRecord: true),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'queued' => 'Queued',
                                'in_progress' => 'In Progress',
                                'qc_hold' => 'QC Hold',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                            
                        Forms\Components\Select::make('current_department')
                            ->label('Current Department')
                            ->options([
                                'queued' => 'Queued',
                                'cutting' => 'Cutting (CNC)',
                                'sewing' => 'Sewing (Tent)',
                                'qc' => 'Quality Control',
                                'complete' => 'Complete',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->label('Product'),
                    
                Tables\Columns\TextColumn::make('quantity_requested')
                    ->numeric()
                    ->label('Qty'),
                    
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'danger' => 'urgent',
                        'warning' => 'high',
                        'success' => 'normal',
                        'gray' => 'low',
                    ]),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => ['draft', 'qc_hold'],
                        'success' => 'completed',
                        'primary' => 'in_progress',
                        'secondary' => ['queued', 'draft'],
                    ]),
                    
                Tables\Columns\BadgeColumn::make('current_department')
                    ->label('Department'),
                    
                Tables\Columns\TextColumn::make('promised_delivery_date')
                    ->date()
                    ->label('Due Date')
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null),
                    
                Tables\Columns\TextColumn::make('customer_commitment.customer_name')
                    ->label('Customer'),
                    
                Tables\Columns\ProgressBarColumn::make('progress_percentage')
                    ->label('Progress'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('current_department')
                    ->label('Department'),
                Tables\Filters\SelectFilter::make('priority'),
                Tables\Filters\TernaryFilter::make('overdue')
                    ->queries(
                        true: fn ($query) => $query->overdue(),
                        false: fn ($query) => $query->where(function ($q) {
                            $q->whereNull('promised_delivery_date')
                              ->orWhere('promised_delivery_date', '>=', now()->startOfDay());
                        }),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_kanban')
                    ->label('View in Kanban')
                    ->icon('heroicon-o-view-columns')
                    ->url(fn ($record) => route('filament.admin.pages.work-order-kanban')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
            'kanban' => WorkOrderKanban::route('/kanban'),
        ];
    }
}