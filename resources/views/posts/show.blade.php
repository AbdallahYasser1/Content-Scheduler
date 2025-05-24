@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Post Details') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h2>{{ $post->title }}</h2>
                        
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <strong>Status:</strong>
                                @if($post->status == \App\Enums\PostStatusEnum::DRAFT)
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($post->status == \App\Enums\PostStatusEnum::SCHEDULED)
                                    <span class="badge bg-info">Scheduled</span>
                                @elseif($post->status == \App\Enums\PostStatusEnum::PUBLISHED)
                                    <span class="badge bg-success">Published</span>
                                @endif
                            </div>
                            
                            <div class="me-3">
                                <strong>Created:</strong> {{ date('Y-m-d H:i', strtotime($post->created_at)) }}
                            </div>
                            
                            @if($post->scheduled_time)
                                <div>
                                    <strong>Scheduled for:</strong> {{ date('Y-m-d H:i', strtotime($post->scheduled_time)) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <strong>Platforms:</strong>
                            @foreach($post->platforms as $platform)
                                <span class="badge bg-primary me-1">
                                    {{ $platform->name }}
                                    @if($platform->pivot->platform_status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($platform->pivot->platform_status == 'published')
                                        <span class="badge bg-success">Published</span>
                                    @elseif($platform->pivot->platform_status == 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">Content</div>
                        <div class="card-body">
                            <p class="card-text">{{ $post->content }}</p>
                            <div class="text-muted small">Character count: {{ strlen($post->content) }}</div>
                        </div>
                    </div>

                    @if($post->image_url)
                        <div class="card mb-4">
                            <div class="card-header">Image</div>
                            <div class="card-body">
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="img-fluid" style="max-height: 300px;">
                                <div class="mt-2">
                                    <a href="{{ $post->image_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">View Full Image</a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back to Posts</a>
                        </div>
                        
                        <div>
                            @if(in_array($post->status, ['draft', 'scheduled']))
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary">Edit Post</a>
                            @endif
                            
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete Post</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
