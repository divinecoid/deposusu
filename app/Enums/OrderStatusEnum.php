<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case ON_PROCESS = 'onprocess';
    case ON_PREPARATION = 'onpreparation';
    case PREPARED = 'prepared';
    case ON_DELIVERY = 'ondelivery';
    case DELIVERED = 'delivered';
    case PARTIAL_DELIVERED = 'partialdelivered';
    case DONE = 'done';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    /**
     * Get all enum values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for display
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ON_PROCESS => 'On Process',
            self::ON_PREPARATION => 'On Preparation',
            self::PREPARED => 'Prepared',
            self::ON_DELIVERY => 'On Delivery',
            self::DELIVERED => 'Delivered',
            self::PARTIAL_DELIVERED => 'Partial Delivered',
            self::DONE => 'Done',
            self::CANCELLED => 'Cancelled',
            self::REJECTED => 'Rejected',
        };
    }
}
