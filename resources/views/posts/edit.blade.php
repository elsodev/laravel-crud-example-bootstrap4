

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">


                    <div class="card-header">
                        Edit a Post
                    </div>
                    <div class="card-body">

                        @if (session()->has('success'))
                            <div class="alert alert-success">
                                Successfully saved
                            </div>
                        @endif


                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('posts.update', [$post->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('posts.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
