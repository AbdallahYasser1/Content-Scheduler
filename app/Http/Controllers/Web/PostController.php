<?php

namespace App\Http\Controllers\Web;

use App\Enums\ActivityActionTypeEnum;
use App\Enums\PostStatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\LogUserActivity;
use App\Jobs\PublishPost;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\Platform;
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
        $this->middleware('auth');
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

        return view('posts.index', [
            'posts' => $posts,
            'status' => $request->status,
            'date' => $request->date
        ]);
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $platforms = auth()->user()->activePlatforms;
        return view('posts.create', compact('platforms'));
    }

    /**
     * Store a newly created post in storage.
     *
     * @param \App\Http\Requests\StorePostRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $this->postService->setData($request->validated())->createPost();
        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified post.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);
        $post->load('platforms');

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        if (!in_array($post->status->value, PostStatusEnum::editableStatus())) {
            return redirect()->route('posts.index')
                ->with('error', 'Only draft or scheduled posts can be updated.');
        }

        $platforms = Platform::all();
        $selectedPlatforms = $post->platforms->pluck('id')->toArray();

        return view('posts.edit', compact('post', 'platforms', 'selectedPlatforms'));
    }

    /**
     * Update the specified post in storage.
     *
     * @param \App\Http\Requests\UpdatePostRequest $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(StorePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        $this->postService->setPost($post)->setData($request->validated())->updatePost();

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post from storage.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $this->postService->setPost($post)->deletePost();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    public function publish(Post $post)
    {
        $this->authorize('publish', $post);

        if ($post->status == PostStatusEnum::PUBLISHED) {
            return redirect()->route('posts.show', $post)
                ->with('error', 'This post has already been published.');
        }

        PublishPost::dispatch($post);

        LogUserActivity::dispatch(
            Auth::id(),
            ActivityActionTypeEnum::PUBLISH->value,
            'Manually published post: ' . $post->title,
            $post
        );

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post has been queued for publishing. It will be published shortly.');
    }
}
