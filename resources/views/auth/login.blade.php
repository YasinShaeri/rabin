@extends('layouts.auth')
@section('content')

    <div class="fixed-background"></div>
    <main>
        <div class="container">
            <div class="row h-100">
                <div class="col-12 col-md-10 mx-auto my-auto">
                    <div class="card auth-card">
                        <div class="position-relative image-side d-flex flex-column justify-content-end">

                            <p class=" text-white h2">سامانه رابین تیکتینگ</p>

                            <p class="white mb-0">
                                شرکت تجارت الکترونیک پرتیکان.
                                {{-- <br>If you are not a member, please
                                <a href="#" class="white">register</a>. --}}
                            </p>
                        </div>
                        <div class="form-side">
                            <div class="text-center">
                                <img src="{{ asset('img/rabin-png.png') }}" alt="ogo" width="150px">
                            </div>

                            <h6 class="mb-4">ورود</h6>
                            <form method="POST" action="{{ route('login.post') }}">
                                @csrf
                                <label class="form-group has-float-label mb-4">
                                    <input class="form-control" name="username" required />
                                    <span>نام کاربری</span>
                                </label>

                                <label class="form-group has-float-label mb-4">
                                    <input class="form-control" type="password" name="password" required />
                                    <span>رمز عبور</span>
                                </label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-primary btn-lg btn-shadow" type="submit">LOGIN</button>
                                </div>
                            </form>
                            @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
