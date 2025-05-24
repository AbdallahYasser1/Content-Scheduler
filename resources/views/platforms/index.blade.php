@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Platform Management') }}</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <p class="mb-4">Toggle the platforms you want to use for your content scheduling.</p>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Platform</th>
                                    <th>Type</th>
                                    <th>Characters Limit</th>
                                    <th>Image Required</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($platforms as $platform)
                                    <tr>
                                        <td>{{ $platform->name }}</td>
                                        <td>{{ ucfirst($platform->type) }}</td>
                                        <td>{{ $platform->character_limit }}</td>
                                        <td>{{ $platform->is_image_required ? "Yes" : "No" }}</td>
                                        <td>
                                            @if($platform->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('platforms.toggle', $platform) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm {{ $platform->is_active ? 'btn-danger' : 'btn-success' }}">
                                                    {{$platform->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
