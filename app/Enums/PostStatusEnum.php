<?php

namespace App\Enums;

enum PostStatusEnum: int
{
    case DRAFT = 0;
    case PUBLISHED = 1;
    case SCHEDULED = 2;

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::SCHEDULED => 'Scheduled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-secondary',
            self::PUBLISHED => 'bg-success',
            self::SCHEDULED => 'bg-info',
        };
    }

    public static function values()
    {
        return [
            self::DRAFT->value,
            self::PUBLISHED->value,
            self::SCHEDULED->value,
        ];
    }

    public static function editableStatus()
    {
        return [
            self::DRAFT->value,
            self::SCHEDULED->value,
        ];
    }
}
