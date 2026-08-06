

@if(!$variant)


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
            <label for="product_name" class="form-label">Status</label>
            <input readonly  value="{{$product->status ? 'ACTIVE' : 'INACTIVE'}}" style="background-color: {{$product->status ? 'green' : 'red'}} ; color:white" type="text"  class="form-control" >
        </div>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Available Qty</label>
            <input readonly value="{{$availableQty}}"  type="text" name="available_qty" id="available_qty" placeholder="Scan here" class="form-control" >
        </div>
    </div>


    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Audit Qty</label>
            <input    type="text" value="" name="in_hand_qty" id="in_hand_qty" placeholder="Enter here" class="form-control" >
        </div>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Difference Qty</label>
            <input readonly value="0"  type="text" name="difference_qty" id="difference_qty" placeholder="Scan here" class="form-control" >
        </div>
    </div>
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Stock</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_stock" id="adjust_in_stock" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    <div class="card-body" style="padding: 0.5rem; display:none">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Expiry</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_expiry" id="adjust_in_expiry" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Damage</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_damage" id="adjust_in_damage" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Missing</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_missing" id="adjust_in_missing" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    
     <div class="card-body" style="padding: 0.5rem;display:none">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Tester</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_tester" id="adjust_in_tester" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Reason </label>
            <input   type="text"  name="reason" id="reason" placeholder="Enter Reason" class="form-control" >
        </div>
    </div>
</div>

<input type="hidden" name="product_id" value="{{$product->id}}">
<input type="hidden" name="variant_id" value="0">


@else

<div class="card mb-4">
    <div class="card-header">
        <h4>Product Info</h4>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Title</label>
            <input readonly value="{{$product->product->title}}"  type="text" name="scan" id="scan" placeholder="Scan here" class="form-control" >
        </div>
    </div>
    
     <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Size</label>
            <input readonly value="{{$product->size}}"  type="text" name="scan" id="scan" placeholder="Scan here" class="form-control" >
        </div>
    </div>


 <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Color</label>
            <input readonly value="{{$product->shade}}"  type="text" name="scan" id="scan" placeholder="Scan here" class="form-control" >
        </div>
    </div>

 
    
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Status</label>
            <input readonly  value="{{$product->product->status ? 'ACTIVE' : 'INACTIVE'}}" style="background-color: {{$product->product->status ? 'green' : 'red'}} ; color:white" type="text"  class="form-control" >
        </div>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Available Qty</label>
            <input readonly value="{{$availableQty}}"  type="text" name="available_qty" id="available_qty" placeholder="Scan here" class="form-control" >
        </div>
    </div>


    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Audit Qty</label>
            <input    type="text" value="" name="in_hand_qty" id="in_hand_qty" placeholder="Enter here" class="form-control" >
        </div>
    </div>

    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Difference Qty</label>
            <input readonly value="0"  type="text" name="difference_qty" id="difference_qty" placeholder="Scan here" class="form-control" >
        </div>
    </div>
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Stock</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_stock" id="adjust_in_stock" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    <div class="card-body" style="padding: 0.5rem; display:none">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Expiry</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_expiry" id="adjust_in_expiry" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Damage</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_damage" id="adjust_in_damage" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Missing</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_missing" id="adjust_in_missing" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    
     <div class="card-body" style="padding: 0.5rem;display:none">
        <div class="mb-1">
            <label for="product_name" class="form-label">Adjust In Tester</label>
            <input onkeyup="updateDiff()"  type="text" value="0" name="adjust_in_tester" id="adjust_in_tester" placeholder="Enter here" class="form-control" >
        </div>
    </div>
    
    <div class="card-body" style="padding: 0.5rem;">
        <div class="mb-1">
            <label for="product_name" class="form-label">Reason </label>
            <input   type="text" name="reason" id="reason" placeholder="Enter here" class="form-control" >
        </div>
    </div>
</div>

<input type="hidden" name="product_id" value="{{$product->product->id}}">
<input type="hidden" name="variant_id" value="{{$product->id}}">



@endif
