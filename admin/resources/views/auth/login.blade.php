
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>EJAZ SPORTS</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('imgs/theme/favicon.svg')}}" />
    <!-- Template CSS -->
    <link href="{{asset('css/main.css?v=1.0')}}" rel="stylesheet" type="text/css" />
</head>

<body>
<main>
    <header class="main-header style-2 navbar">
        <div class="col-brand">

                <img style="width: 100px;" src="{{asset('imgs/theme/logo.jpeg')}}" class="logo" alt="Sports logo" />

        </div>

    </header>
    <section class="content-main mt-80 mb-80">
        <div class="card mx-auto card-login">
            <div class="card-body">
                <h4 class="card-title mb-4">Sign in ..</h4>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    @if($errors->has('email'))
                        <div style="text-align: center;">
                            <span id="login-error" style="color: red">{{ $errors->first('email') }}</span>
                        </div>
                    @endif

                    @if($errors->has('password'))
                        <div style="text-align: center;">
                            <span id="login-error" style="color: red">{{ $errors->first('password') }}</span>
                        </div>
                    @endif

                    <div class="mb-3">
                        <input class="form-control" id="email" name="email" required placeholder="Username or email" type="text" />
                    </div>
                    <!-- form-group// -->
                    <div class="mb-3">
                        <input class="form-control" id="password" name="password" required placeholder="Password" type="password" />
                    </div>


                    <!-- form-group// -->
                    <div class="mb-3">
                        @if (Route::has('password.request'))
                            <a style="margin-left: 45%;" class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" checked="" />
                            <span class="form-check-label">Remember</span>
                        </label>
                    </div>
                    <!-- form-group form-check .// -->
                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </div>
                    <!-- form-group// -->
                </form>

            </div>
        </div>
    </section>
    <footer class="main-footer text-center" style="padding: 0px 0%;">
        <p class="font-xs">
            <script>
                document.write(new Date().getFullYear());
            </script>
            ©, EJAZSPORTS.COM.
        </p>
        <p class="font-xs mb-30">All rights reserved</p>
    </footer>
</main>
<script src="{{asset('js/vendors/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('js/vendors/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/vendors/jquery.fullscreen.min.js')}}"></script>
<!-- Main Script -->
<script src="{{asset('js/main.js?v=1.0')}}" type="text/javascript"></script>
</body>
</html>
