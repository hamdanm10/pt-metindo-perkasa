<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The cartoon animation shown for a step in the process diagram. The drawings live in the
 * `process.step-animation` Blade component. A step without an animation has none.
 */
enum StepAnimation: string
{
    case Degreasing = 'degreasing';
    case Rinsing = 'rinsing';
    case Pickling = 'pickling';
    case Plating = 'plating';
    case Passivation = 'passivation';
    case Drying = 'drying';
    case Baking = 'baking';
    case Inspection = 'inspection';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case): string => $case->value, self::cases());
    }
}
