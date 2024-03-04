<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comment;
use App\Models\BlogLike;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model {
    use HasFactory;
    protected $primaryKey = 'blog_id';
    protected $fillable = [
        'title',
        'short_description',
        'long_description',
        'image',
        'user_id',
        'category_id'
    ];

    public $appends = [ 'image_url', 'human_readable_created_at',  'human_readable_updated_at'];

    //     public function user(): This declares a public method named user, which will be used to access the related User model instance.
    // return $this->belongsTo( User::class );
    // : This is an Eloquent relationship method. It establishes a 'belongs to' relationship between the current model
    // ( the model where this code is placed ) and the User model.
    // $this refers to the instance of the current model.
    // belongsTo( User::class ) indicates that each instance of the current model belongs to a single instance of the User model.

    //User relationship with the blog table.

    public function user() {
        return $this->belongsTo( User::class);
    }

    //Category relationship with the blog table

    public function category() {
        return $this->belongsTo( Category::class, 'category_id', 'category_id' );

        //         In the belongsTo relationship:
        // The second parameter ( 'category_id' ) is the name of the foreign key column on the blogs table.
        // The third parameter ( 'category_id' ) is the name of the primary key column on the categories table.
    }

    // public function comments() {
    //     return $this->belongsTo(Comment::class,'comment_id');
    // }
    public function comments() {
        return $this->hasMany(Comment::class, 'blog_id');
        //the foreign key column on the comments table must be given
    }
    
    public function likes() {
        return $this->hasMany(BlogLike::class, 'blog_id');
        //the foreign key column on the comments table must be given
    }

    //creating attributes to change the image name to path of the image url

    public function getImageUrlAttribute() {
        return asset( '/uploads/blog_images/'.$this->image );
    }
    //     This accessor is used to generate and return the URL for the image associated with the blog. When you access the image_url
    // attribute on a Blog instance, Laravel  will automatically call this accessor method.
    // asset is a Laravel helper function that generates a URL for an asset ( file, image, etc. ) based on the configured
    // asset URL in your application.
    // $this->image refers to the image attribute of the Blog model instance, assuming that each blog has an image
    // attribute storing the filename of the associated image.

    public function getHumanReadableCreatedAtAttribute() {
        return $this->created_at->diffForHumans();
    }
    public function getHumanReadableUpdatedAtAttribute() {
        return $this->updated_at->diffForHumans();
    }

//     This accessor is used to provide a human-readable version of the created_at timestamp. When you access the human_readable_created_at 
//     attribute on a Blog instance, Laravel will call this accessor method.
// $this->created_at refers to the created_at timestamp of the Blog model instance.
// diffForHumans is a method provided by Carbon, the underlying date and time library used by Laravel. 
// It returns a human-readable difference between the current time and the created_at timestamp.
}
