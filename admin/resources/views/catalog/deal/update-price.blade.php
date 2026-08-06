<table class="table table-bordered">
    <thead>
    <tr>
    <th> Sr #</th>
    <th> Product </th>
    <th> Quantity</th>
    <th> Variations</th>
    <th> Price</th>
    </tr>
    </thead>
    <tbody>
    <?php $s = 1;$sum = 0;?>
    @foreach($result as $r)
        <tr>
            <td>{{$s++}}</td>
            <td>{{$r['product']->title}}</td>
            <td>{{$r['quan']}}</td>
            <td>
                <?php $c = 1;?>
                @foreach($r['variant'] as $v)
                    {{$c++}}  -  {{$v->barcode}}  -  {{$v->shade}}  -  {{$v->size}} - {{$v->online_available_stock}} <br>
                    @endforeach

            </td>
            <td>{{number_format($r['product']->price)}}</td>
            <?php $sum += $r['product']->price * $r['quan']; ?>
        </tr>
    @endforeach
    </tbody>
    <tr>
        <td colspan="4"></td>
        <td><b>{{number_format($sum)}}</b></td>
    </tr>
</table>
<input type="hidden" name="total_price" id="total_price" value="{{$sum}}">
<input type="hidden" name="deal_products" value="{{$dealProducts}}">
