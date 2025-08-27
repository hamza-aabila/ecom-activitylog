<?php

namespace HamzaAabila\EcomActivitylog\Contracts;

use Spatie\Activitylog\Models\Activity;

interface TimelineFormatter
{
    /**
     * Format an activity for timeline display
     *
     * @param Activity $activity
     * @return array
     */
    public function format(Activity $activity): array;

    /**
     * Format multiple activities for timeline display
     *
     * @param \Illuminate\Support\Collection $activities
     * @return array
     */
    public function formatMany($activities): array;

    /**
     * Build property rows from activity changes
     *
     * @param Activity $activity
     * @return array
     */
    public function buildPropertyRows(Activity $activity): array;

    /**
     * Format a single property change
     *
     * @param string $field
     * @param mixed $oldValue
     * @param mixed $newValue
     * @param array $humanLabels
     * @return array
     */
    public function formatPropertyChange(string $field, $oldValue, $newValue, array $humanLabels = []): array;

    /**
     * Get field metadata (label, icon, color)
     *
     * @param string $field
     * @return array
     */
    public function getFieldMetadata(string $field): array;

    /**
     * Format money values
     *
     * @param mixed $value
     * @param string|null $currency
     * @return string|null
     */
    public function formatMoney($value, ?string $currency = null): ?string;

    /**
     * Format enum values
     *
     * @param mixed $value
     * @param string|null $enumClass
     * @return string|null
     */
    public function formatEnum($value, ?string $enumClass = null): ?string;

    /**
     * Create arrow notation for before/after values
     *
     * @param mixed $from
     * @param mixed $to
     * @return string
     */
    public function createArrow($from, $to): string;
}