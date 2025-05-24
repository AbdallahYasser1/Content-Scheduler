@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Analytics Dashboard') }}</div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Posts per platform -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">Posts per Platform</div>
                                    <div class="card-body">
                                        @if(count($analytics['posts_per_platform']) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Platform</th>
                                                        <th>Post Count</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($analytics['posts_per_platform'] as $platform)
                                                        <tr>
                                                            <td>{{ $platform->name }}</td>
                                                            <td>{{ $platform->post_count }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info">No data available yet.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Publishing stats -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">Publishing Statistics</div>
                                    <div class="card-body">
                                        @if($analytics['publishing_stats']['total'] > 0)
                                            <div class="mb-3">
                                                <h5>Success Rate: {{ $analytics['publishing_stats']['success_rate'] }}
                                                    %</h5>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                         style="width: {{ $analytics['publishing_stats']['success_rate'] }}%"
                                                         aria-valuenow="{{ $analytics['publishing_stats']['success_rate'] }}"
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Status</th>
                                                        <th>Count</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>Total</td>
                                                        <td>{{ $analytics['publishing_stats']['total'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Published</td>
                                                        <td>{{ $analytics['publishing_stats']['published'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Failed</td>
                                                        <td>{{ $analytics['publishing_stats']['failed'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pending</td>
                                                        <td>{{ $analytics['publishing_stats']['pending'] }}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info">No data available yet.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Post status counts -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">Post Status Distribution</div>
                                    <div class="card-body">
                                        @if(count($analytics['post_status_counts']) > 0)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th>Status</th>
                                                                <th>Count</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($analytics['post_status_counts'] as $status => $count)
                                                                <tr>
                                                                    <td>{{ucfirst(\App\Enums\PostStatusEnum::tryFrom($status)->label()) }}</td>
                                                                    <td>{{ $count }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex justify-content-center">
                                                        <!-- In a real app, this would be a chart using Chart.js or similar -->
                                                        <div class="p-4 text-center">
                                                            <p class="mb-3">Status Distribution</p>

                                                            @foreach($analytics['post_status_counts'] as $status => $count)
                                                                @php
                                                                    $total = array_sum(array_values($analytics['post_status_counts']));
                                                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                                                @endphp
                                                                <div class="d-flex align-items-center mb-2 gap-2">
    <span class="badge
        {{ $status == \App\Enums\PostStatusEnum::DRAFT->value ? 'bg-secondary' :
           ($status == \App\Enums\PostStatusEnum::SCHEDULED->value ? 'bg-info' : 'bg-success') }}">
        {{ ucfirst(\App\Enums\PostStatusEnum::tryFrom($status)->label()) }}
    </span>

                                                                    <div class="progress flex-grow-1"
                                                                         style="height: 20px; max-width: 200px;">
                                                                        <div class="progress-bar
            {{ $status == \App\Enums\PostStatusEnum::DRAFT->value ? 'bg-secondary' :
               ($status == \App\Enums\PostStatusEnum::SCHEDULED->value ? 'bg-info' : 'bg-success') }}"
                                                                             role="progressbar"
                                                                             style="width: {{ $percentage }}%"
                                                                             aria-valuenow="{{ $count }}"
                                                                             aria-valuemin="0"
                                                                             aria-valuemax="{{ $total }}">
                                                                        </div>
                                                                    </div>

                                                                    <span>{{ $count }}</span>
                                                                </div>

                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">No data available yet.</div>
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
