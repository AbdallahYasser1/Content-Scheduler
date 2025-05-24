<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RateLimitPosts
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (($request->is('api/posts') || $request->is('posts')) && $request->isMethod('post')) {
            $user = $request->user();

            $todayPostCount = Post::query()
                ->where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->count();

            if ($todayPostCount >= 10) {
                return response()->json([
                    'message' => 'Rate limit exceeded. You can only schedule 10 posts per day.',
                    'posts_scheduled_today' => $todayPostCount,
                    'limit' => 10
                ], 429); // 429 Too Many Requests
            }
        }

        return $next($request);
    }
}
