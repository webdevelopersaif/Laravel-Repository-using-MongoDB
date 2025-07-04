<?php
namespace App\Http\Controllers;

use App\Repositories\PostRepositoryInterface;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index(Request $request): View|JsonResponse
    {   
        if ($request->ajax()) {
            //dd($request->all());
            $title = $request->title ?? '';
            //dd($title);
            $posts = $title ? $this->postRepository->searchByTitle($title) : $this->postRepository->all();
            return response()->json([
                'posts' => $posts->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'image' => $post->image ? asset('storage/' . $post->image) : null,
                        'content' => Str::limit($post->content, 250, '...'),
                        'tags' => $post->tags->pluck('name')->implode(', '), // Include tags
                        'routes' => [
                            'show' => route('posts.show', $post->id),
                            'edit' => route('posts.edit', $post->id),
                            'destroy' => route('posts.destroy', $post->id)
                        ],
                    ];
                })->toArray(),
            ]);
        }

        $posts = $this->postRepository->all();
        // dd($posts);
        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(PostRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['tags'] = array_filter(array_map('trim', explode(',', $request->input('tags', ''))));
            $this->postRepository->create($data);
            return redirect()->route('posts.index')->with('success', 'Post created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    public function show($id): View
    {
        $post = $this->postRepository->find($id);
        return view('posts.show', compact('post'));
        
    }

    public function edit($id): View
    {
        $post = $this->postRepository->find($id);
        return view('posts.edit', compact('post'));
    }

    public function update(PostRequest $request, $id): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['tags'] = array_filter(array_map('trim', explode(',', $request->input('tags', ''))));
            $this->postRepository->update($id, $data);
            return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $this->postRepository->delete($id);
            return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('posts.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function search(Request $request): View
    {
        $posts = $this->postRepository->searchByTitle($request->query('title', ''));
        return view('posts.index', compact('posts'));
    }


    public function removeImage($id): RedirectResponse
    {
        try {
            $this->postRepository->removeImage($id);
            return redirect()->back()->with('success', 'Image removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}