<?php

namespace App\Services;

use App\Enums\ActivityActionTypeEnum;
use App\Enums\PostStatusEnum;
use App\FIlters\PostFilter;
use App\Jobs\LogUserActivity;
use App\Jobs\PublishPost;
use App\Models\Post;
use App\Models\Platform;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class PostService
{
    private $data;
    private $post;

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setPost(Post $post)
    {
        $this->post = $post;
        return $this;
    }

    public function getUserPosts()
    {
        $user = Auth::user();
        return $user->posts()
            ->with('platforms')
            ->filter(PostFilter::class)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function createPost()
    {
        $this->post = Auth::user()->posts()->create([
            'title' => $this->data['title'],
            'content' => $this->data['content'],
            'image_url' => $this->data['image_url'] ?? null,
            'scheduled_time' => $this->data['scheduled_time'] ?? null,
            'status' => $this->data['status'] ?? PostStatusEnum::DRAFT,
        ]);

        $platformData = [];

        foreach ($this->data['platforms'] as $platformId) {
            $platformData[$platformId] = ['platform_status' => 'pending'];
        }

        $this->post->platforms()->sync($platformData);

        if ($this->post->status == PostStatusEnum::SCHEDULED) {
            $delay = Carbon::parse($this->post->scheduled_time)->diffInSeconds(now(), true);
            PublishPost::dispatch($this->post)->delay($delay);
        }

        LogUserActivity::dispatch(
            Auth::id(),
            ActivityActionTypeEnum::CREATE->value,
            'Created a new post: ' . $this->post->title,
            $this->post
        );
        return $this->post;
    }

    public function updatePost()
    {
        $updateData = [
            'title' => $this->data['title'],
            'content' => $this->data['content'],
            'scheduled_time' => $this->data['scheduled_time'] ?? null,
            'image_url' => $this->data['image_url'] ?? null,
            'status' => $this->data['status'] ?? null,
        ];


        $this->post->update($updateData);

        if (isset($this->data['platforms'])) {
            $platformData = [];
            foreach ($this->data['platforms'] as $platformId) {
                $platformData[$platformId] = ['platform_status' => 'pending'];
            }
            $this->post->platforms()->sync($platformData);
        }
        if ($this->post->status == PostStatusEnum::SCHEDULED) {
            $delay = Carbon::parse($this->post->scheduled_time)->diffInSeconds(now(), true);
            PublishPost::dispatch($this->post)->delay($delay);
        }
        LogUserActivity::dispatch(
            Auth::id(),
            ActivityActionTypeEnum::UPDATE->value,
            'Updated post: ' . $this->post->title,
            $this->post
        );

        return $this->post->refresh();
    }

    /**
     * Delete a post
     *
     * @param Post $post
     * @return bool
     */
    public function deletePost()
    {
        LogUserActivity::dispatch(
            Auth::id(),
            ActivityActionTypeEnum::DELETE->value,
            'Deleted post: ' . $this->post->title,
            $this->post,
        );
        $this->post->delete();
    }

    public function processScheduledPosts()
    {
        $results = [
            'processed' => 0,
            'success' => 0,
            'failed' => 0
        ];

        $duePosts = Post::where('status', 'scheduled')
            ->where('scheduled_time', '<=', now())
            ->with('platforms')
            ->get();

        foreach ($duePosts as $post) {
            $results['processed']++;

            $post->status = 'published';
            $post->save();


            foreach ($post->platforms as $platform) {
                $success = $this->mockPublishToSocialPlatform($post, $platform);
                $post->platforms()->updateExistingPivot(
                    $platform->id,
                    ['platform_status' => $success ? 'published' : 'failed']
                );

                if ($success) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                }
            }
        }

        return $results;
    }

    public function publishPost(Post $post)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'platforms' => []
        ];

        $post->status = PostStatusEnum::PUBLISHED;
        $post->save();

        foreach ($post->platforms as $platform) {
            $success = $this->mockPublishToSocialPlatform($post, $platform);

            $post->platforms()->updateExistingPivot(
                $platform->id,
                ['platform_status' => $success ? 'published' : 'failed']
            );

            $results['platforms'][] = [
                'name' => $platform->name,
                'status' => $success ? 'published' : 'failed'
            ];

            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    private function mockPublishToSocialPlatform(Post $post, Platform $platform)
    {
        return (rand(1, 10) <= 6);
    }
}
