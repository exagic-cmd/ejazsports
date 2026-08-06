@extends('layouts.app')

@section('css')
<style>
    /* General Link Styling */
    a { color: #0060B6; text-decoration: none; }
    a:hover { color: #00A0C6; text-decoration: none; cursor: pointer; }

    /* Tooltip Styling (kept from original) */
    .tooltip-toggle { cursor: pointer; position: relative; }
    .tooltip-toggle::before {
        position: absolute; top: -80px; left: -80px; background-color: green; border-radius: 5px; color: #fff;
        content: attr(data-tooltip); padding: 1rem; text-transform: none; transition: all 0.5s ease; width: 300px;
    }
    .tooltip-toggle::after {
        position: absolute; top: -12px; left: 9px; border-left: 5px solid transparent; border-right: 5px solid transparent;
        border-top: 5px solid green; content: " "; font-size: 0; line-height: 0; margin-left: -5px; width: 0;
    }
    .tooltip-toggle::before, .tooltip-toggle::after {
        color: #efefef; font-family: monospace; font-size: 16px; opacity: 0; pointer-events: none; text-align: left;
    }
    .tooltip-toggle:hover::before, .tooltip-toggle:hover::after { opacity: 1; transition: all 0.75s ease; }
</style>

{{-- DataTables & Select2 CSS --}}
<link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="content-header d-flex align-items-center justify-content-between">
            <h2 class="content-title">New Purchase Order</h2>
            <div>
                <button onclick="setFormSubmitting();document.getElementById('form').submit()"
                        class="btn btn-md rounded font-sm hover-up">Save</button>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Basic</h4>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-0">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body">
                <form action="{{ route('purchase-orders.store') }}" method="post" id="form" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            {{-- Supplier --}}
                            <div class="mb-2">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select id="supplier_id" class="form-control select2 @error('supplier_id') is-invalid @enderror" name="supplier_id">
                                    <option value="">None</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }} - {{ $s->mobile_number }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Brand --}}
                            <div class="mb-2">
                                <label for="brand_id" class="form-label">Brands</label>
                                <select id="brand_id" class="form-control select2 @error('brand_id') is-invalid @enderror" name="brand_id">
                                    <option value="">None</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->title }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Comment --}}
                            <div class="mb-4">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea id="comment" name="comment" class="form-control" rows="3"></textarea>
                                @error('comment')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            {{-- Date --}}
                            <div class="mb-4">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ $today }}">
                                @error('date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Shipment At --}}
                            <div class="mb-4">
                                <label for="store_id" class="form-label">Shipment At</label>
                                <select id="store_id" class="form-control select2 @error('store_id') is-invalid @enderror" name="store_id">
                                    @foreach($stores as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Hidden fields to submit filtered arrays --}}
                    <input type="hidden" name="s_p_qty[]" id="s_p_qty" value="0">
                    <input type="hidden" name="s_variant_ids[]">
                    <input type="hidden" name="s_product_ids[]">
                    <input type="hidden" name="s_p_tps[]">
                </form>

                <hr>

                {{-- Products Section (hidden until Brand is chosen & rows exist) --}}
                <div class="col-lg-12" id="productDiv" style="display:none;">
                    {{-- Category filter (will be moved next to DT search) --}}
                    <select id="category_id" class="form-control mb-1 d-none select2" style="display:none">
                        <option value="">-- All Categories --</option>
                        {{-- Options will be rebuilt dynamically based on selected brand --}}
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>#Sr</th>
                                    <th>Product Title</th>
                                    <th>Category</th>
                                    <th>Shade</th>
                                    <th>Size</th>
                                    <th>Available Qty</th>
                                    <th>Purchase Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sr = 1; @endphp
                                @foreach($products as $p)
                                    @php
                                        $cat = $p->category ?? null;
                                        $categoryId = $cat->category->id ?? $cat->id ?? '';
                                        $categoryTitle = $cat->category->title ?? $cat->title ?? '';
                                        $brandId = $p->brand_id ?? '';
                                    @endphp

                                    @if(count($p->variants) > 0)
                                        @foreach($p->variants as $v)
                                            <tr data-brand="{{ $brandId }}" data-category="{{ $categoryId }}">
                                                <td>{{ $sr++ }}</td>
                                                <td>{{ $p->title }}</td>
                                                <td>{{ $categoryTitle }}</td>
                                                <td>{{ $v->shade ?? '' }}</td>
                                                <td>{{ $v->size ?? '' }}</td>
                                                <td>{{ $v->available_stock ?? $p->available_stock }}</td>
                                                <td>
                                                    <input type="number" class="form-control" name="p_qty[]" value="0" min="0">
                                                </td>

                                                <input type="hidden" value="{{ $v->id ?? 0 }}" name="variant_ids[]">
                                                <input type="hidden" value="{{ $p->id }}" name="product_ids[]">
                                                <input type="hidden" value="0" name="p_tps[]">
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr data-brand="{{ $brandId }}" data-category="{{ $categoryId }}">
                                            <td>{{ $sr++ }}</td>
                                            <td>{{ $p->title }}</td>
                                            <td>{{ $categoryTitle }}</td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $p->available_stock }}</td>
                                            <td>
                                                <input type="number" class="form-control" name="p_qty[]" value="0" min="0">
                                            </td>

                                            <input type="hidden" value="0" name="variant_ids[]">
                                            <input type="hidden" value="{{ $p->id }}" name="product_ids[]">
                                            <input type="hidden" value="0" name="p_tps[]">
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div> {{-- /.table-responsive --}}
                </div> {{-- /#productDiv --}}
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
{{-- jQuery, DataTables, Buttons, Select2 --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Prepare data arrays before submit
    let formSubmitting = false;
    function setFormSubmitting () {
        const pQty = $("input[name='p_qty[]']").map(function(){ return $(this).val(); }).get();
        const variantIds = $("input[name='variant_ids[]']").map(function(){ return $(this).val(); }).get();
        const productIds = $("input[name='product_ids[]']").map(function(){ return $(this).val(); }).get();
        const pTps = $("input[name='p_tps[]']").map(function(){ return $(this).val(); }).get();

        const sQty = [], sVId = [], sProId = [], sPT = [];
        for (let i = 0; i < pQty.length; i++) {
            const qty = parseInt(pQty[i] || 0, 10);
            if (qty > 0) {
                sQty.push(qty);
                sVId.push(parseInt(variantIds[i] || 0, 10));
                sProId.push(parseInt(productIds[i] || 0, 10));
                sPT.push(parseFloat(pTps[i] || 0));
            }
        }

        $('[name="s_p_qty[]"]').val(sQty);
        $('[name="s_variant_ids[]"]').val(sVId);
        $('[name="s_product_ids[]"]').val(sProId);
        $('[name="s_p_tps[]"]').val(sPT);

        formSubmitting = true;
    }

    // Warn on page leave if not submitted
    window.addEventListener("beforeunload", function (e) {
        if (formSubmitting) return;
        const msg = 'It looks like you have been editing something. If you leave before saving, your changes will be lost.';
        (e || window.event).returnValue = msg;
        return msg;
    });

    $(document).ready(function () {
        // Init Select2 everywhere
        $('.select2').select2({ width: '100%' });

        // --- DataTables Custom Filter: brand + category ---
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'myTable') return true;

            const selectedBrand = $('#brand_id').val();
            const selectedCategory = $('#category_id').val();

            const $tr = $(settings.aoData[dataIndex].nTr);
            const rowBrand = $tr.attr('data-brand') || '';
            const rowCategory = $tr.attr('data-category') || '';

            const brandOk = (!selectedBrand || rowBrand === selectedBrand);
            const categoryOk = (!selectedCategory || rowCategory === selectedCategory);

            return brandOk && categoryOk;
        });

        // Init DataTable
        const table = $('#myTable').DataTable({
            ordering: false,
            paging: true,
            info: false,
            searching: true,
            dom: 'Bfrtip',
            buttons: ['csv', 'excel', 'print']
        });

        const $productDiv = $('#productDiv');
        const $category = $('#category_id');

        // Move Category next to the search field
        (function moveCategoryNextToSearch(){
            if ($category.length) {
                $category.detach();
                const $searchLabel = $("#myTable_filter label");
                $searchLabel.css({ display: 'flex', alignItems: 'center', gap: '10px' });
                $category.prependTo($searchLabel);
                $category.removeClass('d-none').show();
                $category.select2({ width: '250px', placeholder: '-- All Categories --' });
            }
        })();

        // Build Category options for selected brand
        function rebuildCategoryOptionsForBrand(selectedBrand) {
            // Keep only the "All" option
            $category.find('option').not(':first').remove();

            if (!selectedBrand) {
                $category.val('').trigger('change.select2');
                return;
            }

            const uniqueCats = new Map(); // id => title

            table.rows().every(function () {
                const $tr = $(this.node());
                const rowBrand = $tr.attr('data-brand') || '';
                const rowCategoryId = $tr.attr('data-category') || '';

                if (rowBrand === selectedBrand && rowCategoryId) {
                    const rowData = this.data();
                    const rowCategoryTitle = (Array.isArray(rowData) ? rowData[2] : '') || '';
                    if (!uniqueCats.has(rowCategoryId)) {
                        uniqueCats.set(rowCategoryId, rowCategoryTitle);
                    }
                }
            });

            Array.from(uniqueCats.entries())
                .sort((a,b) => (a[1] || '').localeCompare(b[1] || ''))
                .forEach(([id, title]) => {
                    $category.append(new Option(title, id, false, false));
                });

            $category.val('').trigger('change.select2');
        }

        function applyFiltersAndToggleVisibility() {
            const selectedBrand = $('#brand_id').val();

            // Rebuild categories for this brand
            rebuildCategoryOptionsForBrand(selectedBrand);

            // Redraw with filters
            table.draw();

            // Toggle section
            const anyRowVisible = table.rows({ filter: 'applied' }).count() > 0;
            if (selectedBrand && anyRowVisible) {
                $productDiv.show();
            } else {
                $productDiv.hide();
            }
        }

        // Events
        $('#brand_id').on('change', applyFiltersAndToggleVisibility);

        $('#category_id').on('change', function () {
            table.draw();
            const selectedBrand = $('#brand_id').val();
            const anyRowVisible = table.rows({ filter: 'applied' }).count() > 0;
            if (selectedBrand && anyRowVisible) {
                $productDiv.show();
            } else {
                $productDiv.hide();
            }
        });

        // Initial state
        applyFiltersAndToggleVisibility();
    });
</script>
@stop
