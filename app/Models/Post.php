<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';
    protected $fillable = ['title', 'content', 'image'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, null, 'post_ids', 'tag_ids', '_id', '_id')
                    ->withTimestamps()
                    ->withPivot(['_id']);
    }
}
