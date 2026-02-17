<?php

namespace Zervi\Manufacturing\Enums;

enum WorkOrderStatus: string
{
    case DRAFT = 'draft';
    case QUEUED = 'queued';
    case IN_PROGRESS = 'in_progress';
    case QC_HOLD = 'qc_hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    
    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::QUEUED => 'Queued',
            self::IN_PROGRESS => 'In Progress',
            self::QC_HOLD => 'QC Hold',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }
    
    public function getColor(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::QUEUED => 'blue',
            self::IN_PROGRESS => 'warning',
            self::QC_HOLD => 'danger',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}