<?php

namespace App\Filters;

class PostFilter extends Filters
{
    public $filters = ['status', 'scheduled_time'];

    protected function status($status)
    {
        return $this->builder->where('status', $status);
    }

    protected function scheduledTime($status)
    {
        return $this->builder->whereDate('scheduled_time', $status);
    }
}