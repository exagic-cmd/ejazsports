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
            <th scope="col">Available Qty</th>
            <th scope="col">Last Purchase Price</th>
            <th scope="col">Purchase Qty</th>
        </tr>
        </thead>
        <tbody><?php $sr = 1;?>
        @foreach($supplier->brands as $b)
            <tr>
                <td></td>

                <td><b>{{strtoupper($b->brand->title)}}</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach($b->brand->products as $p)
                <?php $pName = $p->title;?>
                @foreach($p->variants as $v)
            <tr>
                <td>{{$sr++}}</td>
                <td>{{$p->code}}</td>
                @if($loop->iteration == 1)
                <td><b>{{$p->title}}</b></td>
                @else
                    <td><b>&nbsp;&nbsp; // &nbsp;&nbsp;</b></td>
                    @endif
                <td>{{$v->barcode}}</td>
                <td>{{$v->shade}}</td>
                <td>{{$v->size}}</td>
                <td>{{$v->available_stock}}</td>
                <td>{{number_format($lastPurchasePrice[$p->id])}}</td>
                <td><input type="number" @class('form-control') name="p_qty[]" id="p_qty" value="0"> </td>
                <input type="hidden" value="{{$v->id}}" name="variant_ids[]">
                <input type="hidden" value="{{$p->id}}" name="product_ids[]">
                <input type="hidden" value="{{$lastPurchasePrice[$p->id]}}" name="p_tps[]">
            </tr>
                    @endforeach
                @endforeach
        @endforeach

        </tbody>
    </table>
</div>
<!-- table-responsive //end -->
