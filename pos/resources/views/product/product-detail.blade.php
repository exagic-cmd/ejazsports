<div>
    <div class="pos-discount-form">

        <div class="page-content">
            <div class="form-container">

                <div class="pos-customer-fields">
                    <div class="control-group"><label for="gender" ><b>Title :</b> {{$result->data->product->title}}</label>

                    </div>
                    <div class="control-group"><label for="date_of_birth" ><b>Brand : </b>{{$result->data->product->brand ? $result->data->product->brand->title : ''}}</label>

                        <!----></div>
                    <div class="control-group">
                        <label for="first_name" ><b>Categories : </b>
                            @foreach($result->data->product->categories as $c)
                                {{$c->category ? $c->category->title : ''}},
                            @endforeach
                        </label>

                        <!----></div>
                    <div class="control-group">
                        <label for="last_name"><b>Price : </b> {{number_format($result->data->product->price)}}</label>

                        <!----></div>
                    <div class="control-group"><label for="email" ><b>Discounted Price : </b>{{$result->data->product->discount_status ? number_format($result->data->product->price - $result->data->product->discount_amount) : number_format($result->data->product->price)}}</label>

                        <!----></div>

                    <div class="control-group"><label for="email" ><b>Variation </b></label>
                        <table id="table1" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Barcode</th>
                                <th>Shade</th>
                                <th>Size</th>
                                <th>Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = 1;$totalQty = 0;?>
                            @foreach($result->data->product->variants as $v)
                                <tr style="text-align:center;">
                                    <td>{{$sr++}}</td>
                                    <td>{{$v->barcode}}</td>
                                    <td>{{$v->shade}}</td>
                                    <td>{{$v->size}}</td>
                                    <td>{{$v->additional_price}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!----></div>

                    <div class="control-group"><label for="email" ><b>Stock Transactions</b></label>
                        <table id="table2" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = 1;?>
                            @foreach($result->data->transactions as $t)
                                <tr style="text-align:center;">
                                    <td>{{$sr++}}</td>
                                    <td>{{$t->date}}</td>
                                    <td>{{$t->type}}</td>
                                    <td>{{$t->quantity}}</td>
                                    <?php $totalQty += $t->quantity;?>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <th colspan="3">Total Available Stock</th>
                            <th>{{$totalQty}}</th>
                            </tfoot>
                        </table>
                        <!----></div>


                </div>
            </div>
        </div>

    </div>
</div>
