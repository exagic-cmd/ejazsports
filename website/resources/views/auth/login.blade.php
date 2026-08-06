@extends('layouts.app')

@section('content')
<main id="content" role="main">
    <div class="container">
        <div class="row justify-content-center my-8">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('customer.login.post') }}">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary-dark-w btn-block">Login</button>
                            
                            <div class="text-center mt-3">
                                <a class="small text-muted" href="{{ route('password.request') }}">Forgot Password?</a>
                            </div>
                            <div class="text-center mt-2">
                                <span class="small text-muted">Don't have an account? <a href="{{ route('customer.register') }}">Register here</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
