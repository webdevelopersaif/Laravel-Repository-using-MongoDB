@extends('layouts.app')
@section('title', 'Post Details')
@section('content')
    <h1>{{ $post->title }}</h1>
    <p>{{ $post->content }}</p>
    @if ($post->image)
        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="max-width: 300px;">
    @else
        <p>No Image</p>
    @endif
    <a href="{{ route('posts.index') }}" class="btn btn-primary">Back to Posts</a>
@endsection