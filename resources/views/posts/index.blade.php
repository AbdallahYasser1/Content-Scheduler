@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Posts') }}</span>
                        <a href="{{ route('posts.create') }}" class="btn btn-primary btn-sm">Create New Post</a>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Filters -->
                        <div class="mb-4">
                            <form action="{{ route('posts.index') }}" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select"
                                            placeholder="Select a status option">


                                        <option value="{{\App\Enums\PostStatusEnum::DRAFT->value}}" {{ request('status') == \App\Enums\PostStatusEnum::DRAFT->value ? 'selected' : '' }} >
                                            Draft
                                        </option>
                                         <option value="" {{ (request('status') == null || request('status') == '') ? 'selected' : '' }}>
                                            Select a status option
                                        </option>
                                        <option value="{{\App\Enums\PostStatusEnum::SCHEDULED->value}}" {{ request('status') == \App\Enums\PostStatusEnum::SCHEDULED->value ? 'selected' : '' }}>
                                            Scheduled
                                        </option>
                                        <option value="{{\App\Enums\PostStatusEnum::PUBLISHED->value}}" {{ request('status') ==\App\Enums\PostStatusEnum::PUBLISHED->value ? 'selected' : '' }}>
                                            Published
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="scheduled_time"
                                           value="{{ request('scheduled_time') }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-secondary me-2">Filter</button>
                                    <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </form>
                        </div>

                        @if($posts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Platforms</th>
                                        <th>Scheduled Time</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($posts as $post)
                                        <tr>
                                            <td>{{ $post->title }}</td>
                                            <td>
                                                <span class="badge {{$post->status->color()}}">{{$post->status->label()}}</span>
                                            </td>
                                            <td>
                                                @foreach($post->platforms as $platform)
                                                    <span class="badge bg-primary">{{ $platform->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $post->scheduled_time ? date('Y-m-d H:i', strtotime($post->scheduled_time)) : 'N/A' }}</td>
                                            <td>{{ date('Y-m-d', strtotime($post->created_at)) }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('posts.show', $post) }}"
                                                       class="btn btn-sm btn-outline-primary me-2">View</a>

                                                    @if(in_array($post->status->value, \App\Enums\PostStatusEnum::editableStatus()))
                                                        <a href="{{ route('posts.edit', $post) }}"
                                                           class="btn btn-sm btn-outline-secondary me-2">Edit</a>
                                                    @endif

                                                    <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger  me-2">
                                                            Delete
                                                        </button>
                                                    </form>
                                                    @if($post->status == \App\Enums\PostStatusEnum::DRAFT)
                                                        <form action="{{ route('posts.publish', $post) }}" method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to publish this post now?');">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-warning">
                                                                Publish
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $posts->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                No posts found. <a href="{{ route('posts.create') }}">Create your first post</a>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelector('form[action="{{ route('posts.index') }}"]').addEventListener('submit', function (e) {
            const statusSelect = this.querySelector('select[name="status"]');
            const dateSelect = this.querySelector('input[name="scheduled_time"]');
            if (statusSelect && statusSelect.value === '') {
                statusSelect.disabled = true;
            }
            if (dateSelect && dateSelect.value === '') {
                dateSelect.disabled = true;
            }
        });
    </script>
@endsection
