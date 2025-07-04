<?php
namespace App\Repositories;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class EloquentPostRepository implements PostRepositoryInterface
{
    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): Post
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Post
    {
        try {
            if (!empty($data['image'])) {
                $data['image'] = $this->uploadImage($data['image']);
            } else {
                $data['image'] = null;
            }
            $post = $this->model->create($data);
            if (!empty($data['tags'])) {
                $this->syncTags($post->_id, $data['tags']);
            }
            return $post;
        } catch (Exception $e) {
            throw new Exception('Failed to create post: ' . $e->getMessage());
        }
    }

    public function update($id, array $data): Post
    {
        try {
            $post = $this->model->findOrFail($id);
            if (!empty($data['image'])) {
                if ($post->image) {
                    Storage::disk('public')->delete($post->image);
                }
                $data['image'] = $this->uploadImage($data['image']);
            }
            $post->update($data);
            if (isset($data['tags'])) {
                $this->syncTags($id, $data['tags']);
            }
            return $post;
        } catch (Exception $e) {
            throw new Exception("Failed to update post: {$e->getMessage()}");
        }
    }

    public function delete($id): bool
    {
        try {
            $post = $this->model->findOrFail($id);
            // Delete image if exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $post->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to delete post: {$e->getMessage()}");
        }
    }

    public function searchByTitle(string $title): Collection
    {
        return $this->model->where('title', 'like', "%{$title}%")->get();
    }

    protected function uploadImage($image): string
    {
        $path = $image->store('posts', 'public');
        return $path;
    }

    public function syncTags($id, array $tagNames): void
    {
        try {
            $post = $this->model->findOrFail($id);
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                $tagName = trim($tagName);
                if ($tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->_id;
                }
            }
            $post->tags()->sync($tagIds);
        } catch (Exception $e) {
            throw new Exception("Failed to sync tags: {$e->getMessage()}");
        }
    }

    public function removeImage($id): void
    {
        try {
            $post = $this->model->findOrFail($id);
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
                $post->update(['image' => null]);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to remove image: {$e->getMessage()}");
        }
    }
}