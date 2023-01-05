@extends('index')
@section('content')
<div class="container">
    @if(session()->has('errorMessage'))
    <div class="alert alert-warning mx-auto mt-4 " role="alert" style="max-width: 400px">
        {{ session('errorMessage') }}
    </div>
    @endif
    <div class="card mx-auto mt-4" style="max-width: 400px">
        <form class="card-body" method="POST" action="/signin">
            @csrf
            <h5>Login</h5>
            <div class="mb-3">
                <label for="">Username</label>
                <input type="text" name='username' class="form-control" required>
                @error('username')
                    <label class="text-danger">{{ $message }}</label>
                @enderror
            </div>
            <div class="mb-3">
                <label for="">Password</label>
                <input type="password" name='password' class="form-control" required>
                @error('password')
                    <label class="text-danger">{{ $message }}</label>
                @enderror
            </div>
            <div class="">
                <button type="submit" class="btn btn-primary d-block me-0 ms-auto">Login</button>
            </div>
        </form>
    </div>
</div>
@endsection