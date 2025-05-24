<?php

namespace App\Console\Commands;

use App\Enums\PostStatusEnum;
use Illuminate\Console\Command;
use App\Models\Post;
use App\Jobs\PublishPost;
use Carbon\Carbon;

class ProcessScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled posts and queue them for publishing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing scheduled posts...');

        $duePosts = Post::where('status', PostStatusEnum::SCHEDULED->value)
            ->where('scheduled_time', '<=', now())
            ->with('platforms')
            ->get();

        $count = $duePosts->count();
        $this->info("Found {$count} posts due for publishing");

        foreach ($duePosts as $post) {
            PublishPost::dispatch($post);
            $this->info("Queued post #{$post->id}: {$post->title}");
        }

        $upcomingPosts = Post::where('status', 'scheduled')
            ->where('scheduled_time', '>', now())
            ->where('scheduled_time', '<=', now()->addHour())
            ->with('platforms')
            ->get();

        $upcomingCount = $upcomingPosts->count();
        $this->info("Found {$upcomingCount} upcoming posts for the next hour");

        foreach ($upcomingPosts as $post) {
            $delay = Carbon::parse($post->scheduled_time)->diffInSeconds(now());
            PublishPost::dispatch($post)->delay(now()->addSeconds($delay));
            $this->info("Scheduled post #{$post->id} to publish in {$delay} seconds");
        }

        $this->info('Scheduled posts processing completed');

        return Command::SUCCESS;
    }
}
