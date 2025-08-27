<?php

namespace HamzaAabila\EcomActivitylog\Filament\Table\Actions;

use Filament\Tables\Actions\Action;
use HamzaAabila\EcomActivitylog\Contracts\TimelineFormatter;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Model;

class TimelineAction extends Action
{
    protected int $limit = 50;

    public static function getDefaultName(): ?string
    {
        return 'timeline';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('ecom-activitylog::actions.timeline.label'))
            ->icon('heroicon-m-clock')
            ->color('gray')
            ->modalHeading(__('ecom-activitylog::actions.timeline.modal_heading'))
            ->modalWidth('5xl')
            ->modalContent(function (Model $record) {
                return $this->getTimelineView($record);
            });
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    protected function getTimelineView(Model $record): \Illuminate\Contracts\View\View
    {
        $activities = Activity::forSubject($record)
            ->with('causer')
            ->latest()
            ->limit($this->limit)
            ->get();

        $formatter = app(TimelineFormatter::class);
        $timeline = $formatter->formatMany($activities);

        return view('ecom-activitylog::filament.timeline-modal', [
            'timeline' => $timeline,
            'record' => $record,
            'total_activities' => Activity::forSubject($record)->count(),
            'showing_count' => $activities->count(),
        ]);
    }
}