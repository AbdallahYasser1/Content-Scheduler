<?php

namespace App\Services;

use App\Enums\PostStatusEnum;
use App\Models\Post;
use App\Models\Platform;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    /**
     * Get analytics data for the authenticated user
     *
     * @return array
     */
    public function getPostsAnalytics()
    {
        $user = Auth::user();
        $cacheKey = "user_{$user->id}_analytics";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($user) {
            $postsPerPlatform = DB::table('platform_post')
                ->join('posts', 'platform_post.post_id', '=', 'posts.id')
                ->join('platforms', 'platform_post.platform_id', '=', 'platforms.id')
                ->where('posts.user_id', $user->id)
                ->select('platforms.name', DB::raw('count(*) as post_count'))
                ->groupBy('platforms.name')
                ->get();

            $publishingStats = DB::table('platform_post')
                ->join('posts', 'platform_post.post_id', '=', 'posts.id')
                ->where('posts.user_id', $user->id)
                ->select(
                    DB::raw('count(*) as total'),
                    DB::raw(
                        'sum(case when platform_post.platform_status = "published" then 1 else 0 end) as published'
                    ),
                    DB::raw('sum(case when platform_post.platform_status = "failed" then 1 else 0 end) as failed'),
                    DB::raw('sum(case when platform_post.platform_status = "pending" then 1 else 0 end) as pending')
                )
                ->first();

            $successRate = 0;
            if ($publishingStats->total > 0 && $publishingStats->published > 0) {
                $successRate = round(($publishingStats->published / $publishingStats->total) * 100, 2);
            }

            $postStatusCounts = Post::where('user_id', $user->id)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->toArray();
            $postStatusCounts = array_column($postStatusCounts, 'count', 'status');

            return [
                'posts_per_platform' => $postsPerPlatform,
                'publishing_stats' => [
                    'total' => $publishingStats->total,
                    'published' => $publishingStats->published,
                    'failed' => $publishingStats->failed,
                    'pending' => $publishingStats->pending,
                    'success_rate' => $successRate
                ],
                'post_status_counts' => $postStatusCounts
            ];
        });
    }

    /**
     * Clear user analytics cache
     *
     * @param int|null $userId
     * @return void
     */
    public function clearUserAnalyticsCache($userId = null)
    {
        $userId = $userId ?? Auth::id();
        Cache::forget("user_{$userId}_analytics");
    }

    public function getUsersAnalytics()
    {
        $user = Auth::user();
        $activities = DB::table('activity_logs')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();


        $upcomingPosts = Post::where('user_id', $user->id)
            ->where('status', PostStatusEnum::SCHEDULED->value)
            ->where('scheduled_time', '>', now())
            ->orderBy('scheduled_time', 'asc')
            ->limit(5)
            ->get();

        return [
            'activities' => $activities,
            'upcomingPosts' => $upcomingPosts
        ];
    }
}
