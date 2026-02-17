<?php

namespace Zervi\Manufacturing\Enums;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case READY = 'ready';
    case IN_PROGRESS = 'in_progress';
    case QC_REQUIRED = 'qc_required';
    case COMPLETED = 'completed';
    case BLOCKED = 'blocked';
    
    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::READY => 'Ready',
            self::IN_PROGRESS => 'In Progress',
            self::QC_REQUIRED => 'QC Required',
            self::COMPLETED => 'Completed',
            self::BLOCKED => 'Blocked',
        };
    }
    
    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::READY => 'info',
            self::IN_PROGRESS => 'warning',
            self::QC_REQUIRED => 'danger',
            self::COMPLETED => 'success',
            self::BLOCKED => 'danger',
        };
    }
}