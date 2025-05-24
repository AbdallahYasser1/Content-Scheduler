<?php

namespace App\Http\Controllers\Web;

use App\Enums\PostStatusEnum;
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
        $activePlatforms = $user->activePlatforms;

        return view(
            'dashboard',
            compact(
                'postsAnalytics',
                'usersAnalytics',
                'activePlatforms'
            )
        );
    }
}
