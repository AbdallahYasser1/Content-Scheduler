<?php

namespace Database\Seeders;

use App\Enums\PlatformEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Platform;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            [
                "name" => "Twitter",
                "type" => PlatformEnum::TWITTER->value,
                'url' => 'https://twitter.com',
                'character_limit' => 280,
                'is_image_required' => false
            ],
            [
                "name" => "Facebook",
                "type" => PlatformEnum::FACEBOOK->value,
                'url' => 'https://facebook.com',
                'character_limit' => 63206,
                'is_image_required' => false
            ],
            [
                "name" => "Instagram",
                "type" => PlatformEnum::INSTAGRAM->value,
                'url' => 'https://instagram.com',
                'character_limit' => 2200,
                'is_image_required' => true
            ],
            [
                "name" => "LinkedIn",
                "type" => PlatformEnum::LINKEDIN->value,
                'url' => 'https://linkedin.com',
                'character_limit' => 1300,
                'is_image_required' => false
            ],
            [
                "name" => "TikTok",
                "type" => PlatformEnum::TIKTOK->value,
                'url' => 'https://tiktok.com',
                'character_limit' => 1000,
                'is_image_required' => true
            ],
            [
                "name" => "YouTube",
                "type" => PlatformEnum::YOUTUBE->value,
                'url' => 'https://youtube.com',
                'character_limit' => 1000,
                'is_image_required' => true
            ],
            [
                "name" => "Pinterest",
                "type" => PlatformEnum::PINTEREST->value,
                'url' => 'https://pinterest.com',
                'character_limit' => 500,
                'is_image_required' => true
            ],
            [
                "name" => "Reddit",
                "type" => PlatformEnum::REDDIT->value,
                'url' => 'https://reddit.com',
                'character_limit' => 40000,
                'is_image_required' => false
            ],
            [
                "name" => "Snapchat",
                "type" => PlatformEnum::SNAPCHAT->value,
                'url' => 'https://snapchat.com',
                'character_limit' => 80,
                'is_image_required' => true
            ],
        ];

        foreach ($platforms as $platform) {
            Platform::UpdateOrCreate(['type' => $platform['type']], $platform);
        }
    }
}
