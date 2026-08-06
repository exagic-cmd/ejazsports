<div class="card mb-4">
    <div class="card-header">
        <h4>Product Info</h4>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Title</label>
            <input readonly value="{{$product->title}}"  type="text" name="scan" id="scan" placeholder="Scan here" class="form-control" >
        </div>
    </div>

   

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Shade</label>
            <input  readonly value="{{count($product->variants) > 0 ? $product->variants[0]->shade : ''}}"  type="text" name="shade" id="shade"  class="form-control" >
        </div>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Size</label>
            <input readonly value="{{count($product->variants) > 0 ? $product->variants[0]->size : ''}}"  type="text" name="size" id="size"  class="form-control" >
        </div>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Status</label>
            <input readonly  value="{{$product->status ? 'ACTIVE' : 'INACTIVE'}}"  type="text"  class="form-control" >
        </div>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Scanned Qty</label>
            <input    type="text" value="" name="scan_qty" id="in_hand_qty" placeholder="Enter here" class="form-control" >
        </div>
    </div>



    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Total Trade Price</label>
            <input type="number" @class('form-control total-trade-price') name="total_t_price"  value="0">
        </div>
    </div>

</div>

<input type="hidden" name="product_id" value="{{$product->id}}">
<input type="hidden" name="variant_id" value="{{count($product->variants) > 0 ? $product->variants[0]->id : 0}}">
