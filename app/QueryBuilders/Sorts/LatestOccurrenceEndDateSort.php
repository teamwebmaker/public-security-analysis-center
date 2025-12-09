<?php

namespace App\QueryBuilders\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class LatestOccurrenceEndDateSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'desc' : 'asc';

        $query->orderByRaw("(select end_date from task_occurrences where task_occurrences.task_id = tasks.id order by created_at desc, id desc limit 1) {$direction}");
    }
}
