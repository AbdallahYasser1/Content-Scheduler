<?php

namespace App\Traits;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Builder;

trait HasFilterTrait
{
    public function scopeFilter(Builder $query, Filters|string $filters, array $params = []): Builder
    {
        $filters = $this->resolveFilterClass($filters);
        return $filters->apply($query, $params);
    }

    public function resolveFilterClass(Filters|string $filters): Filters
    {
        return is_string($filters) ? app($filters) : $filters;
    }
}