<?php

namespace App\Services;

use App\Models\Platform;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PlatformService
{
    public function getAllPlatformsWithActiveStatus()
    {
        $user = Auth::user();
        $cacheKey = "user_{$user->id}_platforms";

        return Cache::remember($cacheKey, now()->addMinutes(120), function () use ($user) {
            $platforms = Platform::all();
            $activePlatformIds = $user->activePlatforms()->pluck('platforms.id')->toArray();
            foreach ($platforms as $platform) {
                $platform->is_active = in_array($platform->id, $activePlatformIds);
            }
            return [
                'platforms' => $platforms,
                'activePlatformIds' => $activePlatformIds
            ];
        });
    }

    public function togglePlatform(Platform $platform)
    {
        $user = Auth::user();

        $isActive = $user->activePlatforms()->where('platform_id', $platform->id)->exists();

        if ($isActive) {
            $user->activePlatforms()->detach($platform->id);
            $result = false;
        } else {
            $user->activePlatforms()->attach($platform->id);
            $result = true;
        }

        $this->clearUserPlatformsCache($user->id);

        return $result;
    }

    private function clearUserPlatformsCache($userId)
    {
        Cache::forget("user_{$userId}_platforms");
    }
}
