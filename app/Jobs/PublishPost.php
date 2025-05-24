<?php

namespace App\Jobs;

use App\Enums\PostStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use App\Services\PostService;

class PublishPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The post instance.
     *
     * @var \App\Models\Post
     */
    protected $post;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Post $post
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\PostService $postService
     * @return void
     */
    public function handle(PostService $postService)
    {
        $this->post->refresh();

        if ($this->post->status !== PostStatusEnum::PUBLISHED
            && ($this->post->scheduled_time <= now() || is_null($this->post->scheduled_time))) {
            $postService->publishPost($this->post);
        }
    }
}
