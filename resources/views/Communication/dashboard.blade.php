@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Communication.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="" class="btn btn-primary">
                                <i class="feather-bell me-2"></i>
                                <span>Send Notification</span>
                            </a>
                            <a href="" class="btn btn-outline-primary">
                                <i class="feather-message-circle me-2"></i>
                                <span>Messages</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body p-3">
                    <!-- Stats Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="feather-bell text-primary fs-4 mb-2"></i>
                                    <h6 class="text-muted mb-1">Notifications</h6>
                                    <h3 class="mb-0">{{ $totalNotifications ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="feather-mail text-success fs-4 mb-2"></i>
                                    <h6 class="text-muted mb-1">Email Logs</h6>
                                    <h3 class="mb-0">{{ $totalEmailLogs ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="feather-message-square text-info fs-4 mb-2"></i>
                                    <h6 class="text-muted mb-1">SMS Logs</h6>
                                    <h3 class="mb-0">{{ $totalSmsLogs ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="feather-message-circle text-warning fs-4 mb-2"></i>
                                    <h6 class="text-muted mb-1">PT Messages</h6>
                                    <h3 class="mb-0">{{ $totalPtMessages ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Notifications Table -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recent Notifications</h5>
                                <a href="" class="btn btn-sm btn-outline-secondary">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Send To</th>
                                            <th>Scheduled At</th>
                                            <th>Sent At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentNotifications ?? [] as $not)
                                            <tr>
                                                <td>{{ $not->title }}</td>
                                                <td>
                                                    @if($not->send_to_role)
                                                        <span class="badge bg-primary">{{ ucfirst($not->send_to_role) }}</span>
                                                    @else
                                                        <span class="text-muted">Specific users</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($not->scheduled_at)
                                                        {{ \Carbon\Carbon::parse($not->scheduled_at)->format('d M, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">Now</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($not->sent_at)
                                                        {{ \Carbon\Carbon::parse($not->sent_at)->format('d M, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge
                                                        @if($not->sent_at) bg-success
                                                        @else bg-warning
                                                        @endif">
                                                        {{ $not->sent_at ? 'Sent' : 'Scheduled' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    No notifications yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Logs Row -->
                    <div class="row g-3 mt-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom-0">
                                    <h6 class="card-title mb-0">Recent SMS Logs</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>To</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentSmsLogs ?? [] as $log)
                                                    <tr>
                                                        <td>{{ $log->phone }}</td>
                                                        <td>
                                                            <span class="badge
                                                                @if($log->status === 'sent') bg-success
                                                                @else bg-danger
                                                                @endif">
                                                                {{ ucfirst($log->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-muted text-center py-2">
                                                            No SMS logs.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom-0">
                                    <h6 class="card-title mb-0">Recent Email Logs</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>To</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentEmailLogs ?? [] as $log)
                                                    <tr>
                                                        <td>{{ $log->to_email }}</td>
                                                        <td>
                                                            <span class="badge
                                                                @if($log->status === 'sent') bg-success
                                                                @else bg-danger
                                                                @endif">
                                                                {{ ucfirst($log->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-muted text-center py-2">
                                                            No email logs.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent PT Messages -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-white border-bottom-0">
                            <h6 class="card-title mb-0">Recent Parent‑Teacher Messages</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Message</th>
                                            <th>Read</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentPtMessages ?? [] as $msg)
                                            <tr>
                                                <td>{{ $msg->senderUser->name ?? 'Unknown' }}</td>
                                                <td>{{ $msg->receiverUser->name ?? 'Unknown' }}</td>
                                                <td class="text-truncate" style="max-width: 300px;">
                                                    {{ Str::limit($msg->message, 60) }}
                                                </td>
                                                <td>
                                                    <span class="badge
                                                        @if($msg->is_read) bg-success
                                                        @else bg-warning
                                                        @endif">
                                                        {{ $msg->is_read ? 'Read' : 'Unread' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">
                                                    No messages yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
