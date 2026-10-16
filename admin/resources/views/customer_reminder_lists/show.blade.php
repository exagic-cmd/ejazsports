@extends('layouts.app')

@section('content')
<div class="content-header">
    <div>
        <a href="{{ route('customer-reminder-lists.index') }}"><i class="material-icons md-arrow_back"></i> Go back</a>
        <h2 class="content-title card-title mt-2">{{ $list->title }}</h2>
        <p>
            <b>Date:</b> {{ \Carbon\Carbon::parse($list->reminder_date)->format('d M, Y (l)') }} &bull;
            <b>Created By:</b> {{ $list->creator ? $list->creator->name : 'System' }} &bull;
            <span class="badge rounded-pill {{ $list->status == 2 ? 'alert-success' : 'alert-warning' }}">
                {{ $list->status == 2 ? 'Completed' : 'Active' }}
            </span>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('customer-reminder-lists.print', [$list->id, 'sort_by' => $sortBy]) }}" target="_blank" class="btn btn-light rounded font-sm">
            <i class="material-icons md-print"></i> Print
        </a>
        <a href="{{ route('customer-reminder-lists.pdf', [$list->id, 'sort_by' => $sortBy]) }}" target="_blank" rel="noopener" class="btn btn-light rounded font-sm">
            <i class="material-icons md-picture_as_pdf"></i> PDF
        </a>
        <button type="button" class="btn btn-success-light rounded font-sm" id="btnShareWhatsApp" title="Opens WhatsApp with this recovery list's text ready to share">
            <i class="material-icons md-share"></i> WhatsApp
        </button>
        <button type="button" class="btn btn-light rounded font-sm" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
            <i class="material-icons md-calendar_today"></i> Change Date
        </button>
        <button type="button" class="btn btn-primary font-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
            <i class="material-icons md-person_add"></i> Add Customer
        </button>
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

<div class="card card-body mb-4">
    <div class="row">
        <div class="col-md-3 col-6 text-center">
            <h6>Total Customers</h6>
            <p class="font-lg"><b>{{ $list->items->count() }}</b></p>
        </div>
        <div class="col-md-3 col-6 text-center">
            <h6>Total Recovery Balance</h6>
            <p class="font-lg text-danger"><b>Rs. {{ number_format($totalBalance, 2) }}</b></p>
        </div>
        <div class="col-md-3 col-6 text-center">
            <h6>Recovered / Paid</h6>
            <p class="font-lg text-success"><b>{{ $list->items->where('status', \App\Models\CustomerReminderListItem::STATUS_PAID)->count() }}</b></p>
        </div>
        <div class="col-md-3 col-6 text-center">
            <h6>Contacted / Called</h6>
            <p class="font-lg text-info"><b>{{ $list->items->where('status', \App\Models\CustomerReminderListItem::STATUS_CALLED)->count() }}</b></p>
        </div>
    </div>
</div>

<div class="card mb-4">
    <header class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6 col-12">
                <h5 class="card-title mb-0">Customer Recovery List</h5>
                @if($list->notes)
                    <p class="text-muted font-xs mb-0"><b>Note:</b> {{ $list->notes }}</p>
                @endif
            </div>
            <div class="col-md-6 col-12 text-md-end mt-2 mt-md-0">
                <label class="font-xs fw-bold me-2 mb-0">Sort / Group By:</label>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('customer-reminder-lists.show', [$list->id, 'sort_by' => 'area']) }}" 
                       class="btn {{ $sortBy === 'area' ? 'btn-primary' : 'btn-light' }}">
                        Area Wise
                    </a>
                    <a href="{{ route('customer-reminder-lists.show', [$list->id, 'sort_by' => 'balance_desc']) }}" 
                       class="btn {{ $sortBy === 'balance_desc' ? 'btn-primary' : 'btn-light' }}">
                        Balance: High to Low
                    </a>
                    <a href="{{ route('customer-reminder-lists.show', [$list->id, 'sort_by' => 'name']) }}" 
                       class="btn {{ $sortBy === 'name' ? 'btn-primary' : 'btn-light' }}">
                        Customer Name
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="myTable">
                <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Area / City</th>
                        <th class="text-end">Balance</th>
                        <th width="190">Status</th>
                        <th>Remarks</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $globalIndex = 1; @endphp
                    @forelse($groupedItems as $groupName => $items)
                        @if($sortBy === 'area')
                            @php
                                $groupSubtotal = $items->sum('snapshot_balance');
                            @endphp
                            <tr style="background-color: #f8f9fa;">
                                <td colspan="4" class="py-2">
                                    <b>Area: {{ $groupName }}</b> 
                                    <span class="badge rounded-pill alert-info ms-2">{{ $items->count() }} Customers</span>
                                </td>
                                <td class="text-end py-2">
                                    <b>Subtotal: Rs. {{ number_format($groupSubtotal, 2) }}</b>
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        @endif

                        @foreach($items as $item)
                            @php $cust = $item->customer; @endphp
                            <tr>
                                <td>{{ $globalIndex++ }}</td>
                                <td>
                                    <b>{{ $cust ? $cust->first_name . ' ' . $cust->last_name : 'N/A' }}</b>
                                    @if($cust)
                                        <br>
                                        <a href="{{ route('customers.show', $cust->id) }}" target="_blank" class="font-xs">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($cust && $cust->phone_number)
                                        {{ $cust->phone_number }}
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cust->whatsapp_number ?: $cust->phone_number) }}" target="_blank" class="text-success ms-1" title="WhatsApp Customer">
                                            <i class="material-icons md-chat font-xs"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    {{ $cust && $cust->area ? $cust->area->name : ($cust && $cust->city ? $cust->city : ($cust ? $cust->address : 'General')) }}
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('customer-reminder-lists.item.update', $item->id) }}" method="POST" class="d-inline-block" style="max-width: 150px;">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $item->status }}">
                                        <input type="number" name="snapshot_balance" step="0.01" value="{{ number_format($item->snapshot_balance, 2, '.', '') }}" class="form-control form-control-sm text-end" title="Edit this customer's balance; changes are recorded in audit history" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('customer-reminder-lists.item.update', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Pending</option>
                                            <option value="2" {{ $item->status == 2 ? 'selected' : '' }}>Called / In Followup</option>
                                            <option value="3" {{ $item->status == 3 ? 'selected' : '' }}>Paid / Recovered</option>
                                            <option value="4" {{ $item->status == 4 ? 'selected' : '' }}>Rescheduled</option>
                                            <option value="5" {{ $item->status == 5 ? 'selected' : '' }}>Cancelled / Skipped</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('customer-reminder-lists.item.update', $item->id) }}" method="POST" class="d-flex">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $item->status }}">
                                        <input type="text" name="remarks" class="form-control form-control-sm me-1 font-xs" 
                                               placeholder="Remarks..." value="{{ $item->remarks }}"
                                               onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="text-end">
                                    @if($cust && $cust->email)
                                        <form action="{{ route('customer-reminder-lists.item.email', [$list->id, $item->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light text-primary rounded" title="Email this customer's reminder">
                                                <i class="material-icons md-email"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light rounded" title="Add an email address to this customer to send a reminder" disabled>
                                            <i class="material-icons md-email"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('customer-reminder-lists.item.remove', $item->id) }}" method="POST" onsubmit="return confirm('Do you really want to remove {{ $cust ? $cust->first_name : 'this customer' }} from the list?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger rounded" title="Remove customer">
                                            <i class="material-icons md-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No customers currently in this reminder list.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><b>GRAND TOTAL ({{ $list->items->count() }} Customers):</b></td>
                        <td class="text-end"><b>Rs. {{ number_format($totalBalance, 2) }}</b></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Access &amp; Audit History</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Date / Time</th><th>User</th><th>Change</th><th>Previous Values</th><th>New Values</th></tr></thead>
                <tbody>
                    @forelse($auditLogs as $audit)
                        <tr>
                            <td>{{ $audit->created_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $audit->user ? $audit->user->name : 'System' }}</td>
                            <td>{{ $audit->description }}</td>
                            <td><small>{{ $audit->old_values ? json_encode(json_decode($audit->old_values), JSON_UNESCAPED_UNICODE) : '—' }}</small></td>
                            <td><small>{{ $audit->new_values ? json_encode(json_decode($audit->new_values), JSON_UNESCAPED_UNICODE) : '—' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No changes recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal 1: Reschedule Date -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer-reminder-lists.reschedule', $list->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rescheduleModalLabel">Change Reminder Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label style="font-weight: 700">Current Date:</label>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($list->reminder_date)->format('l, d M Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <label style="font-weight: 700">Select New Date *</label>
                        <input type="date" name="reminder_date" class="form-control" value="{{ old('reminder_date', $list->reminder_date) }}" required>
                    </div>
                    @if(session('dateDuplicateDetails'))
                        <div class="alert alert-warning">
                            Changing to this date would duplicate customers on another active list:
                            <ul class="mb-2 mt-2">
                                @foreach(session('dateDuplicateDetails') as $duplicate)
                                    <li>{{ $duplicate }}</li>
                                @endforeach
                            </ul>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="confirm_duplicates" value="1" id="confirmDateDuplicates">
                                <label class="form-check-label" for="confirmDateDuplicates">Change the date anyway.</label>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Date</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Add Customer to Active List -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('customer-reminder-lists.add-item', $list->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add Customer to List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(session('duplicateCustomerId'))
                        <div class="alert alert-warning">
                            {{ session('error') }}
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" name="confirm_duplicate" value="1" id="confirmSingleDuplicate">
                                <label class="form-check-label" for="confirmSingleDuplicate">Add this customer anyway.</label>
                            </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label style="font-weight: 700">Select Customer *</label>
                        <select name="customer_id" id="modalCustomerSelect" class="form-select select2" required style="width: 100%;">
                            <option value="">-- Choose Customer --</option>
                            @foreach($availableCustomers as $c)
                                <option value="{{ $c->id }}" {{ (int) old('customer_id', session('duplicateCustomerId')) === (int) $c->id ? 'selected' : '' }}>
                                    {{ $c->first_name }} {{ $c->last_name }} ({{ $c->phone_number ?: 'No Phone' }}) - {{ $c->area ? $c->area->name : ($c->city ?: 'General') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formatted text for sharing -->
<textarea id="hiddenShareText" style="display:none;">*{{ $list->title }}*
Date: {{ \Carbon\Carbon::parse($list->reminder_date)->format('d M Y') }}
Total Recovery: Rs. {{ number_format($totalBalance, 2) }}
---------------------------------
@php $idx = 1; @endphp
@foreach($groupedItems as $grp => $grpList)
[{{ $grp }}]
@foreach($grpList as $it)
{{ $idx++ }}. {{ $it->customer ? $it->customer->first_name : 'Customer' }} | Bal: Rs. {{ number_format($it->snapshot_balance, 0) }} | Ph: {{ $it->customer ? $it->customer->phone_number : 'N/A' }}
@endforeach

@endforeach
---------------------------------
Ejaz Sports ERP</textarea>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#btnShareWhatsApp').on('click', function() {
        let text = $('#hiddenShareText').val();
        let encodedText = encodeURIComponent(text);
        let url = 'https://api.whatsapp.com/send?text=' + encodedText;
        window.open(url, '_blank');
    });

    if ($.fn.select2) {
        $('#modalCustomerSelect').select2({
            dropdownParent: $('#addCustomerModal')
        });
    }
    @if(session('duplicateCustomerId'))
        new bootstrap.Modal(document.getElementById('addCustomerModal')).show();
    @endif
    @if(session('dateDuplicateDetails'))
        new bootstrap.Modal(document.getElementById('rescheduleModal')).show();
    @endif
});
</script>
@endsection
