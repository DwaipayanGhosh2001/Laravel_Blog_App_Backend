<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogLike extends Model
{
    use HasFactory;
    protected $primaryKey = 'like_id';
    protected $fillable = [
         'blog_id', 'user_id'
    ];
}
