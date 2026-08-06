@extends('layouts.app')

@section('content')
<div class="content-header">
    <div>
        <h2 class="content-title card-title">Bundle Details</h2>
        <p>Detailed information about this bundle</p>
    </div>
    <div>
        <a href="{{ route('bundles.index') }}" class="btn btn-light rounded">Back to List</a>
        @can('Edit Bundle')
        <a href="{{ route('bundles.edit', $bundle->id) }}" class="btn btn-primary rounded">Edit Bundle</a>
        @endcan
         @can('Delete Bundle')
        <form action="{{ route('bundles.destroy', $bundle->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger rounded"
                    onclick="return confirm('Are you sure you want to delete this bundle?')">
                Delete Bundle
            </button>
        </form>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-4">
                    @if($bundle->firstImage)
                    <img src="{{ asset('storage/'.$bundle->firstImage->path) }}"
                         class="img-fluid rounded"
                         alt="{{ $bundle->name }}">
                    @else
                    <img src="{{ asset('imgs/default-bundle.jpg') }}"
                         class="img-fluid rounded"
                         alt="Default Bundle Image">
                    @endif
                </div>

                <div class="gallery">
                    @foreach($bundle->images as $image)
                    <a href="{{ asset('storage/'.$image->path) }}" class="gallery-item" data-fancybox="gallery">
                        <img src="{{ asset('storage/'.$image->path) }}"
                             class="img-thumbnail"
                             style="width: 80px; height: 80px; object-fit: cover;"
                             alt="Bundle Image">
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="col-md-8">
                <h3>{{ $bundle->name }}</h3>
                <p class="text-muted">{{ $bundle->short_desc }}</p>
                <hr>
                <div class="mb-3">
                    <h5>Short Description</h5>
                    <p>{{ $bundle->short_desc ?? 'No detailed description available' }}</p>
                </div>
                <div class="mb-3">
                    <h5>Full Description</h5>
                    <p>{{ $bundle->full_desc ?? 'No detailed description available' }}</p>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Pricing Information</h5>
                                <div class="price-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0">Purchase Price</h6>
                                            <small class="text-muted">Cost price of included items</small>
                                        </div>
                                        <strong class="fs-5">Rs.{{ number_format($bundle->purchase_price, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Additional Price</h6>
                                            <small class="text-muted">Markup/retail price</small>
                                        </div>
                                        <strong class="fs-5">Rs.{{ number_format($bundle->additional_price, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Bundle Status</h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Status:</span>
                                    <span class="badge rounded-pill {{ $bundle->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $bundle->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Products Included:</span>
                                    <strong>{{ $bundle->variants->count() }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <h5>Included Products</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Variant</th>
                                <th class="text-end">Purchase Price</th>
                                <th class="text-end">Additional Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $uniqueProducts = [];
                                $totalPurchase = 0;
                                $totalAdditional = 0;
                            @endphp

                            @foreach($bundle->variants as $variant)
                                @php
                                    $productKey = $variant->product_id . '-' . ($variant->product_variant_id ?? '0');
                                    if (in_array($productKey, $uniqueProducts)) {
                                        continue;
                                    }
                                    $uniqueProducts[] = $productKey;

                                    // Calculate totals
                                    $purchasePrice = $variant->product_variant_id && $variant->variant
                                        ? $variant->variant->purchase_price
                                        : ($variant->product ? $variant->product->purchase_price : 0);

                                    $additionalPrice = $variant->product_variant_id && $variant->variant
                                        ? $variant->variant->additional_price
                                        : ($variant->product ? $variant->product->price : 0);

                                    $totalPurchase += $purchasePrice;
                                    $totalAdditional += $additionalPrice;
                                @endphp

                                <tr>
                                    <td>
                                        @if($variant->product)
                                            {{ $variant->product->title }}
                                        @else
                                            <span class="text-danger">Product not available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($variant->product_variant_id && $variant->variant)
                                            {{ $variant->variant->size ?? 'N/A' }}
                                            ({{ $variant->variant->shade ?? 'N/A' }})
                                        @else
                                            Default variant
                                        @endif
                                    </td>
                                    <td class="text-end">Rs.{{ number_format($purchasePrice, 2) }}</td>
                                    <td class="text-end">Rs.{{ number_format($additionalPrice, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Totals:</th>
                                <th class="text-end">Rs.{{ number_format($totalPurchase, 2) }}</th>
                                <th class="text-end">Rs.{{ number_format($totalAdditional, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {});
</script>
<style>
    .price-section {
        border-left: 4px solid #f8f9fa;
        padding-left: 1rem;
    }
    .price-section h6 {
        font-weight: 600;
    }
</style>
@endsection
