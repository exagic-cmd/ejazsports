<?php $customers = (array) $result->data->customers;?>
@if(count($customers) == 0)
    <span> no result found..</span>
    @endif
@foreach($result->data->customers as $customer)
    <a href="#!" onclick="customerInfo({{$customer->id}})"><li class="recordc">
        <div class="customer_name">
            {{$customer->first_name}} {{$customer->last_name}}
        </div> <div class="customer_contact"><i class="fa fa-envelope"></i>
            {{$customer->email}}
        </div> <div class="customer_contact"><i class="fa fa-phone"></i>
            {{$customer->phone_number}}
            </div></li></a>
@endforeach
