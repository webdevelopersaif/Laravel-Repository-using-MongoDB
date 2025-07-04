@extends('layouts.app')
@section('title', 'Posts')
@section('content')
    <h1>Posts</h1>
    <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Create Post</a>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
    <div class="input-group">
        <input type="text" name="title" id="search-title" class="form-control" placeholder="Search by title" value="{{ request('title') }}">
        <button type="button" id="search_btn" class="btn btn-primary">Search</button>
    </div>
    <table class="table" id="posts-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Image</th>
                <th>Content</th>
                <th>Tags</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="posts-table-body">
            @foreach ($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->title }}</td>
                    <td>
                        @if ($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="max-width: 100px;">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>{{ Str::limit($post->content, 250, '...') }}</td>
                    <td>{{ $post->tags->pluck('name')->implode(', ') }}</td>
                    <td>
                        <a href="{{ route('posts.show', $post->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search_btn').on('click', function(e) {
            e.preventDefault();
            let title = $('#search-title').val();
            $.ajax({
                url: '{{ route("posts.index") }}',
                method: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    title: title
                },
                success: function(response) {
                    let tbody = $('#posts-table-body');
                    tbody.empty();
                    if (response.posts.length === 0) {
                        $('#posts-table').html('<p>No posts found.</p>');
                        return;
                    }
                    let html = '';
                    response.posts.forEach(function(post) {
                        html += `
                            <tr>
                                <td>${post.id}</td>
                                <td>${post.title}</td>
                                <td>
                                    ${post.image ? `<img src="${post.image}" alt="${post.title}" style="max-width: 100px;">` : 'No Image'}
                                </td>
                                <td>${post.content}</td>
                                <td>${post.tags}</td>
                                <td>
                                    <a href="${post.routes.show}" class="btn btn-info btn-sm">View</a>
                                    <a href="${post.routes.edit}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="${post.routes.destroy}" method="POST" style="display:inline;">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>`;
                    });
                    tbody.html(html);
                },
                error: function(xhr) {
                    $('#posts-table').html('<p>Error fetching posts: ' + xhr.responseJSON?.message || 'Unknown error' + '</p>');
                }
            });
        });

        // Trigger search on input change (optional, for real-time search)
        $('#search-title').on('input', function() {
            if ($(this).val().length >= 3 || $(this).val().length === 0) {
                $('#search_btn').click();
            }
        });
    });
</script>
@endsection
