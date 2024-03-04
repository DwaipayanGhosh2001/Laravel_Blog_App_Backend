<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogLike;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller {
    public function create( Request $request ) {
        try {
            $validator = Validator::make( $request->all(), [
                'title' => 'required|max:250',
                'short_description' => 'required',
                'long_description' => 'required',
                'category_id' => 'required',
                'image' => 'required|image|mimes:jpg,png'
                //The user id is not included here at it will come from the user token generated.
            ] );

            if ( $validator->fails() ) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Validation failed',
                    'error' => $validator->errors()
                ];
                return response()->json( $data, 422 );
            }

            //  $image_name = time().'.'.$request->image->extension();
            // time() generates a timestamp representing the current time.
            // $request->image refers to the file uploaded through the HTTP request, assuming it's part of a form with enctype="multipart/form-data".
// $request->image->extension() retrieves the file extension of the uploaded image (e.g., 'jpg', 'png', etc.).
// The combination of time() and the file extension creates a unique name for the image by appending the timestamp to the original file extension.
// $request->image->move(public_path('/uploads/blog_images'), $image_name);:
// $request->image->move() is a method that moves the uploaded file to a specified location.
// public_path('/uploads/blog_images') specifies the destination directory where the file will be moved. The public_path() function generates the absolute path to the public directory, and '/uploads/blog_images' is a relative path from the public directory.
// $image_name is the unique name generated in the first line, and it serves as the new filename for the uploaded image.

    
    $image_name = time().'.'.$request->image->extension();
    $request->image->move( public_path( '/uploads/blog_images' ), $image_name );

    $blog = Blog::create([
        'title' =>$request->title,
        'short_description' =>$request->short_description,
        'long_description' =>$request->long_description,
        //The user data is been taken from the user login token
        'user_id' =>$request->user()->id,
        'category_id' =>$request->category_id,
        //the image name will be stored in the database
        'image'=>$image_name
    ]);
   
if ($blog) {
    //loading the data from the user table and category table in the blog
    //this statement will give all the data from the user and category table of the selected user and category.
    // $blog->load('user','category');

$blog->load('user:id,name,email', 'category:category_id,name');
//This statement will give selected parameters only from the user and category table.
    $data = [
        'status' => 'passed',
        'message' => 'Blog successfully created',
        'error' => $blog
    ];
    return response()->json( $data, 200 );
} else {
    $data = [
        'status' => 'failed',
        'message' => 'Something went wrong',
    ];
    return response()->json( $data, 400 );
}

    
 } catch (\Exception $e) {
    $data = [
        'status' => 'failed',
        'message' => 'Internal Server Error',
        'error' => $e->getMessage()
    ];
    return response()->json( $data, 500 );
 }

    }


    
    public function blogDetails($id, Request $request){
        try{
            // $blog=Blog::with(['user','category'])->where('blog_id',$id)->first();
            //OR
             $blog=Blog::withCount(['comments','likes'])->with(['user:id,name,email','category:category_id,name'])->find($id);
            
            if($blog){
                //As this route is not protected by auth sanctum therefore Checking if the user is logged in or not
                $user=auth('sanctum')->user();
                if($user){
                    //If user is present, then checking if the blog is liked by the logged user.
                    $blog_like=BlogLike::where('blog_id',$blog->blog_id)->where('user_id',$user->id)->first();
                    if ($blog_like) {
                        //If the blog is liked by the logged user then add a new field to the blog details data
                        $blog->liked_by_user =true;
                    } else {
                        $blog->liked_by_user =false;
                    }
                }else{
                    $blog->liked_by_user =false;
                }
                
                $data = [
                    'status' => 'sucess',
                    'message' => 'Blog found successfully',
                    'data' => $blog
                ];
                return response()->json( $data, 200);
            }
            else{
                $data = [
                    'status' => 'failed',
                    'message' => 'Blog not found',
                ];
                return response()->json( $data,404);
            }
        }
       catch(\Exception $e){
        $data = [
            'status' => 'failed',
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ];
        return response()->json( $data,500);
    }
}

     public function list(Request $request){
        //In this function multiple queries can be used at the route at the same time
        //Example: {{base_url}}/blogs?user_id=1&category=Fashion
try {
    $blog_query=Blog::withCount(['comments','likes'])->with(['user:id,name,email','category:category_id,name']);

    //SEARCH FUNCTION
    // Route: {{base_url}}/blogs?keyword=fir
    //checking if keyword is passed through the parameters of the route.
    if($request->keyword){
        // if the keyword is passed then a query is attached with the blog_query to implement search function
        //remember in laravel variables are used with . and not ,
        $blog_query->where('title','LIKE','%'.$request->keyword.'%');
    }

 //CATEGORY FILTERATION FOR BLOG 
// Route: {{base_url}}/blogs?category=Fashion
//  if ($request->category): This checks if the incoming request has a parameter named category and if its value is not empty or null.
//  $blog_query->whereHas('category', function ($query) use ($request) { ... }): This method is used to filter the results of the 
//  $blog_query based on the existence of related models in the category relationship. It takes two parameters:
//  'category': This is the name of the relationship in the Blog model.
//  function ($query) use ($request) { ... }: This is a closure that defines the condition on the related category model. 
//  In this case, it adds a where clause to filter categories based on the name column matching the value of the category
//   parameter from the request.

 if($request->category){
    $blog_query->whereHas('category', function($query) use($request){
$query->where('name', $request->category);
    });
}
 // All Blogs for a perticular user
// Route: {{base_url}}/blogs?user_id=2
// Here the first parameter is the name of the field that i want to match with in the table and the route query data is second parameter.
//Searching for a single column in the table.
 if($request->user_id){
    $blog_query->where('user_id', $request->user_id);
 }
  

 //SORTING THE BLOGS TABLE DATA by blog_id or created_at.
 // Route: {{base_url}}/blogs?sortBy=created_at&sortOrder=asc
//  if ($request->sortBy && in_array($request->sortBy, ['blog_id', 'created_at'])): This condition checks if the sortBy parameter exists
//   in the request ($request->sortBy is truthy) and if its value is in the array ['blog_id', 'created_at'].
// Inside the if block:
// $sortBy = $request->sortBy;: If the condition is true, it sets the $sortBy variable to the value of sortBy from the request.
// Inside the else block:
// $sortBy = 'blog_id';: If the condition is false (either sortBy doesn't exist in the request or its value is not allowed), 
// it sets the $sortBy variable to the default value 'blog_id'.

   if($request->sortBy && in_array($request->sortBy,['blog_id','created_at'])){
$sortBy=$request->sortBy;
   }
   else{
    $sortBy='blog_id';
    // if sortBy is not passed through param then the blogs will be sorted by id as default
   }


//SORTING THE BLOGS TABLE DATA in Ascending Order or Descending Order.
   if($request->sortOrder && in_array($request->sortOrder,['asc','desc'])){
    $sortOrder=$request->sortOrder;
       }
       else{
        $sortOrder='desc';
         // if sortOrder is not passed through param then the blogs will be sorted in desc order as default
       }

  //PAGINATION or Display selected number of blogs in a page.
  //Route:{{base_url}}/blogs?paginate=1
  //Route of next page:{{base_url}}/blogs?page=2&paginate=1
  //Route with custom per page: {{base_url}}/blogs?perPage=2&paginate=1
    //Route with custom per page next page url: {{base_url}}/blogs?page=2&perPage=2&paginate=1
// Determine the number of items to display per page
$perPage = $request->perPage ?? 1; // Adjust the default value as needed

// Determine the current page
$page = $request->page ?? 1;
    if($request->paginate){
        $blogs = $blog_query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }else{
        $blogs = $blog_query->orderBy($sortBy,$sortOrder)->get();
        //function to get all the blogs from the blogs table. It will also add the user and category details
    }
       
    if($blogs){
        $data = [
            'status' => 'passed',
            'message' => 'Blogs fetched successfully',
            'count' => $blogs->count(),
            'data'=>$blogs
        ];
        return response()->json( $data, 200);
    }
    else{
        $data = [
            'status' => 'failed',
            'message' => 'No blogs found',
        ];
        return response()->json( $data, 400);
    }
} catch (\Exception $e) {
    $data = [
        'status' => 'failed',
        'message' => 'Internal Server Error',
        'error' => $e->getMessage()
    ];
    return response()->json( $data, 500 );
}
        
     }

     public function update(Request $request,$id){
        try{
            //finds the blog from the blogs table using the id and adds the user and category for that blog
            $blog=Blog::with(['user:id,name,email','category:category_id,name'])->where('blog_id',$id)->first();
            if ($blog) {

                //Matching the user id of the blog and the currently token authenticated user
                if($blog->user_id == $request->user()->id){
                    $validator = Validator::make( $request->all(), [
                        'title' => 'required|max:250',
                        'short_description' => 'required',
                        'long_description' => 'required',
                        'category_id' => 'required',
                        'image' => 'nullable|image|mimes:jpg,png'
                        //The user id is not included here at it will come from the user token generated.
                    ] );
        
                    if ( $validator->fails() ) {
                        $data = [
                            'status' => 'failed',
                            'message' => 'Validation failed',
                            'error' => $validator->errors()
                        ];
                        return response()->json( $data, 422 );
                    }
                    //Now if the image file is changed then we will check if there is any image file in the input
                    if($request->hasFile('image')){
                        //If image file exists then we add the new image path and name.
                        $image_name = time().'.'.$request->image->extension();
                        $request->image->move( public_path( '/uploads/blog_images' ), $image_name );
                        //FInding the old image from its local path.
                        $old_image_path= public_path( 'uploads/blog_images/'.$blog->image);
                        if(File::exists( $old_image_path)){
                            //If the old file exists in the said path the delete it.
                            File::delete( $old_image_path );
                        }
                    }
                    else{
                        //If image is not changed by the input then keep the image data same
                        $image_name=$blog->image;

                    }
                    //updating the blog
                    $blog->update([
                        'title' =>$request->title,
                        'short_description' =>$request->short_description,
                        'long_description' =>$request->long_description,
                        'category_id' =>$request->category_id,
                        //the image name will be stored in the database
                        'image'=>$image_name
                    ]);
        
                    $data = [
                        'status' => 'passed',
                        'message' => 'Blog updated successfully',
                        'data' =>$blog
                    ];
                    return response()->json( $data, 200 );
                }else{
                    $data = [
                        'status' => 'failed',
                        'message' => 'Access denied',
                    ];
                    return response()->json( $data, 403);
                }
            }
            else{
                $data = [
                    'status' => 'failed',
                    'message' => 'No blog found',
                ];
                return response()->json( $data, 404 );
            }
        }
        catch(\Exception $e){
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );
        }
       
     }

     public function delete($id, Request $request){
        try {
            $blog=Blog::where('blog_id',$id)->first();
            if ($blog) {

                //Matching the user id of the blog and the currently token authenticated user
                if($blog->user_id == $request->user()->id){
                    //first delete the image

                    $old_image_path= public_path( 'uploads/blog_images/'.$blog->image);
                        if(File::exists( $old_image_path)){
                            //If the old file exists in the said path the delete it.
                            File::delete( $old_image_path );
                        }

                        //Delete the record from the database
                        $blog->delete();
                        $data = [
                            'status' => 'passed',
                            'message' => 'Blog deleted successfully',
                        ];
                        return response()->json( $data, 200 );

                }else{
                    $data = [
                        'status' => 'failed',
                        'message' => 'Access denied',
                    ];
                    return response()->json( $data, 403 );
                }
            }
            else{
                $data = [
                    'status' => 'failed',
                    'message' => 'No blog found',
                ];
                return response()->json( $data, 404 );
            }
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );
        }
     }
    
     public function toggleLike($id, Request $request){
        try {
            //Finding the blog using the blog id in the blogs table
            $blog=Blog::find($id);
            if($blog){
                $user=$request->user();
                $blog_like=BlogLike::where('blog_id',$blog->blog_id)->where('user_id',$user->id)->first();
                if($blog_like){
                    $blog_like->delete();
                    $data = [
                        'status' => 'passed',
                        'message' => 'Like successfully disliked',
                    ];
                    return response()->json( $data, 200 );
                }else{
                    BlogLike::create([
                        'blog_id' => $blog->blog_id,
                        'user_id'=>$user->id,
                    ]);
                    $data = [
                        'status' => 'passed',
                        'message' => 'Blog liked',
                    ];
                    return response()->json( $data, 200 );

                }

            }else{
                $data = [
                    'status' => 'failed',
                    'message' => 'No blog found',
                ];
                return response()->json( $data, 400 );
            }
            
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );
        }

     }
        }