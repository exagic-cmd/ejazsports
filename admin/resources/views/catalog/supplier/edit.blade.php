@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Supplier</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
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
                    <form action="{{route('suppliers.update',$supplier->id)}}" method="post" id="form">
                        @csrf
                        @method('PUT')
                      
                        <div class="mb-4 position-relative">
                            <label for="product_name" class="form-label">Name</label>
                            <input type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" id="product_name" value="{{ $supplier->name }}" autocomplete="off">
                            <div id="supplier-suggestions-box" class="name-suggestions-dropdown" style="display:none;"></div>
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!--
                        <div class="mb-4">
                            <label for="product_name" class="form-label">NTN Number</label>
                            <input  type="text" name="ntn_number" placeholder="Type here" class="form-control @error('ntn_number') is-invalid @enderror" id="product_name" value="{{ $supplier->ntn_number }}">
                            @error('ntn_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        -->

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Phone Number</label>
                            <input  type="text" name="phone_number" placeholder="Type here" class="form-control @error('phone_number') is-invalid @enderror" id="product_name" value="{{ $supplier->mobile_number }}">
                            @error('phone_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!--
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Office Number</label>
                            <input  type="text" name="office_number" placeholder="Type here" class="form-control @error('office_number') is-invalid @enderror" id="product_name" value="{{ $supplier->office_number }}">
                            @error('office_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Email</label>
                            <input  type="text" name="email" placeholder="Type here" class="form-control @error('email') is-invalid @enderror" id="product_name" value="{{ $supplier->email }}">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        -->
                        
                        <div class="mb-4">
                            <label for="address" class="form-label">Area</label>
                            <input  type="text" name="address" placeholder="Type here" class="form-control @error('address') is-invalid @enderror"  value="{{ $supplier->address ?? '' }}">
                            @error('address')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cargo_service" class="form-label">Cargo Service Name</label>
                            <input  type="text" name="cargo_service" placeholder="Type here" class="form-control @error('cargo_service') is-invalid @enderror"  value="{{ $supplier->cargo_service ?? '' }}">
                            @error('cargo_service')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Opening Balance</label>
                            <input  type="text" name="opening_balance" placeholder="Type here" class="form-control @error('opening_balance') is-invalid @enderror" value="{{ $supplier->opening_balance }}">
                            @error('opening_balance')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        

                        <div class="mb-4">
                            <label for="role" class="form-label">Status</label>
                            <select class="form-control select2"
                                    name="status" required>

                                <option value="1"  {{$supplier->status ? 'selected' : ''}}>Active</option>
                                <option value="0" {{$supplier->status ? '' : 'selected'}}>InActive</option>

                            </select>
                            @error('status')
                            <span class="text-danger text-left">{{ $errors->first('status') }}</span>
                            @enderror
                        </div>

                    

                    </form>
                </div>
            </div>

        </div>

    </div>



@stop

@section('css')
<style>
    .position-relative { position: relative; }
    .name-suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        max-height: 280px;
        overflow-y: auto;
        margin-top: 4px;
    }
    .name-suggestions-header {
        background-color: #fef2f2;
        color: #991b1b;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 600;
        border-bottom: 1px solid #fecaca;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .name-suggestion-item {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.15s ease;
    }
    .name-suggestion-item:hover {
        background-color: #f8fafc;
    }
    .name-suggestion-item:last-child {
        border-bottom: none;
    }
    .suggestion-info {
        flex-grow: 1;
    }
    .suggestion-title {
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }
    .suggestion-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }
    .suggestion-actions {
        display: flex;
        gap: 6px;
        margin-left: 10px;
    }
</style>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var max_fields      = 100; //maximum input boxes allowed
            var wrapper         = $(".input_fields_wrap"); //Fields wrapper
            var add_button      = $(".add_field_button"); //Add button ID

            var x = 1; //initlal text box count
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    x++; //text box increment
                    $(wrapper).prepend('<div><br><br><div class="mb-4"><label for="product_name" class="form-label">Name</label> <select class="form-control select2" name="brand_id[]">@foreach($brands as $brand)<option value="{{$brand->id}}">{{$brand->title}}</option>@endforeach</select></div><div class="mb-4"><label for="product_name" class="form-label">Lead Time</label><input  type="number" name="lead[]" placeholder="Type here" class="form-control"> </div> <div class="mb-4"> <label for="product_name" class="form-label">Margin (%)</label><input  type="number" name="margin[]" placeholder="Type here" class="form-control "  > </div> <div class="mb-4"> <label for="product_name" class="form-label">Payment Terms</label> <select class="form-control select2" name="payment_terms[]"> <option value="{{\App\Models\SupplierBrand::CASH}}">Cash</option> <option value="{{\App\Models\SupplierBrand::CREDIT}}">Credit</option> <option value="{{\App\Models\SupplierBrand::SALE_BASIS}}">Sale Basis</option> </select> </div><a href="#" class="remove_field">Remove</a>'); //add input box

                    $('.select2').select2();

                }
            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); x--;
            })
        });
    </script>

    <script>
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

        const currentSupplierId = "{{ $supplier->id }}";
        let debounceTimer = null;
        function fetchSupplierSuggestions() {
            clearTimeout(debounceTimer);
            const query = $('input[name="name"]').val().trim();

            if (query.length < 2) {
                $('#supplier-suggestions-box').hide().empty();
                return;
            }

            debounceTimer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('supplier.name-suggestions') }}",
                    type: "GET",
                    data: { q: query, current_id: currentSupplierId },
                    success: function(data) {
                        const container = $('#supplier-suggestions-box');
                        container.empty();

                        if (data && data.length > 0) {
                            let html = `<div class="name-suggestions-header">
                                ⚠️ ${data.length} other similar vendor/supplier(s) found!
                            </div>`;

                            data.forEach(function(item) {
                                html += `
                                <div class="name-suggestion-item">
                                    <div class="suggestion-info">
                                        <div class="suggestion-title">${item.name}</div>
                                        <div class="suggestion-sub">
                                            📞 ${item.phone || 'N/A'} ${item.email ? '| ✉️ ' + item.email : ''} | Opening Bal: Rs. ${item.opening_balance}
                                        </div>
                                    </div>
                                    <div class="suggestion-actions">
                                        <a href="${item.edit_url}" target="_blank" class="btn btn-sm btn-outline-info" style="font-size: 11px; padding: 3px 8px;">View / Edit ↗</a>
                                    </div>
                                </div>`;
                            });

                            container.html(html).show();
                        } else {
                            container.hide();
                        }
                    },
                    error: function() {
                        $('#supplier-suggestions-box').hide().empty();
                    }
                });
            }, 250);
        }

        $(document).on('input', 'input[name="name"]', function() {
            fetchSupplierSuggestions();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.position-relative').length) {
                $('#supplier-suggestions-box').hide();
            }
        });
    </script>
@endsection




