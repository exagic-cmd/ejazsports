@extends('layouts.app')

@section('content')
<div class="content-header">
    <div>
        <h2 class="content-title card-title">Daily Customer Reminder & Recovery Lists</h2>
        <p>Manage and track daily recovery sheets with customer remaining balances.</p>
    </div>
    <div>
        <a href="{{ route('customer-reminder-lists.create') }}" class="btn btn-primary">
            <i class="text-muted material-icons md-post_add"></i>Add New List
        </a>
    </div>
</div>

@if(session()->has('message'))
    <div class="alert alert-success text-center">
        {{ session()->get('message') }}
    </div>
@endif

@if(session()->has('error'))
    <div class="alert alert-danger text-center">
        {{ session()->get('error') }}
    </div>
@endif

<header class="card card-body mb-4">
    <form method="GET" action="{{ route('customer-reminder-lists.index') }}">
        <div class="row gx-3 align-items-center">
            <div class="col-lg-3 col-md-6 mb-3">
                <label style="font-weight: 700">Filter by Date</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <label style="font-weight: 700">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-lg-4 col-md-6 mb-3" style="margin-top: 25px;">
                <button type="submit" class="btn btn-primary search">Filter</button>
                <a href="{{ route('customer-reminder-lists.index') }}" class="btn btn-light">Reset</a>
            </div>
        </div>
    </form>
</header>

<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Reminder Date</th>
                        <th>List Title</th>
                        <th>Customers</th>
                        <th>Total Balance</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reminderLists as $key => $list)
                        <tr>
                            <td>{{ $reminderLists->firstItem() + $key }}</td>
                            <td>
                                <b>{{ \Carbon\Carbon::parse($list->reminder_date)->format('d M, Y') }}</b>
                                <br>
                                @if(\Carbon\Carbon::parse($list->reminder_date)->isToday())
                                    <span class="badge rounded-pill alert-success">Today</span>
                                @elseif(\Carbon\Carbon::parse($list->reminder_date)->isPast())
                                    <span class="badge rounded-pill alert-danger">{{ \Carbon\Carbon::parse($list->reminder_date)->diffForHumans() }}</span>
                                @else
                                    <span class="badge rounded-pill alert-info">{{ \Carbon\Carbon::parse($list->reminder_date)->format('D') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('customer-reminder-lists.show', $list->id) }}">
                                    <b>{{ $list->title }}</b>
                                </a>
                                @if($list->notes)
                                    <div class="text-muted font-xs text-truncate" style="max-width: 250px;">{{ $list->notes }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill alert-primary">{{ $list->items_count }} Customers</span>
                                @if($list->called_count > 0 || $list->completed_count > 0)
                                    <div class="font-xs text-muted mt-1">
                                        <span class="text-success">{{ $list->completed_count }} Paid</span> &bull; 
                                        <span class="text-info">{{ $list->called_count }} Called</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <b>Rs. {{ number_format($list->total_balance, 2) }}</b>
                            </td>
                            <td>
                                @if($list->status == 2)
                                    <span class="badge rounded-pill alert-success">Completed</span>
                                @else
                                    <span class="badge rounded-pill alert-warning">Active</span>
                                @endif
                            </td>
                            <td>
                                {{ $list->creator ? $list->creator->name : 'System' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('customer-reminder-lists.show', $list->id) }}" class="btn btn-md rounded font-sm">Detail</a>
                                
                                <div class="dropdown d-inline">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm">
                                        <i class="material-icons md-more_horiz"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('customer-reminder-lists.print', $list->id) }}" target="_blank">
                                            <i class="material-icons md-print font-xs"></i> Print Sheet
                                        </a>
                                        <a class="dropdown-item" href="{{ route('customer-reminder-lists.pdf', $list->id) }}" target="_blank" rel="noopener">
                                            <i class="material-icons md-picture_as_pdf font-xs"></i> Download PDF
                                        </a>
                                        <form action="{{ route('customer-reminder-lists.destroy', $list->id) }}" method="POST" onsubmit="return confirm('Do you really want to do this?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="material-icons md-delete font-xs"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="material-icons md-event_busy font-xxl"></i>
                                <p class="mt-2">No daily customer reminder lists found.</p>
                                <a href="{{ route('customer-reminder-lists.create') }}" class="btn btn-sm btn-primary">
                                    Create First List
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-area mt-30 mb-50">
            {{ $reminderLists->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
