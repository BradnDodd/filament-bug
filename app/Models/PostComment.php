<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PostComment extends Model
{
    protected $table = 'comments_posts';

    protected $fillable = [
        'pinned',
        'post_id',
        'comment_id',
    ];

    public function post(): HasOne
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }

    public function comment(): HasOne
    {
        return $this->hasOne(Comment::class, 'id', 'comment_id');
    }
}
