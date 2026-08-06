@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Customer</h2>
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
                    <form action="{{route('customers.update',$customer->id)}}" method="post" id="form">
                        @csrf
                        @method('PUT')
                        <div class="mb-4 position-relative">
                            <label for="product_name" class="form-label">Name</label>
                            <input type="text" name="first_name" placeholder="Type here" class="form-control @error('first_name') is-invalid @enderror" value="{{ $customer->first_name }}" autocomplete="off">
                            <div id="customer-suggestions-box" class="name-suggestions-dropdown" style="display:none;"></div>
                            @error('first_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!--
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" placeholder="Type here" class="form-control @error('last_name') is-invalid @enderror" value="{{ $customer->last_name }}" autocomplete="off">
                            @error('last_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Email</label>
                            <input  type="text" name="email" placeholder="Type here" class="form-control @error('email') is-invalid @enderror"  value="{{ $customer->email }}">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        -->

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Phone Number</label>
                            <input  type="text" name="phone_number" placeholder="Type here" class="form-control @error('phone_number') is-invalid @enderror" id="product_name" value="{{ $customer->phone_number }}">
                            @error('phone_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label">Area</label>
                            <input  type="text" name="address" placeholder="Type here" class="form-control @error('address') is-invalid @enderror"  value="{{ $customer->address }}">
                            @error('address')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cargo_service" class="form-label">Cargo Service Name</label>
                            <input  type="text" name="cargo_service" placeholder="Type here" class="form-control @error('cargo_service') is-invalid @enderror"  value="{{ $customer->cargo_service ?? '' }}">
                            @error('cargo_service')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                      
                        
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Opening Balance</label>
                            <input  type="text" name="opening_balance" placeholder="Type here" class="form-control @error('oepning_balance') is-invalid @enderror"  value="{{$customer->opening_balance}}">
                            @error('opening_balance')
                            <div class="alert alert-danger">{{ $message }}</div>
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

        const currentCustomerId = "{{ $customer->id }}";
        let debounceTimer = null;
        function fetchCustomerSuggestions() {
            clearTimeout(debounceTimer);
            const firstName = $('input[name="first_name"]').val().trim();
            const lastName = $('input[name="last_name"]').val().trim();
            const query = (firstName + ' ' + lastName).trim();

            if (query.length < 2) {
                $('#customer-suggestions-box').hide().empty();
                return;
            }

            debounceTimer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('customer.name-suggestions') }}",
                    type: "GET",
                    data: { q: query, current_id: currentCustomerId },
                    success: function(data) {
                        const container = $('#customer-suggestions-box');
                        container.empty();

                        if (data && data.length > 0) {
                            let html = `<div class="name-suggestions-header">
                                ⚠️ ${data.length} other similar customer ledger(s) found!
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
                                        <a href="${item.show_url}" target="_blank" class="btn btn-sm btn-outline-info" style="font-size: 11px; padding: 3px 8px;">View Ledger ↗</a>
                                    </div>
                                </div>`;
                            });

                            container.html(html).show();
                        } else {
                            container.hide();
                        }
                    },
                    error: function() {
                        $('#customer-suggestions-box').hide().empty();
                    }
                });
            }, 250);
        }

        $(document).on('input', 'input[name="first_name"], input[name="last_name"]', function() {
            fetchCustomerSuggestions();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.position-relative').length) {
                $('#customer-suggestions-box').hide();
            }
        });
    </script>
@endsection




