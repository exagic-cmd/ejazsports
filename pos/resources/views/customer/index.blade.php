@extends('layouts.app')



@section('content')
    <div class="pos-content-container" id="pos-content-container">
        <div>
            <div class="pos-customer-main" style="width: 100%;margin-top: 1%;"><div class="pos-nav-container"><ul class="pos-nav-lists"><li label="menu_count_0" class="pos-nav"><a href="/pos-202-165-225-93/pos/customer/list" aria-current="page" class="nav-link router-link-exact-active router-link-active">
                                Customers
                            </a></li></ul></div> <div class="pos-nav-content" style="height: 642px;"><div><div class="pos-product-container" style="height: 641px;"><div class="pos-customer-list"><div class="customer_search"><i class="fa fa-search"></i> <input type="text" placeholder="Search Customer By Name" id="customer_search_field" class="control_disabled customer_search_field"></div> <ul class="customer_list" id="customer-list" style="height: 581px;">
                                    @foreach($result->data->customers as $customer)
                                        <a href="#!" onclick="customerInfo({{$customer->id}})"><li class="recordc">
                                            <div class="customer_name">
                                                {{$customer->first_name}} {{$customer->last_name}}
                                            </div> <div class="customer_contact"><i class="fa fa-envelope"></i>
                                                {{$customer->email}}
                                            </div> <div class="customer_contact"><i class="fa fa-phone"></i>
                                                {{$customer->phone_number}}
                                            </div></li>
                                        </a>
                                    @endforeach

                                </ul></div> <div class="pos-customer-view"><div class="customer-view-panel"><div class="customer_details" id="customer-detail"><div class="message-alert danger">
                                            Warning: Currently no customer is selected!
                                        </div></div>
                                    <div class="add_customer"><a href="#!" onclick="openCustomerModal()">
                                        <div class="customer-add-icon">
                                            <i class="fa fa-plus"></i>
                                        </div>
                                        <div class="customer-add-text">
                                            Add Customer
                                        </div></a>
                                    </div>
                                    <!----></div></div></div></div></div></div>
            <div class="pos-cart-container" style="height: 686px;">

            </div>
        </div></div>

    <div style="display: none" id="customer-modal"><div id="addCustomer"><div class="pos-modal-overlay"></div> <div class="pos-modal-container"><div class="modal-header"><h4> Add Customer</h4> <i onclick="hideCustomerModal()" class="icon remove-icon"></i></div> <div class="modal-body"><div><div class="pos-discount-form">
                            <form autocomplete="off" action="{{route('customer.create')}}" method="POST">
                                @csrf
                                <div class="page-content">
                                    <div class="form-container">
                                        <input type="hidden" name="store_id" value="{{Auth::user()->store_id}}">
                                        <div class="pos-customer-fields">
                                            <div class="control-group" style="position: relative;">
                                                <label for="first_name" class="required">First Name</label>
                                                <input type="text" name="first_name" id="pos_first_name" class="control" data-vv-id="15" aria-required="true" aria-invalid="false" style="width: 90%;" autocomplete="off">
                                                <div id="pos-customer-suggestions-box" class="name-suggestions-dropdown" style="display:none; width: 90%;"></div>
                                                <!----></div>
                                            <div class="control-group">
                                                <label for="last_name" class="required">Last Name</label>
                                                <input type="text" name="last_name" class="control" data-vv-id="16" aria-required="true" aria-invalid="false" style="width: 90%;" autocomplete="off">
                                                <!----></div> <div class="control-group"><label for="email" class="required">Email</label>
                                                <input type="email" name="email" class="control" data-vv-id="17" aria-required="true" aria-invalid="false" style="width: 90%;">
                                                <!----></div> <div class="control-group"><label for="phone">Phone</label>
                                                <input type="text" name="phone" class="control" style="width: 90%;"> <!---->
                                            </div> <div class="control-group"><label for="gender">Gender</label> <select name="gender" class="control" style="width: 90%;"><option value="1">Male</option> <option value="2">Female</option></select></div>
                                            <div class="control-group"><label for="date_of_birth">Date Of Birth</label>
                                                <input type="date" name="dob" class="control" style="width: 90%;"> <!----></div>

                                            <div class="pos-action text-center"><button type="submit" text="Save" class="btn btn-lg btn-pos-primary"> Save </button></div></div>
</div></div></form></div></div></div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .name-suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1050;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        max-height: 250px;
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
    }
    .name-suggestion-item {
        padding: 8px 12px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
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
        font-size: 13px;
    }
    .suggestion-sub {
        font-size: 11px;
        color: #64748b;
    }
</style>
@endsection

@section('js')
    <script>
        function openCustomerModal() {
            document.getElementById('customer-modal').style.display = 'block';
        }
        function hideCustomerModal() {
            document.getElementById('customer-modal').style.display = 'none';
        }
        
        if (document.getElementById('search1')) document.getElementById('search1').style.display = 'none';
        if (document.getElementById('search2')) document.getElementById('search2').style.display = 'none';

        let debounceTimerPos = null;
        function fetchPosCustomerSuggestions() {
            clearTimeout(debounceTimerPos);
            const firstName = $('#pos_first_name').val() ? $('#pos_first_name').val().trim() : '';
            const lastName = $('input[name="last_name"]').val() ? $('input[name="last_name"]').val().trim() : '';
            const query = (firstName + ' ' + lastName).trim();

            if (query.length < 2) {
                $('#pos-customer-suggestions-box').hide().empty();
                return;
            }

            debounceTimerPos = setTimeout(function() {
                $.ajax({
                    url: "{{ route('customer.suggestions') }}",
                    type: "POST",
                    data: {
                        val: query,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        const container = $('#pos-customer-suggestions-box');
                        container.empty();

                        const data = (response && response.data) ? response.data : response;

                        if (data && Array.isArray(data) && data.length > 0) {
                            let html = `<div class="name-suggestions-header">
                                ⚠️ ${data.length} existing customer(s) found! Click to select:
                            </div>`;

                            data.forEach(function(item) {
                                html += `
                                <div class="name-suggestion-item" onclick="selectPosCustomer(${item.id})">
                                    <div class="suggestion-info">
                                        <div class="suggestion-title">${item.name}</div>
                                        <div class="suggestion-sub">
                                            📞 ${item.phone || 'N/A'} ${item.email ? '| ✉️ ' + item.email : ''}
                                        </div>
                                    </div>
                                    <div class="suggestion-actions">
                                        <span class="btn btn-sm btn-pos-primary" style="font-size: 11px; padding: 2px 6px;">Select</span>
                                    </div>
                                </div>`;
                            });

                            container.html(html).show();
                        } else {
                            container.hide();
                        }
                    },
                    error: function() {
                        $('#pos-customer-suggestions-box').hide().empty();
                    }
                });
            }, 250);
        }

        function selectPosCustomer(id) {
            hideCustomerModal();
            if (typeof customerInfo === 'function') {
                customerInfo(id);
            }
        }

        $(document).on('input', '#pos_first_name, input[name="last_name"]', function() {
            fetchPosCustomerSuggestions();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#addCustomer').length) {
                $('#pos-customer-suggestions-box').hide();
            }
        });
    </script>
@endsection
