<?php

namespace App\Http\Controllers\API;

use App\Enums\ActivityActionTypeEnum;
use App\Enums\PostStatusEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Jobs\LogUserActivity;
use App\Jobs\PublishPost;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected $postService;

    /**
     * Create a new controller instance.
     *
     * @param PostService $postService
     * @return void
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the posts.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $posts = $this->postService->getUserPosts();
        return ApiResponse::success(new PostCollection($posts));
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->setData($request->validated())->createPost();
        return ApiResponse::success(new PostResource($post), 'Post created successfully.', 201);
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);
        $post->load('platforms');
        return ApiResponse::success(new PostResource($post));
    }

    public function update(StorePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        $post = $this->postService->setPost($post)->setData($request->validated())->updatePost();
        return ApiResponse::success(new PostResource($post), 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $this->postService->setPost($post)->deletePost();
        return ApiResponse::success(null, 'Post deleted successfully.');
    }

    public function publish(Post $post)
    {
        $this->authorize('publish', $post);

        if ($post->status == PostStatusEnum::PUBLISHED) {
            return ApiResponse::error('This post has already been published.', 400);
        }

        PublishPost::dispatch($post);
        LogUserActivity::dispatch(Auth::id(), ActivityActionTypeEnum::PUBLISH->value, 'Manually published post: ' . $post->title, $post);

        return ApiResponse::success(null, 'Post has been queued for publishing. It will be published shortly.');
    }
}