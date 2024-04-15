<?php

namespace App\Models;

use App\Enums\ActivityType;
use App\Enums\WeekScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;

class Activity extends Model
{
    use HasFactory;

    private string $mockedToday = '14-01-2022';
    protected $table = 'activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'starts',
        'ends',
        'from',
        'to',
        'activity_remark',
        'row',
    ];

    /**
     * Scope a query to specific date range.
     *
     * @param Builder $query
     * @param string $from date
     * @return Builder
     */
    public function scopeDateFrom(Builder $query, string $from): Builder
    {
        return $query->where('starts', '>=', $from);
    }

    /**
     * Scope a query to specific date range.
     *
     * @param Builder $query
     * @param string $to date
     * @return Builder
     */
    public function scopeDateTo(Builder $query, string $to): Builder
    {
        return $query->where('starts', '<=', $to);
    }

    /**
     * Scope a query to specific type.
     *
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeType(Builder $query, string $type): Builder //TODO: add enum validation
    {
        return $query->where('type', $type);
    }


    /**
     * Scope a query to specific week, backend calculates the week.
     *
     * @param Builder $query
     * @param string $week
     * @return Builder
     */
    public function scopeWeek(Builder $query, #[ExpectedValues([WeekScope::NEXT_WEEK])] string $week): Builder
    {
        switch ($week) {
            case WeekScope::NEXT_WEEK->value:
                $datesRange = $this->getNextWeekBoundaries($this->mockedToday);
                break;
            default:
                //throw new \Exception('Week scope not supported:'. $week);
                return $query;
        }

        return $query->whereBetween('starts', [$datesRange['from'], $datesRange['to']]);
    }

    /**
     * Get the next week boundaries.
     *
     * @param string $today Current date inf strtotime recognizable format
     * @return array{'from' => 'string', 'to' => 'string'}
     */
    #[ArrayShape(['from' => "string", 'to' => "string"])]
    private function getNextWeekBoundaries(string $today): array
    {
        $todayTimestamp = strtotime($today);

        $nextWeekStart = strtotime('next Monday', $todayTimestamp);
        $nextWeekEnd = strtotime('next Sunday', $nextWeekStart);

        return [
            'from' => date('Y-m-d', $nextWeekStart) . ' 00:00:00',
            'to' => date('Y-m-d', $nextWeekEnd) . ' 23:59:59',
        ];
    }

}
