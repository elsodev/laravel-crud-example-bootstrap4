
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">

                    <div class="card-header">
                        {{ $post->title }}

                        @if ($post->user_id == auth()->user()->id)
                        <div class="float-right">
                            <form class="form-inline" action="{{ route('posts.destroy', [$post->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                 <a href="{{ route('posts.edit', [$post->id]) }}" class="btn btn-outline-secondary btn-sm">Edit</a>&nbsp;

                                <input type="submit" class="btn btn-danger btn-sm" value="Delete"/>
                            </form>
                        </div>
                        @endif

                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Written by {{ $post->user->name }}, {{ $post->created_at->diffForHumans() }}
                        </p>

                        <p>
                            {{ $post->content  }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection