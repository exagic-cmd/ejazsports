<?php $orders = (array) $result->data->orders;?>
@if(count($orders) == 0)
<span> no result found..</span>
@endif
@foreach($result->data->orders as $o)
<li class="record" data-order-id="{{$o->id}}">
    <div class="order_id">
        #{{$o->order_no}}
    </div>
    <div class="order_date">
        {{date('M d, Y',strtotime($o->created_at))}}
        <br>
        {{date('h:i A',strtotime($o->created_at))}}
    </div>
    <div class="order_total">
        Rs. {{number_format($o->total_amount)}}
    </div>
</li>
@endforeach
