<?php

namespace App\Models;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model {
    use HasFactory;
    protected $primaryKey = 'comment_id';
    protected $fillable = [
        'message', 'blog_id', 'user_id'
    ];

    public $appends = [  'human_readable_created_at', 'human_readable_updated_at'];
    public function user() {
        return $this->belongsTo( User::class );
    }

    public function getHumanReadableCreatedAtAttribute() {
        return $this->created_at->diffForHumans();
    }
    public function getHumanReadableUpdatedAtAttribute() {
        return $this->updated_at->diffForHumans();
    }
}
