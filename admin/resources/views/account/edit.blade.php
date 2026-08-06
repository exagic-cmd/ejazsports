@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Account</h2>
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
                    <form action="{{route('accounts.update',$account->id)}}" method="post" id="form" autocomplete="false">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Name</label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" value="{{ $account->name }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Email</label>
                            <input  type="text" autocomplete="false" name="email" placeholder="Type here" class="form-control @error('email') is-invalid @enderror" value="{{ $account->email }}">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Phone Number</label>
                            <input  type="text" name="phone_number" placeholder="Type here" class="form-control @error('phone_number') is-invalid @enderror" value="{{ $account->phone_number }}">
                            @error('phone_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Password</label>
                            <small>(left empty if you don't want to change)</small>
                            <input  type="password" name="password" placeholder="Type here" class="form-control @error('password') is-invalid @enderror" >
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label">Store (if any)</label>
                            <select class="form-control select2"
                                    name="store_id">
                                <option value="">None</option>

                                @foreach($stores as $store)
                                    @if($account->store_id == $store->id)
                                    <option selected value="{{ $store->id }}">{{ $store->name }}</option>
                                    @else
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endif
                                @endforeach
                            </select>
                            @error('store_id')
                            <span class="text-danger text-left">{{ $errors->first('store_id') }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control select2"
                                    name="role[]" multiple required>

                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ in_array($role->name, $accountRole)
                                            ? 'selected'
                                            : '' }}>{{ $role->name }}</option>
                                @endforeach

                            </select>
                            @error('role')
                            <span class="text-danger text-left">{{ $errors->first('role') }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label">Status</label>
                            <select class="form-control select2"
                                    name="status" required>

                                <option value="1"  {{$account->status ? 'selected' : ''}}>Active</option>
                                <option value="0" {{$account->status ? '' : 'selected'}}>InActive</option>

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

@section('js')
    <script>
        $('.select2').select2();
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
    </script>

@stop

