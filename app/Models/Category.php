<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';
    // the protected $primaryKey = 'category_id'; line in your Category model is used to explicitly specify the 
    // name of the primary key column for the associated database table. This is particularly useful when the primary key column
    //  doesn't follow the default naming convention, which is id.
    protected $fillable = [
        'name',
        'description',
        'long_description',
        'status'
    ];

}
