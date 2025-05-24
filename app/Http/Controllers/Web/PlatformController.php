<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;
use App\Services\PlatformService;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    protected $platformService;

    public function __construct(PlatformService $platformService)
    {
        $this->platformService = $platformService;
    }

    public function index(Request $request)
    {
        $data = $this->platformService->getAllPlatformsWithActiveStatus();

        return view('platforms.index', [
            'platforms' => $data['platforms'],
            'activePlatformIds' => $data['activePlatformIds']
        ]);
    }

    public function toggle(Platform $platform)
    {
        $isActive = $this->platformService->togglePlatform($platform);

        $message = $isActive
            ? "Platform '{$platform->name}' activated successfully."
            : "Platform '{$platform->name}' deactivated successfully.";

        return redirect()->route('platforms.index')->with('success', $message);
    }
}
