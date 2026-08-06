
    <div class="product-lowstock-panel">
        <div class="pos-product-container"><!---->
            <div class="pos-setting-list row-grid-5">
                <div class="pos-setting row-layout">
                    <div class="setting-list-name">
                        <div class="name">{{ucfirst($result->data->option)}} Bills</div>

                    </div>
                    <div class="setting-list-rate">
                        {{$result->data->billsCount}}
                        <span style="padding-left: 100px;">   {{number_format($result->data->billsAmount)}} </span>
                    </div>
                </div>
                <div class="pos-setting row-layout">
                    <div class="setting-list-name">
                        <div class="name">{{ucfirst($result->data->option)}} Cash Sales</div>

                    </div>
                    <div class="setting-list-rate">
                        {{$result->data->cashBillsCount}}
                        <span style="padding-left: 100px;">   {{number_format($result->data->cashBillsAmount)}} </span>
                    </div>
                </div>
                <div class="pos-setting row-layout">
                    <div class="setting-list-name">
                        <div class="name">{{ucfirst($result->data->option)}} Card Sales</div>

                    </div>
                    <div class="setting-list-rate">
                        {{$result->data->cardBillsCount}}
                        <span style="padding-left: 100px;"> {{number_format($result->data->cardBillsAmount)}} </span>
                    </div>
                </div>
                <div class="pos-setting row-layout">
                    <div class="setting-list-name">
                        <div class="name">{{ucfirst($result->data->option)}} Returns</div>
                    </div>
                    <div class="setting-list-rate">
                        {{$result->data->returnBillsCount}}
                        <span style="padding-left: 100px;"> {{number_format($result->data->returnBillsAmount)}} </span>
                    </div>
                </div>
            </div>
            <div class="pos-table-responsive" style="margin-top: 20px;">
                <table class="pos-table">
                    <thead>
                    <tr>
                        <th class="text-left">Sr #</th>
                        <th class="text-left">Product</th>
                        <th class="text-left">Shade / Size</th>
                        <th class="text-left">Sold Qty.</th>
                        <th class="text-left">Unit Price</th>
                        <th class="text-left">Total Price</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <?php $c = 1;$tQty = 0;$tPrice = 0;$tProduct = 0;?>

                    @foreach($result->data->variants as $var)
                        <tr>
                            <td>{{$c++}}</td>
                            <?php $products = (array) $result->data->products;?>
                            <td>{{$products[$var->variant_id]->title}}</td>
                            <td></td>
                            <td>{{$var->tqy}}</td>
                            <td>{{number_format($var->tprice / $var->tqy)}}</td>
                            <td>{{number_format($var->tprice)}}</td>
                            <?php $tQty += $var->tqy; $tPrice += $var->tprice; $tProduct++;?>
                        </tr>
                    @endforeach
                    <tr><td colspan="6"></td></tr>
                    <tr>
                        <td colspan="3" class="text-left"><b> Total Products  : {{$tProduct}}</b></td>
                        <td class="text-left"><b>{{$tQty}}</b></td>
                        <td></td>
                        <td class="text-left"><b>{{{number_format($tPrice)}}}</b></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

