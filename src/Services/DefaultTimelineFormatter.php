<?php

namespace HamzaAabila\EcomActivitylog\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use HamzaAabila\EcomActivitylog\Contracts\TimelineFormatter;

class DefaultTimelineFormatter implements TimelineFormatter
{
    public function format(Activity $activity): array
    {
        $eventConfig = config("ecom-activitylog.event_types.{$activity->event}", [
            'icon' => 'heroicon-m-document',
            'color' => 'gray',
            'label' => Str::title(str_replace('_', ' ', $activity->event)),
        ]);

        return [
            'id' => $activity->id,
            'event' => $activity->event,
            'description' => $activity->description,
            'causer' => $activity->causer ? [
                'id' => $activity->causer->id,
                'name' => $activity->causer->name ?? 'System',
                'email' => $activity->causer->email ?? null,
            ] : ['name' => 'System'],
            'subject' => $activity->subject ? [
                'id' => $activity->subject->id,
                'type' => class_basename($activity->subject),
                'label' => method_exists($activity->subject, 'getActivityLabel') 
                    ? $activity->subject->getActivityLabel() 
                    : "#{$activity->subject->id}",
            ] : null,
            'properties' => $this->buildPropertyRows($activity),
            'created_at' => [
                'datetime' => $activity->created_at,
                'formatted' => $activity->created_at->format(config('ecom-activitylog.datetime_format', 'd/m/Y H:i:s')),
                'human' => $activity->created_at->diffForHumans(),
                'date' => $activity->created_at->format('Y-m-d'),
            ],
            'meta' => [
                'icon' => $eventConfig['icon'],
                'color' => $eventConfig['color'],
                'label' => $eventConfig['label'],
            ],
        ];
    }

    public function formatMany($activities): array
    {
        $grouped = [];
        
        foreach ($activities as $activity) {
            $formatted = $this->format($activity);
            $date = $formatted['created_at']['date'];
            
            if (!isset($grouped[$date])) {
                $grouped[$date] = [
                    'date' => $date,
                    'formatted_date' => Carbon::parse($date)->format('l, F j, Y'),
                    'activities' => [],
                ];
            }
            
            $grouped[$date]['activities'][] = $formatted;
        }

        return array_values($grouped);
    }

    public function buildPropertyRows(Activity $activity): array
    {
        $changes = method_exists($activity, 'changes') ? $activity->changes() : collect($activity->properties ?? []);
        $attributes = (array) $changes->get('attributes', []);
        $old = (array) $changes->get('old', []);
        $human = (array) data_get($activity->properties, 'human', []);

        return collect($attributes)
            ->map(fn ($new, $field) => $this->formatPropertyChange($field, $old[$field] ?? null, $new, $human))
            ->values()
            ->all();
    }

    public function formatPropertyChange(string $field, $oldValue, $newValue, array $humanLabels = []): array
    {
        $metadata = $this->getFieldMetadata($field);
        
        // Use human labels if available
        $humanOld = $humanLabels["{$field}_old_label"] ?? null;
        $humanNew = $humanLabels["{$field}_label"] ?? null;

        // Auto-format specific field types
        if (Str::contains($field, ['amount', 'price', 'cost', 'fee'])) {
            $humanOld ??= $this->formatMoney($oldValue);
            $humanNew ??= $this->formatMoney($newValue);
        }

        // Handle enum fields
        if (Str::endsWith($field, ['_status', 'status'])) {
            $humanOld ??= $this->formatEnum($oldValue);
            $humanNew ??= $this->formatEnum($newValue);
        }

        return [
            'field' => $field,
            'label' => $metadata['label'],
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'human' => [
                'old' => $humanOld,
                'new' => $humanNew,
            ],
            'formatted' => $this->createArrow($humanOld ?? $oldValue, $humanNew ?? $newValue),
            'icon' => $metadata['icon'],
            'color' => $metadata['color'],
        ];
    }

    public function getFieldMetadata(string $field): array
    {
        return match ($field) {
            'confirmation_status' => ['label' => 'Confirmation', 'color' => 'warning', 'icon' => 'heroicon-m-phone'],
            'status' => ['label' => 'Order Status', 'color' => 'info', 'icon' => 'heroicon-m-check-badge'],
            'city', 'carrier_city_id' => ['label' => 'City', 'color' => 'info', 'icon' => 'heroicon-m-map-pin'],
            'scheduled_at' => ['label' => 'Scheduled At', 'color' => 'info', 'icon' => 'heroicon-m-clock'],
            'amount_collected' => ['label' => 'Amount Collected', 'color' => 'success', 'icon' => 'heroicon-m-banknotes'],
            'order_no' => ['label' => 'Tracking Number', 'color' => 'info', 'icon' => 'heroicon-m-hashtag'],
            'customer_name' => ['label' => 'Customer Name', 'color' => 'gray', 'icon' => 'heroicon-m-user'],
            'phone' => ['label' => 'Phone Number', 'color' => 'gray', 'icon' => 'heroicon-m-phone'],
            'address_line' => ['label' => 'Address', 'color' => 'gray', 'icon' => 'heroicon-m-map'],
            default => [
                'label' => Str::of($field)->replace('_', ' ')->title()->toString(),
                'color' => 'gray',
                'icon' => 'heroicon-m-pencil-square',
            ],
        };
    }

    public function formatMoney($value, ?string $currency = null): ?string
    {
        if ($value === null) {
            return null;
        }

        $config = config('ecom-activitylog.field_formatters.money');
        
        return number_format(
            (float) $value,
            $config['decimal_places'] ?? 2,
            $config['decimal_separator'] ?? ',',
            $config['thousands_separator'] ?? ' '
        ) . ($currency ? " {$currency}" : '');
    }

    public function formatEnum($value, ?string $enumClass = null): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \BackedEnum) {
            return method_exists($value, 'getLabel') ? $value->getLabel() : $value->value;
        }

        // Try to find enum class and get label
        if ($enumClass && enum_exists($enumClass)) {
            $enum = $enumClass::tryFrom((string) $value);
            return $enum && method_exists($enum, 'getLabel') ? $enum->getLabel() : (string) $value;
        }

        return (string) $value;
    }

    public function createArrow($from, $to): string
    {
        $fromFormatted = ($from !== null && $from !== '') ? $from : '—';
        $toFormatted = ($to !== null && $to !== '') ? $to : '—';
        
        return "{$fromFormatted} → {$toFormatted}";
    }
}