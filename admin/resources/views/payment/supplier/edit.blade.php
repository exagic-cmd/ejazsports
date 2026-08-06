@extends('layouts.app')


@section('content')

<div class="row">
    <div class="col-12">
        <div class="content-header">
            <h2 class="content-title">Update Supplier Payment</h2>
            <div>
                <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
            </div>
        </div>
    </div>
    <form action="{{route('supplier-payments.update',$supplierPayment->id)}}" method="post" id="form" enctype="multipart/form-data" autocomplete="false" style="display: contents">
        @csrf
        @method('PUT')
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Basic</h4>
                </div>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="card-body">


                    <div @class('row')>
                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Supplier </label>
                            <select id="supplier_id" class="form-control select2 @error('supplier_id') is-invalid @enderror" name="supplier_id">
                                <option value="">None</option>
                                @foreach($suppliers as $s)
                                <option value="{{$s->id}}" {{$supplierPayment->supplier_id == $s->id ? 'selected' : '' }}>{{$s->name}} - {{$s->mobile_number}} - {{$s->ntn_number}}</option>
                                @endforeach
                            </select>


                            @error('supplier_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Date</label>
                            <input  type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{date('Y-m-d',strtotime($supplierPayment->date))}}" >
                            @error('date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                

                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Payment Method</label>
                            <select class="form-control select2" name="payment_method" id="payment_method">
                                <option {{$supplierPayment->payment_method == \App\Models\SupplierPayment::CASH ? 'selected' : '' }} value="{{\App\Models\SupplierPayment::CASH}}">Cash</option>
                                <option {{$supplierPayment->payment_method == \App\Models\SupplierPayment::BANK_TRANSFER ? 'selected' : '' }} value="{{\App\Models\SupplierPayment::BANK_TRANSFER}}">Bank Transfer</option>
                                <option {{$supplierPayment->payment_method == \App\Models\SupplierPayment::CHEQUE ? 'selected' : '' }} value="{{\App\Models\SupplierPayment::CHEQUE}}">Cheque</option>
                            </select>
                            @error('payment_method')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" style="display: {{$supplierPayment->payment_method == \App\Models\SupplierPayment::BANK_TRANSFER ? 'block' : 'none'}};" id="depositor_bank">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Depositor Bank</label>
                        <input  type="text" name="depositor_bank" class="form-control @error('depositor_bank') is-invalid @enderror" value="{{$supplierPayment->depositor_bank}}" >

                        @error('depositor_bank')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: {{$supplierPayment->payment_method == \App\Models\SupplierPayment::CHEQUE ? 'block' : 'none'}};" id="cheque_no">
                <div class="col-lg-6" >
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Cheque #</label>
                        <input  type="text" name="cheque_no" class="form-control @error('cheque_no') is-invalid @enderror" value="{{$supplierPayment->cheque_no}}" >

                        @error('cheque_no')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                    <div class="col-lg-6" >
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Cheque Date</label>
                            <input  type="date" name="cheque_date" class="form-control @error('cheque_date') is-invalid @enderror" value="{{$supplierPayment->cheque_date}}" >

                            @error('cheque_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                     <div class="col-lg-6">
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Cheaque Clear</label>
                            <select class="form-control select2" name="is_clear">
                                @if($supplierPayment->is_clear)
                                <option selected  value="1">Yes</option>
                                <option  value="0">No</option>
                                @else
                                 <option  value="1">Yes</option>
                                <option  selected value="0">No</option>
                                @endif
                                
                            </select>
                            @error('is_clear')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>


                <div @class('row')>

                <div class="col-lg-6">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Note</label>
                        <textarea name="notes" class="form-control" cols="5">{{$supplierPayment->notes}}</textarea>
                        @error('notes')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>



                <div class="col-lg-6">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Received By</label>
                        <input  type="text" name="received_by" class="form-control @error('received_by') is-invalid @enderror" value="{{$supplierPayment->received_by}}" >
                        @error('received_by')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>



                <div class="col-lg-3">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Paid Amount</label>
                        <input  type="number" id="paid_amount" name="paid_amount" class="form-control" value="{{$supplierPayment->amount + $supplierPayment->tax}}" >
                    </div>
                </div>
                
                 <div class="col-lg-3">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Discount</label>
                        <input  type="number" name="discount" class="form-control" value="{{$supplierPayment->discount}}" >
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Tax %</label>
                        <input  type="number" onkeyup="updateTax()" id="tax_per" name="tax_per"  class="form-control" value="{{number_format((float)($supplierPayment->tax / ($supplierPayment->amount + $supplierPayment->tax)),3)}}">

                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Tax Amount</label>
                        <input style="cursor: not-allowed" type="number" readonly  id="tax_amount" name="tax_amount"  class="form-control" value="{{$supplierPayment->tax}}">

                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Net Amount</label>
                        <input style="cursor: not-allowed" type="number" readonly  id="net_amount" name="net_amount"  class="form-control" value="{{$supplierPayment->amount }}">

                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Documents</label>
                        <input  type="file" name="file"  class="form-control"  >

                    </div>
                </div>


            </div>

        </div>
</div>

</div>

</form>
</div>



@stop

@section('js')
<script>
    $('.select2').select2();

    var formSubmitting = false;
    var setFormSubmitting = function() { formSubmitting = true; };

    window.onload = function() {
        window.addEventListener("beforeunload", function (e) {
            if (formSubmitting) {
                return undefined;
            }

            var confirmationMessage = 'It looks like you have been editing something. '
                + 'If you leave before saving, your changes will be lost.';

            (e || window.event).returnValue = confirmationMessage; //Gecko + IE
            return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
        });
    };

    $(document).on('change','#supplier_id',function(e) {

        $.ajax({
            url: "{{route('supplier-payments.unpaid-invoices')}}",
            type: 'GET',
            data: {supplier_id: $('#supplier_id').val() },
            success: function (data) {
                var htmlOptions = [];
                var select = $('#invoice_id');
                if( data.length ){
                    for( item in data ) {
                        html = '<option value="' + data[item].invoice_no + '">' + data[item].invoice_no + '</option>';
                        htmlOptions[htmlOptions.length] = html;
                    }

                    // here you will empty the pre-existing data from you selectbox and will append the htmlOption created in the loop result
                    select.empty().append( htmlOptions.join('') );
                }
            }
        });
    });


    $(document).on('change','#payment_method',function(e) {

        val = $(this).val();

        if(val == {{\App\Models\SupplierPayment::CASH}}) {
            document.getElementById('depositor_bank').style.display = 'none';
            document.getElementById('cheque_no').style.display = 'none';
        }
    else if(val == {{\App\Models\SupplierPayment::BANK_TRANSFER}}) {
            document.getElementById('depositor_bank').style.display = 'block';
            document.getElementById('cheque_no').style.display = 'none';
        }
    else if(val == {{\App\Models\SupplierPayment::CHEQUE}}) {
            document.getElementById('depositor_bank').style.display = 'none';
            document.getElementById('cheque_no').style.display = 'block';
        }
    });

    function updateTax() {

        tax_amount = parseInt(parseInt($('#paid_amount').val()) * (parseFloat($('#tax_per').val())));

        $('#tax_amount').val(tax_amount);
        $('#net_amount').val(parseInt($('#paid_amount').val()) - tax_amount);

    }

</script>


@stop

