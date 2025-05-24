@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <span><i class="bi bi-speedometer2 me-2"></i>{{ __('Dashboard') }}</span>

                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif


                        <div class="row">
                            <!-- Analytics Summary -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm stat-card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Analytics Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Total Posts:</span>
                                            <span class="badge bg-primary rounded-pill">
                                            {{ array_sum($postsAnalytics['post_status_counts'] ?? []) }}
                                        </span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Published:</span>
                                            <span class="badge bg-success rounded-pill">
                                            {{ $postsAnalytics['post_status_counts'][\App\Enums\PostStatusEnum::PUBLISHED->value] ?? 0 }}
                                        </span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Scheduled:</span>
                                            <span class="badge bg-warning rounded-pill">
                                            {{ $postsAnalytics['post_status_counts'][\App\Enums\PostStatusEnum::SCHEDULED->value] ?? 0 }}
                                        </span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Draft:</span>
                                            <span class="badge bg-secondary rounded-pill">
                                            {{ $postsAnalytics['post_status_counts'][\App\Enums\PostStatusEnum::DRAFT->value] ?? 0 }}
                                        </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Success Rate:</span>
                                            <span class="badge bg-info rounded-pill">
                                            {{ $postsAnalytics['publishing_stats']['success_rate'] ?? 0 }}%
                                        </span>
                                        </div>

                                        @if(isset($postsAnalytics['posts_per_platform']) && count($postsAnalytics['posts_per_platform']) > 0)
                                            <hr>
                                            <h6 class="mb-2">Posts by Platform:</h6>
                                            <div class="d-flex flex-wrap">
                                                @foreach($postsAnalytics['posts_per_platform'] as $platform)
                                                    <div class="platform-badge me-2 mb-2">
                                                        <i class="bi bi-{{ strtolower(str_replace(' ', '', $platform->name)) }}"></i>
                                                        {{ $platform->name }}: {{ $platform->post_count }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <hr>
                                        <a href="{{ route('analytics.index') }}"
                                           class="btn btn-outline-primary btn-sm w-100">
                                            <i class="bi bi-bar-chart-line me-1"></i>View Full Analytics
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Upcoming Posts -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm stat-card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Upcoming Posts</h5>
                                    </div>
                                    <div class="card-body">
                                        @if(count($usersAnalytics['upcomingPosts']) > 0)
                                            <ul class="list-group list-group-flush">
                                                @foreach($usersAnalytics['upcomingPosts'] as $post)
                                                    <li class="list-group-item px-0 border-bottom">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-0">{{ Str::limit($post->title, 25) }}</h6>
                                                                <small class="text-muted">
                                                                    <i class="bi bi-clock me-1"></i>
                                                                    {{ $post->scheduled_time ? \Carbon\Carbon::parse($post->scheduled_time)->format('M d, Y g:i A') : 'Not scheduled' }}
                                                                </small>
                                                                <div class="mt-1">
                                                                    @foreach($post->platforms as $platform)
                                                                        <span class="badge bg-light text-dark border me-1">
                                                                        <i class="bi bi-{{ strtolower($platform->type) }} me-1"></i>
                                                                        {{ $platform->name }}
                                                                    </span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="d-flex">
                                                                @if(in_array($post->status->value , \App\Enums\PostStatusEnum::editableStatus()))
                                                                    <a href="{{ route('posts.edit', $post) }}"
                                                                       class="btn btn-sm btn-outline-warning me-1"
                                                                       title="Edit">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                @endif
                                                                <a href="{{ route('posts.show', $post) }}"
                                                                   class="btn btn-sm btn-outline-primary" title="View">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-center text-muted my-4">
                                                <i class="bi bi-calendar-x display-4"></i>
                                                <p class="mt-2">No upcoming posts scheduled</p>
                                                <a href="{{ route('posts.create') }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Schedule a post now
                                                </a>
                                            </div>
                                        @endif
                                        <hr>
                                        <a href="{{ route('posts.index') }}"
                                           class="btn btn-outline-primary btn-sm w-100">
                                            <i class="bi bi-list-ul me-1"></i>Manage All Posts
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Platforms -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm stat-card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="bi bi-share me-2"></i>Active Platforms</h5>
                                    </div>
                                    <div class="card-body">
                                        @if(count($activePlatforms) > 0)
                                            <ul class="list-group list-group-flush">
                                                @foreach($activePlatforms as $platform)
                                                    <li class="list-group-item px-0 border-bottom">
                                                        <div class="d-flex align-items-center">
                                                            <div class="platform-icon me-2">
                                                                <i class="bi bi-{{ strtolower($platform->type) }}"></i>
                                                            </div>
                                                            <div>
                                                                <span>{{ $platform->name }}</span>
                                                                <small class="d-block text-muted">{{ Str::limit($platform->description, 30) }}</small>
                                                            </div>
                                                            <span class="badge bg-success ms-auto">Active</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-center text-muted my-4">
                                                <i class="bi bi-exclamation-triangle display-4"></i>
                                                <p class="mt-2">No active platforms</p>
                                                <a href="{{ route('platforms.index') }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Activate platforms now
                                                </a>
                                            </div>
                                        @endif
                                        <hr>
                                        <a href="{{ route('platforms.index') }}"
                                           class="btn btn-outline-primary btn-sm w-100">
                                            <i class="bi bi-gear me-1"></i>Manage Platforms
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Recent Activity</h5>
                                    </div>
                                    <div class="card-body">
                                        @if(count($usersAnalytics['activities']) > 0)
                                            <div class="list-group">
                                                @foreach($usersAnalytics['activities'] as $activity)
                                                    <div class="list-group-item list-group-item-action activity-item activity-{{ $activity->action_type }}">
                                                        <div class="d-flex w-100 align-items-center">
                                                            <div class="activity-icon bg-{{ $activity->action_type == 'create' ? 'success' : ($activity->action_type == 'update' ? 'warning' : ($activity->action_type == 'publish' ? 'info' : 'danger')) }} text-white">
                                                                <i class="bi bi-{{ $activity->action_type == 'create' ? 'plus-circle' : ($activity->action_type == 'update' ? 'pencil' : ($activity->action_type == 'publish' ? 'send' : 'trash')) }}"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex w-100 justify-content-between">
                                                                    <h6 class="mb-1">{{ ucfirst($activity->action_type) }}
                                                                        Action</h6>
                                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                                                                </div>
                                                                <p class="mb-1">{{ $activity->description }}</p>
                                                                @if(isset($activity->resource_type) && isset($activity->resource_id))
                                                                    <small class="text-muted">
                                                                        {{ ucfirst($activity->resource_type) }}
                                                                        #{{ $activity->resource_id }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-muted my-4">
                                                <i class="bi bi-clock-history display-4"></i>
                                                <p class="mt-2">No recent activity</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Add any dashboard-specific JavaScript here
        document.addEventListener('DOMContentLoaded', function () {
            // Example: Add tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endsection
