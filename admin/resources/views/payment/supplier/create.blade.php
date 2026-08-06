@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">New Supplier Payment</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>
        <form action="{{route('supplier-payments.store')}}" method="post" id="form" enctype="multipart/form-data" autocomplete="false" style="display: contents">
            @csrf
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
                                            <option value="{{$s->id}}">{{$s->name}} - {{$s->mobile_number}} - {{$s->ntn_number}}</option>
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
                                    <input  type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{$today}}" >
                                    @error('date')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <!--<div class="col-lg-6">-->
                            <!--    <div class="mb-4">-->
                            <!--        <label for="product_name" class="form-label">Invoice #</label>-->
                            <!--        <select id="invoice_id" class="form-control select2 @error('invoice_id') is-invalid @enderror" name="invoice_id[]" multiple>-->

                            <!--        </select>-->
                            <!--        @error('invoice_no')-->
                            <!--        <div class="alert alert-danger">{{ $message }}</div>-->
                            <!--        @enderror-->
                            <!--    </div>-->
                            <!--</div>-->

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Payment Method</label>
                                    <select class="form-control select2" name="payment_method" id="payment_method">
                                        <option value="{{\App\Models\SupplierPayment::CASH}}">Cash</option>
                                        <option value="{{\App\Models\SupplierPayment::BANK_TRANSFER}}">Bank Transfer</option>
                                        <option value="{{\App\Models\SupplierPayment::CHEQUE}}">Cheque</option>
                                    </select>
                                    @error('payment_method')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6" style="display: none;" id="depositor_bank">
                            <div class="mb-4">
                                <label for="product_name" class="form-label">Depositor Bank</label>
                                <input  type="text" name="depositor_bank" class="form-control @error('depositor_bank') is-invalid @enderror" value="{{old('depositor_bank')}}" >

                                @error('depositor_bank')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div style="display: none;" id="cheque_no">
                    <div class="col-lg-6" >
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Cheque #</label>
                            <input  type="text" name="cheque_no" class="form-control @error('cheque_no') is-invalid @enderror" value="{{old('cheque_no')}}" >

                            @error('cheque_no')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                            <div class="col-lg-6" >
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Cheque Date</label>
                                    <input  type="date" name="cheque_date" class="form-control @error('cheque_date') is-invalid @enderror" value="{{old('cheque_date')}}" >

                                    @error('cheque_date')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div @class('row')>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Note</label>
                                    <textarea name="notes" class="form-control" cols="5"></textarea>
                                    @error('notes')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Received By</label>
                                    <input  type="text" name="received_by" class="form-control @error('received_by') is-invalid @enderror" value="{{old('received_by')}}" >
                                    @error('received_by')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Paid Amount</label>
                                    <input  type="number" id="paid_amount" name="paid_amount" onkeyup="updateTax()" class="form-control" value="0" >
                                </div>
                            </div>
                            
                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Discount </label>
                                    <input  type="number" onkeyup="updateTax()" id="discount" name="discount"  class="form-control" value="0" >
                                </div>
                            </div>

                            <!--<div class="col-lg-3">-->
                            <!--    <div class="mb-4">-->
                            <!--        <label for="product_name" class="form-label">Tax %</label>-->
                            <!--        <input  type="number" onkeyup="updateTax()" id="tax_per" name="tax_per"  class="form-control" value="0">-->

                            <!--    </div>-->
                            <!--</div>-->

                            <!--<div class="col-lg-3">-->
                            <!--    <div class="mb-4">-->
                            <!--        <label for="product_name" class="form-label">Tax Amount</label>-->
                            <!--        <input style="cursor: not-allowed" type="number" readonly  id="tax_amount" name="tax_amount"  class="form-control" value="0">-->

                            <!--    </div>-->
                            <!--</div>-->

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Net Amount</label>
                                    <input style="cursor: not-allowed" type="number" readonly  id="net_amount" name="net_amount"  class="form-control" value="0">

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

           
            $('#net_amount').val(parseInt($('#paid_amount').val()) + parseInt($('#discount').val()));

        }

    </script>


@stop

