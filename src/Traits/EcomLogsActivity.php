<?php

namespace HamzaAabila\EcomActivitylog\Traits;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

trait EcomLogsActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logName('ecommerce')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Log custom e-commerce event
     */
    public function logEcomEvent(string $event, string $description, array $properties = []): void
    {
        $log = activity('ecommerce')
            ->performedOn($this)
            ->event($event)
            ->withProperties($properties)
            ->log($description);

        if (auth()->check()) {
            $log->causedBy(auth()->user());
        }
    }

    /**
     * Log status change with enum support
     */
    public function logStatusChange(string $field, $oldValue, $newValue, string $description = null): void
    {
        $event = "{$field}_changed";
        $description = $description ?? ucfirst(str_replace('_', ' ', $field)) . ' changed';

        $properties = [
            'attributes' => [$field => $newValue],
            'old' => [$field => $oldValue],
        ];

        // Add human-readable labels for enums
        if ($oldValue instanceof \BackedEnum) {
            $properties['human']["{$field}_old_label"] = method_exists($oldValue, 'getLabel') 
                ? $oldValue->getLabel() 
                : $oldValue->value;
        }

        if ($newValue instanceof \BackedEnum) {
            $properties['human']["{$field}_label"] = method_exists($newValue, 'getLabel') 
                ? $newValue->getLabel() 
                : $newValue->value;
        }

        $this->logEcomEvent($event, $description, $properties);
    }

    /**
     * Log money field change
     */
    public function logMoneyChange(string $field, $oldValue, $newValue, ?string $currency = null): void
    {
        $description = ucfirst(str_replace('_', ' ', $field)) . ' changed';
        
        $properties = [
            'attributes' => [$field => $newValue],
            'old' => [$field => $oldValue],
            'human' => [
                "{$field}_old_label" => $this->formatMoney($oldValue, $currency),
                "{$field}_label" => $this->formatMoney($newValue, $currency),
            ],
        ];

        $this->logEcomEvent("{$field}_changed", $description, $properties);
    }

    /**
     * Log relationship change
     */
    public function logRelationshipChange(string $relation, $oldId, $newId, ?callable $labelResolver = null): void
    {
        $description = ucfirst(str_replace('_', ' ', $relation)) . ' changed';
        
        $properties = [
            'attributes' => ["{$relation}_id" => $newId],
            'old' => ["{$relation}_id" => $oldId],
        ];

        if ($labelResolver) {
            $properties['human'] = [
                "{$relation}_old_label" => $labelResolver($oldId),
                "{$relation}_label" => $labelResolver($newId),
            ];
        }

        $this->logEcomEvent("{$relation}_changed", $description, $properties);
    }

    /**
     * Create arrow notation for changes
     */
    protected function createArrow($from, $to): string
    {
        $fromFormatted = ($from !== null && $from !== '') ? $from : '—';
        $toFormatted = ($to !== null && $to !== '') ? $to : '—';
        
        return "{$fromFormatted} → {$toFormatted}";
    }

    /**
     * Format money values
     */
    protected function formatMoney($value, ?string $currency = null): ?string
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

    /**
     * Get label for activity display
     */
    public function getActivityLabel(): string
    {
        if (method_exists($this, 'getActivityDisplayName')) {
            return $this->getActivityDisplayName();
        }

        $key = $this->getKey();
        
        if (isset($this->customer_name)) {
            return "#{$key} - {$this->customer_name}";
        }

        if (isset($this->name)) {
            return "#{$key} - {$this->name}";
        }

        if (isset($this->title)) {
            return "#{$key} - {$this->title}";
        }

        return "#{$key}";
    }
}