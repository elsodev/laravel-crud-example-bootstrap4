<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyPostRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('user')->paginate(20);
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePostRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create([
            'title'     => $request->input('title'),
            'content'   => $request->input('content'),
            'user_id'   => auth()->user()->id
        ]);

        if ($post) {
            return redirect()->route('posts.show', [$post->id])
                ->with('success', true);
        } else {
            return redirect()->back()->withErrors(['Unable to create post']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        if ($post) {
            return view('posts.single', compact('post'));
        } else {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);

        if ($post) {
            return view('posts.edit', compact('post'));
        } else {
            return abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePostRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdatePostRequest $request)
    {
        $post =  Post::find($id);

        if ($post) {
            $post->update([
                'title'     => $request->input('title'),
                'content'   => $request->input('content'),
            ]);

            session()->flash('success', true);
            return redirect()->back();
        } else {
            return redirect()->back()->withErrors(['Unable to update post']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param DestroyPostRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DestroyPostRequest $request)
    {
        $results = Post::destroy($id);
        if ($results) {
            session()->flash('delete_success', true);
            return redirect()->route('posts.index');
        } else {
            return redirect()->back()->withErrors(['Unable to delete post']);
        }
    }
}
