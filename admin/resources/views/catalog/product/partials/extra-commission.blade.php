{{-- Extra commission (product bonus) paid to the salesman on top of the normal commission --}}
@php
    $extraComOn = old('extra_com_status', $product->extra_com_status ?? 0);
    $extraComType = old('extra_com_type', $product->extra_com_type ?? 1);
    $extraComUnit = $extraComType == 2 ? '%' : 'Rs';
@endphp

<h5 class="mt-2 mb-3">Extra Commission (Product Bonus)</h5>

<div class="row mb-4">
    <label class="col-lg-3 col-form-label">Status</label>
    <div class="col-lg-9">
        <label class="form-check my-2">
            <input type="checkbox" name="extra_com_status" id="extra_com_status" value="1" class="form-check-input" @if($extraComOn) checked @endif>
            <span class="form-check-label">Give extra commission when this product is sold (also inside bundles)</span>
        </label>
    </div>
</div>

<div id="extra_com_fields" class="{{ $extraComOn ? '' : 'd-none' }}">

<div class="row mb-4">
    <label class="col-lg-3 col-form-label">Type</label>
    <div class="col-lg-9">
        <select name="extra_com_type" id="extra_com_type" class="form-select @error('extra_com_type') is-invalid @enderror">
            <option value="1" @if($extraComType == 1) selected @endif>Fixed Rs per piece</option>
            <option value="2" @if($extraComType == 2) selected @endif>% of product margin</option>
        </select>
        @error('extra_com_type')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-4">
    <label class="col-lg-3 col-form-label">Retail Value</label>
    <div class="col-lg-9">
        <div class="input-group">
            <input type="number" step="any" min="0" name="extra_com_retail" class="form-control extra-com-value @error('extra_com_retail') is-invalid @enderror" value="{{ old('extra_com_retail', $product->extra_com_retail ?? 0) }}" placeholder="Type here">
            <span class="input-group-text extra-com-unit">{{ $extraComUnit }}</span>
        </div>
        <small class="text-muted">Paid on sales to the walk-in (retail) customer</small>
        @error('extra_com_retail')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-4">
    <label class="col-lg-3 col-form-label">Wholesale Value</label>
    <div class="col-lg-9">
        <div class="input-group">
            <input type="number" step="any" min="0" name="extra_com_whole" class="form-control extra-com-value @error('extra_com_whole') is-invalid @enderror" value="{{ old('extra_com_whole', $product->extra_com_whole ?? 0) }}" placeholder="Type here">
            <span class="input-group-text extra-com-unit">{{ $extraComUnit }}</span>
        </div>
        <small class="text-muted">Paid on sales to all other customers</small>
        <small id="extra_com_limit" class="d-block text-danger d-none">Percentage can't be more than 100%, so it was set to 100.</small>
        @error('extra_com_whole')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
</div>
</div>

<script>
    document.getElementById('extra_com_status').addEventListener('change', function () {
        document.getElementById('extra_com_fields').classList.toggle('d-none', !this.checked);
    });

    // % of margin can't go above 100 and no value can be negative
    function limitExtraCom() {
        var isPercent = document.getElementById('extra_com_type').value == 2;
        var clamped = false;
        document.querySelectorAll('.extra-com-value').forEach(function (el) {
            var v = parseFloat(el.value);
            if (v < 0) el.value = 0;
            if (isPercent && v > 100) { el.value = 100; clamped = true; }
        });
        document.getElementById('extra_com_limit').classList.toggle('d-none', !clamped);
    }

    document.getElementById('extra_com_type').addEventListener('change', function () {
        var unit = this.value == 2 ? '%' : 'Rs';
        document.querySelectorAll('.extra-com-unit').forEach(function (el) { el.textContent = unit; });
        limitExtraCom();
    });
    document.querySelectorAll('.extra-com-value').forEach(function (el) {
        el.addEventListener('input', limitExtraCom);
    });

    // if saving failed on a bonus field, open the tab that holds it so the error is visible
    @if($errors->hasAny(['extra_com_type', 'extra_com_retail', 'extra_com_whole']))
    document.addEventListener('DOMContentLoaded', function () {
        var section = document.getElementById('extra_com_fields').closest('section.content-body');
        if (section && typeof updateDiv === 'function') updateDiv(section.id.replace('d-', ''));
    });
    @endif
</script>
