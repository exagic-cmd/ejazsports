@extends('layouts.app')

@section('content')
<main id="content" role="main">
    <!-- breadcrumb -->
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">My Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- End breadcrumb -->

    <div class="container mb-8">
        <div class="mb-4">
            <h1 class="font-size-25">My Profile</h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('account.update') }}" method="POST">
                            @csrf
                            <!-- Personal Info -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="first_name" value="{{ session('customer_first_name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="last_name" value="{{ session('customer_last_name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="{{ session('customer_email') }}" readonly disabled>
                                    <small class="text-muted">Email cannot be changed.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="phone" value="{{ session('customer_phone') }}" placeholder="Phone Number">
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="changePasswordCheck">
                                    <label class="custom-control-label" for="changePasswordCheck">Change Password</label>
                                </div>
                            </div>

                            <div id="passwordFields" style="display: none;">
                                <h3 class="h5 mt-4 mb-3">Password Change</h3>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control" name="current_password">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="new_password">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" name="new_password_confirmation">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary-dark-w px-5">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                {{-- Optional: Addresses Section if user wanted it in Profile, but they asked for "Simple Profile Screen". 
                     I will leave it out for now to keep it simple as requested. --}}
            </div>
        </div>
    </div>
</main>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordCheck = document.getElementById('changePasswordCheck');
        const passwordFields = document.getElementById('passwordFields');

        if (passwordCheck && passwordFields) {
            passwordCheck.addEventListener('change', function() {
                if (this.checked) {
                    passwordFields.style.display = 'block';
                } else {
                    passwordFields.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection