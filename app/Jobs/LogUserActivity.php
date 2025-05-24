<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

use function PHPUnit\Framework\isInstanceOf;

class LogUserActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;
    protected string $actionType;
    protected string $description;
    protected $resource;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $actionType, string $description, $resource = null)
    {
        $this->userId = $userId;
        $this->actionType = $actionType;
        $this->description = $description;
        $this->resource = $resource;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $log = new ActivityLog([
            'user_id' => $this->userId,
            'action_type' => $this->actionType,
            'description' => $this->description,
        ]);

        if ($this->resource) {
            $log->resource()->associate($this->resource);
        }

        if ($this->resource instanceof \App\Models\Post) {
            Cache::forget("user_{$this->userId}_analytics");
        }


        $log->save();
    }
}
