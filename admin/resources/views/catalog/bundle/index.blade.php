@extends('layouts.app')

@section('content')
<div class="content-header">
    <div>
        <h2 class="content-title card-title">Bundles List</h2>
        <p>Latest bundle details</p>
    </div>
    <div>
        @can('Create Bundle')
        <a href="{{route('bundles.create')}}" class="btn btn-primary btn-sm rounded">Create new</a>
        @endcan
         @can('Delete Bundle')
        <button class="btn btn-danger btn-sm rounded ms-2" id="batch-delete-btn">
            <i class="far fa-trash-alt"></i> Delete Selected
        </button>
        @endcan
    </div>
</div>

<div class="card mb-4">
    <header class="card-header">
        <div class="row g-3 align-items-center">
            <div class="col-lg-4 col-md-6 me-auto">
                <input type="text" id="searchbox" placeholder="Search By Name, ID, Description..."
                       class="form-control" value="{{ request('searchbox') }}">
            </div>
            <div class="col-md-2 col-6">
                <select class="form-select select2" id="status">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 col-6">
                <select class="form-select select2" id="sort">
                    <option value="">Sort By</option>
                    <option value="purchase_price_asc" {{ request('sort') == 'purchase_price_asc' ? 'selected' : '' }}>Purchase Price: Low to High</option>
                    <option value="purchase_price_desc" {{ request('sort') == 'purchase_price_desc' ? 'selected' : '' }}>Purchase Price: High to Low</option>
                    <option value="additional_price_asc" {{ request('sort') == 'additional_price_asc' ? 'selected' : '' }}>Additional Price: Low to High</option>
                    <option value="additional_price_desc" {{ request('sort') == 'additional_price_desc' ? 'selected' : '' }}>Additional Price: High to Low</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                </select>
            </div>
            <div class="col-md-3 col-12">
                <button type="button" class="btn btn-primary w-100 search">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </div>
    </header>

    <div class="card-body">
        <div id="results-summary" class="mb-3 text-muted" style="display: none;">
            Showing <span id="results-from"></span> to <span id="results-to"></span> of <span id="results-total"></span> results
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" style="min-width: 1000px">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 40px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                        <th style="width: 80px;">Image</th>
                        <th>Bundle Name</th>
                        <th>Description</th>
                        <th class="text-center" style="width: 100px;">Products</th>
                        <th class="text-center" style="width: 120px;">Purchase Price(Rs)</th>
                        <th class="text-center" style="width: 120px;">Additional Price (Rs)</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="result">
                   @include('catalog.bundle.partials.results', ['bundles' => $bundles])
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="pagination-area mt-30 mb-50">
    <nav aria-label="Page navigation example" id="pagination-links">
        {{ $bundles->appends(request()->except('page'))->links() }}
    </nav>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2({
        width: '100%'
    });

    // Update results summary
  function updateResultsSummary(data) {
        if (data.total !== undefined) {
            $('#results-from').text(data.from || 0);
            $('#results-to').text(data.to || 0);
            $('#results-total').text(data.total || 0);
            $('#results-summary').show();
        }
    }

    // Search function
    function performSearch(url = null) {
        let status = $('#status').val();
        let searchbox = $('#searchbox').val();
        let sort = $('#sort').val();

        // If a specific URL is provided (like from pagination), use it directly
        if (url) {
            // Parse the URL to maintain existing parameters
            const urlObj = new URL(url, window.location.origin);
            const params = new URLSearchParams(urlObj.search);

            // Update with current filter values if they exist
            if (status) params.set('status', status);
            if (searchbox) params.set('searchbox', searchbox);
            if (sort) params.set('sort', sort);

            url = urlObj.pathname + '?' + params.toString();
        } else {
            // Build new URL with current parameters
            const params = new URLSearchParams();
            if (status) params.append('status', status);
            if (searchbox) params.append('searchbox', searchbox);
            if (sort) params.append('sort', sort);
            url = "{{ route('bundles.search') }}?" + params.toString();
        }

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                $('#result').html('<tr><td colspan="9" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');
                $('.search').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Searching...');
                $('#results-summary').hide();
            },
            success: function(data) {
                if (data.success !== false) {
                    $('#result').html(data.results);
                    $('#pagination-links').html(data.pagination);
                    updateResultsSummary(data);
                } else {
                    toastr.error(data.message || 'Search failed');
                    $('#result').html('<tr><td colspan="9" class="text-center py-4 text-danger">' + (data.message || 'Search failed') + '</td></tr>');
                }
            },
            error: function(xhr) {
                console.error('Search error:', xhr);
                let errorMessage = 'Error searching bundles';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    errorMessage += ': ' + xhr.statusText;
                }
                toastr.error(errorMessage);
                $('#result').html('<tr><td colspan="9" class="text-center py-4 text-danger">' + errorMessage + '</td></tr>');
            },
            complete: function() {
                $('.search').prop('disabled', false)
                    .html('<i class="fas fa-search me-1"></i> Search');
            }
        });
    }

    // Search button handler
    $(document).on('click', '.search', function(e) {
        e.preventDefault();
        performSearch();
    });

    // Enter key handler for search box
    $(document).on('keypress', '#searchbox', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            performSearch();
        }
    });

    // Pagination handler
    $(document).on('click', '#pagination-links a', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        performSearch(href);
    });

    // Status and sort filter change handlers
    $(document).on('change', '#status, #sort', function() {
        performSearch();
    });

    // Checkbox functionality
    function updateDeleteButton() {
        const checkedCount = $('.bundle-checkbox:checked').length;
        $('#batch-delete-btn').toggleClass('disabled', checkedCount === 0)
            .html(checkedCount > 0
                ? `<i class="far fa-trash-alt"></i> Delete (${checkedCount})`
                : `<i class="far fa-trash-alt"></i> Delete Selected`);
    }

    $(document).on('change', '#select-all', function() {
        $('.bundle-checkbox').prop('checked', $(this).prop('checked'));
        updateDeleteButton();
    });

    $(document).on('change', '.bundle-checkbox', function() {
        const allChecked = $('.bundle-checkbox:checked').length === $('.bundle-checkbox').length;
        $('#select-all').prop('checked', allChecked);
        updateDeleteButton();
    });

    // Batch delete
    $(document).on('click', '#batch-delete-btn:not(.disabled)', function(e) {
        e.preventDefault();
        const selectedIds = $('.bundle-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();

        if (selectedIds.length === 0) return;
        if (!confirm(`Are you sure you want to delete ${selectedIds.length} selected bundle(s)?`)) return;

        const $btn = $(this).prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: "{{ route('bundles.batch-delete') }}",
            type: 'POST',
            data: {
                ids: selectedIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                toastr.success(response.message);
                performSearch();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Delete failed');
            },
            complete: function() {
                $btn.prop('disabled', false)
                    .html('<i class="far fa-trash-alt"></i> Delete Selected');
                $('#select-all').prop('checked', false);
            }
        });
    });

    // Single delete
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this bundle?')) return;

        const $btn = $(this);
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: $btn.attr('href'),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                toastr.success(response.message);
                performSearch();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Delete failed');
            },
            complete: function() {
                $btn.html('<i class="far fa-trash-alt"></i>');
            }
        });
    });
});
</script>
@endsection
