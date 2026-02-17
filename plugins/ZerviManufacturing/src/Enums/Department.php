<?php

namespace Zervi\Manufacturing\Enums;

enum Department: string
{
    case QUEUED = 'queued';
    case CUTTING = 'cutting';
    case SEWING = 'sewing';
    case QC = 'qc';
    case COMPLETE = 'complete';
    
    public function getLabel(): string
    {
        return match($this) {
            self::QUEUED => 'Queued',
            self::CUTTING => 'Cutting (CNC)',
            self::SEWING => 'Sewing (Tent)',
            self::QC => 'Quality Control',
            self::COMPLETE => 'Complete',
        };
    }
    
    public function getColor(): string
    {
        return match($this) {
            self::QUEUED => 'gray',
            self::CUTTING => 'blue',
            self::SEWING => 'green',
            self::QC => 'yellow',
            self::COMPLETE => 'success',
        };
    }
}