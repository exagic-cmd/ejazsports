@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">New Customer Reminder</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>
        <form action="{{route('followup.store')}}" method="post" id="form"  autocomplete="false" style="display: contents">
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
                                    <label for="product_name" class="form-label">Customer </label>
                                    <select id="supplier_id" class="form-control select2 @error('customer_id') is-invalid @enderror" name="customer_id">
                                        <option value="">None</option>
                                        @foreach($customers as $s)
                                            <option value="{{$s->id}}">{{$s->first_name}} - {{$s->phone_number}}</option>
                                        @endforeach
                                    </select>


                                    @error('customer_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Date</label>
                                    <input  type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror"  >
                                    @error('date')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                         
                        </div>


                        


                        <div @class('row')>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Remarks</label>
                                    <textarea name="remarks" class="form-control" cols="5"></textarea>
                                    @error('remarks')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
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

        
    </script>


@stop

