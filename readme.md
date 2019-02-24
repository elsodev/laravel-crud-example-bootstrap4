## CRUD Tutorial with Authentication
### Creating a Post CRUD
Author: Elson Tan



1. Scaffold Authentication (login and register) with Bootstrap 4
    
    `php artisan make:auth`

2. Generate a migration for posts schema

    `php artisan make:migration create_posts_table`
    
     `2019_02_24_064949_create_posts_table`

3. Create posts table schema

    ```php
    Schema::create('posts', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->string('title');
        $table->text('content');
        
        $table->foreign('user_id')->references('id')
            ->on('users')->onDelete('CASCADE');
        
        $table->timestamps();
    });
    ```

4. Create a seeder
`php artisan make:seeder UserSeeder`

5. *UserSeeder.php* code
    ```php
    <?php

    use Illuminate\Database\Seeder;

    class UserSeeder extends Seeder
    {
        /**
        * Run the database seeds.
        *
        * @return void
        */
        public function run()
        {
            \App\User::create([
                'name' => 'Test User',
                'email' => 'test@gmail.com',
                'password' => bcrypt('test1234')
            ]);
        }
    }
    ```

6. *DatabaseSeeder.php* should call our newly created UserSeeder
    ```php
        public function run(){
            $this->call(UserSeeder::class);
        }
    ```


7. Run migrations
    
    `php artisan migrate`


8. Run DB seeders

    `php artisan db:seed`

9. Check you Database, there should be 4 new tables:
    1. migrations
    2. password_resets
    3. posts
    4. users

10. Now, you can try login with your seeded user account
	
    Email: test@gmail.com
	
    Password: test1234

11. You will then see dashboard saying "You're logged in!"

12. Lets create a Model for our Posts table
    
    `php artisan make:model Post`

13. Now go to your model in *app/Post.php*, you post model should have fillables and linked relation to a user.
    ```php
    <?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Post extends Model
    {
        protected $fillable = [
            'title',
            'content',
            'user_id'
        ];

        /**
        * A Post belongs to a User
        * 
        * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
        */
        public function user()
        {
            return $this->belongsTo(User::class); // auto get user_id as foreign key
        }
    }
    ```
    Note: You can get your post fillables from your migrations defined fields (id, timestamps should not be fillable)

14. Then, we need to link our User model to Post model too, go to *app/User.php*
    ```php

        /**
        * A User has many Posts
        * 
        * @return \Illuminate\Database\Eloquent\Relations\HasMany
        */
        public function posts()
        {
            return $this->hasMany(Post::class);
        }
    ```

15. We have now setup Migrations, Seeders, Authentication, Models, we will move forward setting up views, normally a CRUD application has four different views per entity:
    1. index    - shows a list of existing posts
    2. form     - holds post form
    3. create   - create page of post
    4. edit     - edit page of post
    5. single   - standalone view of a single post

    Then, we shall create under *resources/views/posts/*
    1. index.blade.php
    2. form.blade.php
    3. create.blade.php
    4. edit.blade.php
    5. single.blade.php

    All of these we can extend the scaffolded layouts/app.blade.php

    */resources/views/posts/index.blade.php*
    ```html
    @extends('layouts.app')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">=
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

    ```

    */resources/views/posts/single.blade.php*
    ```html 
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
    ```

16. Creating the form for posts, place this in */resources/views/posts/form.blade.php*
    ```html
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" class="form-control" name="title" value="{{ old('title', (isset($post)) ? $post->title: null) }}"/>
    </div>

    <div class="form-group">
        <label for="content">Content</label>
            <textarea class="form-control" name="content" id="content">{{ old('content', (isset($post)) ? $post->content: null) }}</textarea>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-success" value="Submit"/>&nbsp;&nbsp;or&nbsp;&nbsp;<a href="{{ route('posts.index') }}">Back</a>
    </div>
    ```

17. We can now add the form as an include into create/edit template

    */resources/views/posts/create.blade.php*
    ```html
    @extends('layouts.app')

    @section('content')
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">

                        <div class="card-header">
                            Create a Post
                        </div>
                        <div class="card-body">
                            <form action="{{ route('posts.store') }}" method="POST">
                                @csrf
                                @include('posts.form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    ```

    */resources/views/posts/edit.blade.php*
    ```html
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

    ```
    *Note: In edit, we need send the form as a PUT but HTML form does not support PUT method, we need define a hidden form field with value PUT so Laravel knows its a PUT method.*

18. Now we have completed four views, lets move back to Controller and Route to link them up.
    
    Create a resource controller

    `php artisan make:controller PostController --resource`

19. Link all resource routes in *routes/web.php*

    ```php 
    
    Route::group(['middleware' => ['web', 'auth']], function () {
        Route::resource('posts', 'PostController');
    });
    ```
    *Note: We surround post resource route with a Route group middleware web so that only logged in users can access this page.*



20. Now, back to our app/Http/Controllers/PostController.php, we need to write the logic for all resource methods using our Models.

21. For each methods inside PostController.php

    #### *index()*
    ```php

    public function index()
    {
        return view('posts.index');
    }
    ```

    #### *create()*
    ```php
    public function create()
    {
        return view('posts.create');
    }
    ```

    #### *store()*

    a. We need to create a Request when storing data, use:
    `php artisan make:request StorePostRequest`

    b. Then, in *app/Http/Requests/StorePostRequest.php*
    ```php
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'     => 'required',
            'content'   => 'required'
        ];
    }
    ```

    c. Then back in *PostController.php*, store() method:
    ```php

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
    ```
    NOTE: make sure you import StorePostRequest using `use App\Http\Requests\StorePostRequest;`

    #### *show()*
    ```php

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
    ```

    #### *edit()*
    ```php
      /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('posts.edit');
    }
    ```

    #### *update()*

    a. `php artisan make:request UpdatePostRequest`

    b. *app/Http/Requests/UpdatePostRequest.php*
    ```php
    <?php

    namespace App\Http\Requests;

    use App\Post;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Facades\Input;

    class UpdatePostRequest extends FormRequest
    {
        /**
        * Determine if the user is authorized to make this request.
        *
        * @return bool
        */
        public function authorize()
        {
            // make sure only owner can update their own posts
            return Post::where('id', $this->route('post'))
                ->where('user_id', auth()->user()->id)
                ->exists();
        }

        /**
        * Get the validation rules that apply to the request.
        *
        * @return array
        */
        public function rules()
        {
            return [
                'title'     => 'required',
                'content'   => 'required'
            ];
        }
    }
    ```
    *Note: In authorize, we make sure only Post owner can update their own Posts*

    c. Then back in *PostController.php*, update() method:
    ```php
    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePostRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, $id)
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
    ```
    
    #### *destroy()*

    a. `php artisan make:request DestroyPostRequest`

    b. *app/Http/Requests/DestroyPostRequest*
    ```php
    <?php

    namespace App\Http\Requests;

    use App\Post;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Facades\Input;

    class DestroyPostRequest extends FormRequest
    {
        /**
        * Determine if the user is authorized to make this request.
        *
        * @return bool
        */
        public function authorize()
        {
            // make sure only owner can update their own posts
            return Post::where('id', $this->route('post'))
                ->where('user_id', auth()->user()->id)
                ->exists();
        }

        /**
        * Get the validation rules that apply to the request.
        *
        * @return array
        */
        public function rules()
        {
            return [];
        }
    }
    ```

    c. Then back in *PostController.php*
    ```php

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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
    ```
    *Note: Upon successful deletion, we redirect user to posts.index page with a Flashed data.*

22. Try it out.
    




       
    

    
    
