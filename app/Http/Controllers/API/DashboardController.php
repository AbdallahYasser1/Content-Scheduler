<?php

namespace App\Http\Controllers\API;

use App\Enums\PostStatusEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Platform;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $analyticsService;


    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $user = Auth::user();
        $usersAnalytics = $this->analyticsService->getUsersAnalytics();
        $postsAnalytics = $this->analyticsService->getPostsAnalytics();
        return ApiResponse::success(
            [
                'postsAnalytics' => $postsAnalytics,
                'usersAnalytics' => $usersAnalytics,
                'activePlatforms' => $user->activePlatforms
            ],
            'Dashboard data retrieved successfully.'
        );
    }
}
