<div class="table-responsive" >
    <table id="myTable" class="table table-hover">
        <thead>
        <tr>
            <th>#Sr</th>
            <th scope="col">Product Code</th>
            <th scope="col">Product Title</th>
            <th scope="col">Barcode</th>
            <th scope="col">Shade</th>
            <th scope="col">Size</th>
            <th scope="col">Order Qty</th>
            <th scope="col">Received Qty</th>
            <th scope="col">Expiry Date</th>
            <th scope="col"> Add</th>
        </tr>
        </thead>
        <tbody><?php $sr = 1;?>
        @foreach($purchaseOrder->products as $p)
                    <tr id="r{{$p->product_variant_id}}" style="background-color: #ffdada;">
                        <td>{{$sr++}}</td>
                        <td>{{$p->code}}</td>
                        <td><b>{{$p->product->product_heading}}</b></td>
                        <td>{{$p->variant->barcode}}</td>
                        <td>{{$p->variant->shade}}</td>
                        <td>{{$p->variant->size}}</td>
                        <td >{{$p->quantity}}
                            <input type="hidden"  id="o_qty{{$p->product_variant_id}}" value="{{$p->quantity}}">
                        </td>

                        <td><input type="number" @class('form-control') onkeyup="updateDiff({{$p->product_variant_id}})" name="r_qty[]" id="r_qty{{$p->product_variant_id}}" value="0">
                            <br> <span>Difference : <span id="d_qty{{$p->product_variant_id}}" style="color:red">{{$p->quantity}}</span></span>
                        </td>
                        <td><input type="date" class="form-control" name="expiry_date[]" value=""></td>

                        <td><button class="form-control btn btn-primary" type="button" onclick="addQuantity({{$p->product_variant_id}})" >Add</button>
                            <br> <span>Received : <span id="add_qty{{$p->product_variant_id}}" >0</span></span><br>
                            <span id="text_qty{{$p->product_variant_id}}" ></span>
                        </td>

                        <input type="hidden" value="{{$p->product_variant_id}}" name="variant_ids[]">
                        <input type="hidden" value="{{$p->product_id}}" name="product_ids[]">
                        <input type="hidden" value="0" name="p_tps[]">
                        <input type="hidden" value="1" name="po_product[]">

                        <input type="hidden" value="0" @class('form-control qty') name="received_qty[]" id="t_r_qty{{$p->product_variant_id}}">

                        
                    </tr>


        @endforeach

        </tbody>
    </table>
</div>
<!-- table-responsive //end -->
