@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">

                    <div class="card-header">
                        Posts
                        <a href="{{ route('posts.create') }}" class="btn btn-sm btn-success float-right">Create Post</a>

                    </div>
                    <div class="card-body">

                        @if (session()->has('delete_success'))
                            <div class="alert alert-success">
                                Successfully deleted post
                            </div>
                        @endif

                        <ul class="list-group list-group-flush">
                            @foreach($posts as $post)

                            <li class="list-group-item">
                                <a href="{{ route('posts.show', [$post->id]) }}">
                                    {{ $post->title }}
                                </a>
                                <p>
                                    {{ str_limit($post->content, 100, '...') }}
                                </p>
                            </li>
                            @endforeach

                        </ul>
                        <div class="float-right">
                            {{ $posts->links() }}
                        </div>


                        @if ($posts->count() <= 0)
                            No posts yet.
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
