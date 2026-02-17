<?php

namespace Zervi\Manufacturing\Enums;

enum Priority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';
    
    public function getLabel(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::NORMAL => 'Normal',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }
    
    public function getColor(): string
    {
        return match($this) {
            self::LOW => 'gray',
            self::NORMAL => 'success',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
        };
    }
}