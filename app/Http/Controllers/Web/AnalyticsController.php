<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    /**
     * Create a new controller instance.
     *
     * @param AnalyticsService $analyticsService
     * @return void
     */
    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display analytics dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $analytics = $this->analyticsService->getPostsAnalytics();
        
        return view('analytics.index', compact('analytics'));
    }
}
