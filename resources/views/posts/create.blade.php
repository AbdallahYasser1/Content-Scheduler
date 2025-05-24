@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Create New Post') }}</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('posts.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                       name="title" value="{{ old('title') }}" required>
                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content"
                                          name="content" rows="5" required>{{ old('content') }}</textarea>
                                <div id="charCount" class="form-text">Characters: 0</div>
                                @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image_url" class="form-label">Image URL (optional)</label>
                                <input type="url" class="form-control @error('image_url') is-invalid @enderror"
                                       id="image_url" name="image_url" value="{{ old('image_url') }}">
                                @error('image_url')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="scheduled_time" class="form-label">Schedule Time (optional)</label>
                                <input type="datetime-local"
                                       class="form-control @error('scheduled_time') is-invalid @enderror"
                                       id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time') }}">
                                <div class="form-text">Leave empty to save as draft</div>
                                @error('scheduled_time')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Platforms</label>
                                <div class="row">
                                    @if($platforms->isEmpty())
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                No platforms available. Please activate platforms first.
                                            </div>
                                        </div>
                                    @endif
                                    @foreach($platforms as $platform)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="platforms[]"
                                                       value="{{ $platform->id }}"
                                                       id="platform-{{ $platform->id }}" {{ in_array($platform->id, old('platforms', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="platform-{{ $platform->id }}">
                                                    {{ $platform->name }}
                                                    <span class="text-muted"> ({{$platform->character_limit}} Characters)</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('platforms')
                                <span class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Post</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const contentTextarea = document.getElementById('content');
                const charCount = document.getElementById('charCount');

                function updateCharCount() {
                    const count = contentTextarea.value.length;
                    charCount.textContent = `Characters: ${count}`;

                    if (count > 280) {
                        charCount.classList.add('text-danger');
                    } else {
                        charCount.classList.remove('text-danger');
                    }
                }

                contentTextarea.addEventListener('input', updateCharCount);
                updateCharCount();
            });

            document.addEventListener('DOMContentLoaded', function () {
                const scheduledInput = document.getElementById('scheduled_time');

                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                const nowLocal = now.toISOString().slice(0, 16);

                scheduledInput.min = nowLocal;
            });
        </script>
    @endpush
@endsection
