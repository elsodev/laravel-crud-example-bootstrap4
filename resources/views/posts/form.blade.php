<div class="form-group">
    <label for="title">Title</label>
    <input type="text" id="title" class="form-control" name="title" value="{{ old('title', (isset($post)) ? $post->title: null) }}"/>
</div>

<div class="form-group">
    <label for="content">Content</label>
    <textarea class="form-control" name="content" id="content">{{ old('content', (isset($post)) ? $post->content: null) }}</textarea>
</div>

<div class="form-group">
    <input type="submit" class="btn btn-success" value="Submit"/>&nbsp;&nbsp;or&nbsp;&nbsp;<a href="{{ url()->previous() }}">Back</a>
</div>