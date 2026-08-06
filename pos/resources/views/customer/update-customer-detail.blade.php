<div class="customer-profile-icon"><i class="fa fa-user-circle"></i></div> <div class="customer-name">
    {{$result->data->customer->first_name}} {{$result->data->customer->last_name}}
</div> <div class="customer-contact"><i class="fa fa-mobile"></i>
    {{$result->data->customer->phone_number}}
</div> <div class="customer-contact"><i class="fa fa-envelope"></i>
    {{$result->data->customer->email}}
</div>
<!--<div class="customer-contact"><i class="fa fa-genderless"></i>-->
<!--@if($result->data->customer->gender == 1)-->
<!--    MALE-->
<!--    @else-->
<!--    FEMALE-->
<!--    @endif-->
<!--</div>-->
@if($result->data->customer->dob)
<div class="customer-contact"><i class="fa fa-calendar-times-o"></i>
{{date('M d, Y',strtotime($result->data->customer->dob))}}
</div>
@endif

<div class="pos-action text-center">
    <button type="button" text="Select Customer" onclick="selectCustomer({{$result->data->customer->id}},'{{$result->data->customer->first_name}} {{$result->data->customer->last_name}}')" class="btn btn-xlg btn-pos-primary"> Select Customer </button>
</div> <div class="customer_modity_action edit_remove"><div class="modify_customer">
        Total Orders : {{$result->data->totalOrders}}
        </div> <div class="remove_customer">
        Balance : {{number_format($result->data->balance)}}
        </div></div>
