@extends('layouts.app')

@section('content')
<div class="content-header">
    <div>
        <a href="{{ route('customer-reminder-lists.index') }}"><i class="material-icons md-arrow_back"></i> Go back</a>
        <h2 class="content-title card-title mt-2">Create Daily Customer Reminder List</h2>
        <p>Select customer names to create a list for the day with remaining balances.</p>
    </div>
</div>

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger text-center">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form action="{{ route('customer-reminder-lists.store') }}" method="POST" id="reminderListForm">
    @csrf
    @if(session('duplicateCustomers'))
        <div class="alert alert-warning">
            <strong>Duplicate customer warning:</strong> These customers are already included on another active reminder list for {{ old('reminder_date') }}.
            <ul class="mb-2 mt-2">
                @foreach(session('duplicateCustomers') as $duplicate)
                    <li>{{ $duplicate['customer'] }} — {{ $duplicate['lists'] }}</li>
                @endforeach
            </ul>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="confirm_duplicates" value="1" id="confirmDuplicates" {{ old('confirm_duplicates') ? 'checked' : '' }}>
                <label class="form-check-label" for="confirmDuplicates">I reviewed this warning and want to create the list anyway.</label>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Settings -->
        <div class="col-lg-4 col-12 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>List Information</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label style="font-weight: 700">Reminder Date *</label>
                        <input type="date" name="reminder_date" id="reminderDate" class="form-control" value="{{ old('reminder_date', $selectedDate) }}" required>
                    </div>

                    <div class="mb-3">
                        <label style="font-weight: 700">Title (Optional)</label>
                        <input type="text" name="title" id="sheetTitle" class="form-control" placeholder="e.g. Recovery List - {{ date('d M Y') }}" value="{{ old('title') }}">
                    </div>

                    <div class="mb-3">
                        <label style="font-weight: 700">Notes / Instructions</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Notes for today's reminders...">{{ old('notes') }}</textarea>
                    </div>

                    <hr class="my-3">

                    <div class="card card-body mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Selected Customers:</span>
                            <span class="badge rounded-pill alert-primary" id="selectedCountBadge">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total Balance:</span>
                            <b class="text-danger" id="selectedTotalBalance">Rs. 0.00</b>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btnSubmitList">
                        <i class="text-muted material-icons md-post_add"></i> Create Reminder List
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Customer Selection Table -->
        <div class="col-lg-8 col-12 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="mb-0">Select Customers</h4>
                </div>

                <div class="card-body">
                    <!-- Filters row -->
                    <div class="row gx-3 mb-3">
                        <div class="col-md-5 col-12 mb-2">
                            <label style="font-weight: 700">Search</label>
                            <input type="text" id="filterSearch" class="form-control" placeholder="Search by name, phone...">
                        </div>
                        <div class="col-md-4 col-12 mb-2">
                            <label style="font-weight: 700">City</label>
                            <select id="filterArea" class="form-select">
                                <option value="">All Cities</option>
                                @foreach($areaOptions as $areaLabel)
                                    <option value="{{ strtolower($areaLabel) }}">{{ $areaLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-12 mb-2" style="margin-top: 25px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filterPositiveOnly" checked>
                                <label class="form-check-label font-xs fw-bold" for="filterPositiveOnly">Balance &gt; 0 only</label>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="customersTable">
                            <thead>
                                <tr>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="masterCheckbox">
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <span>Customer Name</span>
                                            <span>
                                                <button type="button" class="btn btn-sm btn-light" id="btnSelectAllFiltered">Select All</button>
                                                <button type="button" class="btn btn-sm btn-light" id="btnDeselectAll">Deselect All</button>
                                            </span>
                                        </div>
                                    </th>
                                    <th>Phone Number</th>
                                    <th>Area / City</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                    <tr class="customer-row" 
                                        data-name="{{ strtolower($customer->first_name . ' ' . $customer->last_name) }}"
                                        data-phone="{{ $customer->phone_number }}"
                                        data-area="{{ strtolower($customer->area_label) }}"
                                        data-balance="{{ $customer->computed_balance }}">
                                        <td class="text-center">
                                            <input type="checkbox" 
                                                   name="customer_ids[]" 
                                                   value="{{ $customer->id }}" 
                                                   class="form-check-input customer-checkbox" 
                                                   data-balance="{{ $customer->computed_balance }}"
                                                   {{ in_array($customer->id, old('customer_ids', [])) ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <b>{{ $customer->first_name }} {{ $customer->last_name }}</b>
                                        </td>
                                        <td>
                                            {{ $customer->phone_number ?: '-' }}
                                        </td>
                                        <td>
                                            {{ $customer->area_label }}
                                        </td>
                                        <td class="text-end">
                                            <b>Rs. {{ number_format($customer->computed_balance, 2) }}</b>
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
</form>
@endsection

@section('js')
<script>
$(document).ready(function() {
    function updateSelectedSummary() {
        let count = 0;
        let total = 0;
        $('.customer-checkbox:checked').each(function() {
            count++;
            total += parseFloat($(this).data('balance')) || 0;
        });

        $('#selectedCountBadge').text(count);
        $('#selectedTotalBalance').text('Rs. ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    function applyFilters() {
        let searchTerm = $('#filterSearch').val().toLowerCase().trim();
        let selectedArea = String($('#filterArea').val() || '').toLowerCase().trim();
        let positiveOnly = $('#filterPositiveOnly').is(':checked');

        $('.customer-row').each(function() {
            let row = $(this);
            let name = row.data('name');
            let phone = String(row.data('phone') || '');
            let area = row.data('area');
            let balance = parseFloat(row.data('balance')) || 0;

            let matchesSearch = !searchTerm || name.includes(searchTerm) || phone.includes(searchTerm);
            let matchesArea = !selectedArea || String(area || '').toLowerCase().trim() === selectedArea;
            let matchesBalance = !positiveOnly || (balance > 0);

            if (matchesSearch && matchesArea && matchesBalance) {
                row.show();
            } else {
                row.hide();
            }
        });

        let visibleCheckboxes = $('.customer-row:visible .customer-checkbox');
        let checkedVisible = visibleCheckboxes.filter(':checked').length;
        $('#masterCheckbox')
            .prop('checked', visibleCheckboxes.length > 0 && checkedVisible === visibleCheckboxes.length)
            .prop('indeterminate', checkedVisible > 0 && checkedVisible < visibleCheckboxes.length);
    }

    $('#filterSearch, #filterArea').on('input change', applyFilters);
    $('#filterPositiveOnly').on('change', applyFilters);

    applyFilters();

    $(document).on('change', '.customer-checkbox', function() {
        updateSelectedSummary();
        applyFilters();
    });

    $('#masterCheckbox').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.customer-row:visible .customer-checkbox').prop('checked', isChecked).trigger('change');
    });

    $('#btnSelectAllFiltered').on('click', function() {
        $('.customer-row:visible .customer-checkbox').prop('checked', true).trigger('change');
    });

    $('#btnDeselectAll').on('click', function() {
        $('.customer-checkbox').prop('checked', false);
        $('#masterCheckbox').prop('checked', false);
        $('#masterCheckbox').prop('indeterminate', false);
        updateSelectedSummary();
    });

    $('#reminderListForm').on('submit', function(e) {
        let count = $('.customer-checkbox:checked').length;
        if (count === 0) {
            e.preventDefault();
            alert('Please select at least one customer.');
            return false;
        }
    });
});
</script>
@endsection
