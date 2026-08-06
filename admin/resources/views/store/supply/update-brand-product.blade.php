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
            <th scope="col">Available Qty (Store Out)</th>
            <th scope="col">Available Qty (Store In)</th>
            <th scope="col">Send Qty</th>
        </tr>
        </thead>
        <tbody><?php $sr = 1;?>

            @foreach($brand->products as $p)
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
                        <td>{{number_format($variantOutStock[$v->id])}}</td>
                        <td>{{number_format($variantInStock[$v->id])}}</td>
                        <td><input type="number" @class('form-control qty') name="s_qty[]" onkeyup="updateQty()" id="s_qty" value="0"> </td>
                        <input type="hidden" value="{{$v->id}}" name="variant_ids[]">
                        <input type="hidden" value="{{$p->id}}" name="product_ids[]">
                    </tr>
                @endforeach
            @endforeach


        </tbody>
    </table>
</div>
<!-- table-responsive //end -->
