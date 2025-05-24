<?php

namespace Database\Seeders;

use App\Enums\PostStatusEnum;
use App\Models\Platform;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); // You can use specific user: User::find(1);
        if (!$user) {
            $this->command->error('No users found. Please seed users first.');
            return;
        }

        // Simulate login
        auth()->login($user);

        $postService = App::make(PostService::class);

        $images = [
            'https://media.istockphoto.com/id/814423752/photo/eye-of-model-with-colorful-art-make-up-close-up.jpg?s=612x612&w=0&k=20&c=l15OdMWjgCKycMMShP8UK94ELVlEGvt7GmB_esHWPYE=',
            'https://buffer.com/resources/content/images/size/w2000/2024/11/free-stock-image-sites.png',
            'https://cdn.pixabay.com/photo/2024/05/26/10/15/bird-8788491_1280.jpg',
        ];

        $statuses = [
            PostStatusEnum::DRAFT,
            PostStatusEnum::SCHEDULED,
            PostStatusEnum::PUBLISHED,
        ];

        $platforms = Platform::inRandomOrder()->limit(2)->pluck('id')->toArray(); // adjust as needed

        for ($i = 1; $i <= 20; $i++) {
            $status = $statuses[array_rand($statuses)];
            $image = $images[array_rand($images)];

            $postService->setData([
                'title' => "Sample Post #$i",
                'content' => "This is the content for post number $i.",
                'image_url' => $image,
                'scheduled_time' => $status === PostStatusEnum::SCHEDULED ? now()->addMinutes(rand(10, 120)) : null,
                'status' => $status,
                'platforms' => $platforms,
            ])->createPost();
        }

        $this->command->info('20 posts created using PostService.');
    }
}
