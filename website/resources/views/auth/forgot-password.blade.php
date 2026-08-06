@extends('layouts.app')

@section('content')
<main id="content" role="main">
    <div class="container">
        <div class="row justify-content-center my-8">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">Forgot Password</h3>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your registered email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary-dark-w btn-block">Send Password Reset Link</button>
                            <div class="text-center mt-3">
                                <a href="{{ route('customer.login') }}">Back to Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
