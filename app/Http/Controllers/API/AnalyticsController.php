<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Platform;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $usersAnalytics = $this->analyticsService->getUsersAnalytics();
        $postsAnalytics = $this->analyticsService->getPostsAnalytics();

        return ApiResponse::success([$postsAnalytics, $usersAnalytics], "Analytics data retrieved successfully.");
    }
}
