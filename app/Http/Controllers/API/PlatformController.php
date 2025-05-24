<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Services\PlatformService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    protected $platformService;

    public function __construct(PlatformService $platformService)
    {
        $this->platformService = $platformService;
    }

    public function index(Request $request)
    {
        $platforms = $this->platformService->getAllPlatformsWithActiveStatus()['platforms'];

        return ApiResponse::success($platforms, "Platforms retrieved successfully.");
    }

    public function toggleActivePlatform(Request $request, Platform $platform)
    {
        $result = $this->platformService->togglePlatform($platform);
        return ApiResponse::success($result);
    }

}
