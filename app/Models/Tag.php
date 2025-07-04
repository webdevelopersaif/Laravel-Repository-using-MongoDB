<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'tags';
    protected $fillable = ['name'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, null, 'tag_ids', 'post_ids', '_id', '_id')
                    ->withTimestamps()
                    ->withPivot(['_id']);
    }
}