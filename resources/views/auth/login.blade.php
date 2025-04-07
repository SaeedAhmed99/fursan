@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $settings = Utility::settings();
    $company_logo = $settings['company_logo'] ?? '';
@endphp

@push('custom-scripts')
    @if ($settings['recaptcha_module'] == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

@section('page-title')
    {{ __('Login') }}
@endsection
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

@if ($settings['cust_darklayout'] == 'on')
    <style>
        .g-recaptcha {
            filter: invert(1) hue-rotate(180deg) !important;
        }

    </style>
@endif
<style>
    .password-container {
        position: relative;
        width: 100%; /* السماح له بالتكيف مع حجم الشاشة */
        display: block;
    }

    .password-container input {
    padding-right: 35px; /* مساحة للأيقونة */
    width: 100%; /* جعل العرض مرنًا */

}

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #666;
        }
        
        .toggle-password:hover {
            color: #000;
        }
</style>
@php
    $languages = App\Models\Utility::languages();
@endphp

@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> {{ $languages[$lang] }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach ($languages as $code => $language)
                    <a href="{{ route('login', $code) }}" class="dropdown-item @if ($lang == $code) text-primary @endif">
                        <span>{{ Str::upper($language) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')
    <div class="card-body">
        <div>
            <h2 class="mb-3 f-w-600">{{ __('Login') }}</h2>
        </div>
        {{ Form::open(['route' => 'login', 'method' => 'post', 'id' => 'loginForm', 'class' => 'login-form', 'class'=>'needs-validation', 'novalidate']) }}
        @if (session('status'))
            <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                {{ session('status') }}
            </div>
        @endif
        <div class="custom-login-form">
            <div class="form-group mb-3">
                <label class="form-label">{{ __('Email') }}</label>
                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Email'), 'required' => 'required']) }}
                @error('email')
                    <span class="error invalid-email text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label" style="display: block;">{{ __('Password') }}</label>
                <div class="password-container">
                     {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Your Password'), 'id' => 'input-password', 'required' => 'required']) }}
                     <i class="fa-solid fa-eye toggle-password" id="toggle-password" onclick="togglePassword()"></i>
                    </div>
                @error('password')
                    <span class="error invalid-password text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between">

                    @if (Route::has('password.request'))
                        <span><a href="{{ route('password.request', $lang) }}"
                                tabindex="0">{{ __('Forgot your password?') }}</a></span>
                    @endif
                </div>
            </div>

            @if ($settings['recaptcha_module'] == 'on')
                @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        {!! NoCaptcha::display() !!}
                        @error('g-recaptcha-response')
                            <span class="small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @else
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" class="form-control">
                        @error('g-recaptcha-response')
                            <span class="error small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @endif
            @endif

            <div class="d-grid">
                {{ Form::submit(__('Login'), ['class' => 'btn btn-primary mt-2', 'id' => 'saveBtn']) }}
            </div>
            
            @if ($settings['enable_signup'] == 'on')
                <p class="my-4 text-center">{{ __("Don't have an account?") }}
                    <a href="{{ route('register', $lang) }}" class="text-primary">{{ __('Register') }}</a>
                </p>
            @endif

        </div>
        {{ Form::close() }}
    </div>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("input-password");
    
            // التأكد من أن العنصر موجود قبل محاولة استخدامه
            if (passwordInput) {
                passwordInput.type = (passwordInput.type === "password") ? "text" : "password";
            } else {
                console.error("العنصر #input-password غير موجود في DOM");
            }
        }
    </script>
@endsection

<script src="{{ asset('js/jquery.min.js') }}"></script>
@if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'on')
    @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
        {!! NoCaptcha::renderJs() !!}
    @else
        <script src="https://www.google.com/recaptcha/api.js?render={{ $settings['google_recaptcha_key'] }}"></script>
        <script>
            $(document).ready(function() {
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ $settings['google_recaptcha_key'] }}', {
                        action: 'submit'
                    }).then(function(token) {
                        $('#g-recaptcha-response').val(token);
                    });
                });
            });
        </script>
    @endif
@endif
