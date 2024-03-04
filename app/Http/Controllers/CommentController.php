<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller {

    public function list( Request $request, $id ) {
        try {
            $blog = Blog::find( $id );
            if ( $blog ) {
                $comment = Comment::with( 'user:id,name,email' )->where( 'blog_id', $id )->orderBy( 'comment_id', 'desc' )->get();
                //If you want to paginate the comments then add the paginate( $perPage ) and include the perPage value
                $data = [
                    'status' => 'passed',
                    'message' => 'Comment fetched successfully',
                    'data' => $comment

                ];
                return response()->json( $data, 400 );
            } else {
                $data = [
                    'status' => 'failed',
                    'message' => 'No Comment found',

                ];
                return response()->json( $data, 400 );
            }

        } catch ( \Exception $e ) {
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );
        }
    }

    public function create( Request $request, $id ) {
        try {
            //finding the blog using the blog id.
            // $blog = Blog::where( 'blog_id', $id )->first();
            // or
            $blog = Blog::find( $id );
            if ( $blog ) {
                //checking validation for the comment
                $validator = Validator::make( $request->all(), [
                    'message' =>'required',
                ] );

                if ( $validator->fails() ) {
                    $data = [
                        'status' => 'failed',
                        'message' => 'Validation failed',
                        'error' => $validator->errors()
                    ];
                    return response()->json( $data, 422 );
                }
                //create a new comment in the comments table.
                $comment = Comment::create( [
                    'message' => $request->message,
                    'blog_id' => $blog->blog_id,
                    'user_id' => $request->user()->id
                ] );
                //load the user data from the user table. Create a connection to the user table in the Comment Model
                $comment->load( 'user:id,name' );

                //If you want to display the blog data too. Either add it in json response or
                $blogData = [
                    'blog_id'=>$blog->blog_id,
                    'title'=>$blog->title,
                ];

                // $data = [
                //     'status' => 'passed',
                //     'message' => 'Comment successfully created',
                //     'data' => $comment,
                //     'blog_data'=>$blogData
                // ];
                // OR
                $data = [
                    'status' => 'passed',
                    'message' => 'Comment successfully created',
                    'data' => array_merge( $comment->toArray(), $blogData ),
                ];
                return response()->json( $data, 200 );
            } else {
                $data = [
                    'status' => 'failed',
                    'message' => 'No blog found',
                ];
                return response()->json( $data, 400 );
            }

        } catch ( \Exception $e ) {
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );
        }
    }

    public function update( $id, Request $request ) {
        try {
            $comment = Comment::with( 'user:id,name,email' )->find( $id );
            if ( $comment ) {
                if ( $comment->user_id == $request->user()->id ) {
                    $validator = Validator::make( $request->all(), [
                        'message' =>'required',
                    ] );

                    if ( $validator->fails() ) {
                        $data = [
                            'status' => 'failed',
                            'message' => 'Validation failed',
                            'error' => $validator->errors()
                        ];
                        return response()->json( $data, 422 );
                    }

                    $comment->update( [
                        'message' => $request->message
                    ] );
                    $data = [
                        'status' => 'passed',
                        'message' => 'Comment updated successfully',
                        'data' =>$comment
                    ];
                    return response()->json( $data, 200 );

                } else {
                    $data = [
                        'status' => 'failed',
                        'message' => 'Access denied',
                    ];
                    return response()->json( $data, 403 );
                }

            } else {
                $data = [
                    'status' => 'failed',
                    'message' => 'No comment found',
                ];
                return response()->json( $data, 400 );
            }

        } catch ( \Exception $e ) {
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );
        }
    }

    public function delete( $id, Request $request ) {
        try {
            $comment = Comment::find( $id );
            if ( $comment ) {
                if ( $comment->user_id == $request->user()->id ) {
                    $comment->delete();
                    $data = [
                        'status' => 'passed',
                        'message' => 'Comment deleted successfully',
                        
                    ];
                    return response()->json( $data, 200 );
                } else {
                    $data = [
                        'status' => 'failed',
                        'message' => 'Access denied',
                    ];
                    return response()->json( $data, 403 );
                }
            } else {
                $data = [
                    'status' => 'failed',
                    'message' => 'No Comment found',
                ];
                return response()->json( $data, 400 );
            }

        } catch ( \Exception $e ) {
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );
        }
    }
}