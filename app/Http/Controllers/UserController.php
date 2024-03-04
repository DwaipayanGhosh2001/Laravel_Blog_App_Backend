<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {
    public function register( Request $request ) {
        try {

            // Using the validator library to check validations
            $validator = Validator::make( $request-> all(), [
                'name' =>'required|max:100',
                'email' =>'required|email|unique:users',
                'password' =>'required|min:3',
                'confirm_password' =>'required|same:password'
            ] );
            if ( $validator->fails() ) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Validation failed',
                    'error' => $validator->errors()
                ];
                return response()->json( $data, 422 );

            }

            $user = User::create( [
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> Hash::make( $request->password )
            ] );

            if ( $user ) {
                return response()->json( [
                    'status' => 'success',
                    'message' => 'User created successfully',
                ], 201 );
            } else {
                return response()->json( [
                    'status' => 'failed',
                    'message' => 'Unable to create user',
                ], 400 );
            }
        } catch ( Exception $e ) {
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );
        }
    }

    public function login( Request $request ) {
        try {
            $validator = Validator::make( $request-> all(), [
                'email' =>'required|email',
                'password' =>'required|min:3',
            ] );
            if ( $validator->fails() ) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Validation failed',
                    'error' => $validator->errors()
                ];
                return response()->json( $data, 400 );

            }
            //searching the user table for the email and taking the first match.
            $user = User::where( 'email', $request->email )->first();

            if ( $user ) {
                //Checking the psasword with the password in the user data.
                if ( Hash::check( $request->password, $user->password ) ) {
                    //generating token using Sanctum. Read the documentation to install and use sanctum. Alternative is Passport.
                    $user_token = $user->createToken( 'auth_token' )->plainTextToken;
                    return response()->json( [
                        'status' => 'passed',
                        'message' => 'User  logged in successfully',
                        'token' => $user_token
                    ], 200 );
                } else {
                    return response()->json( [
                        'status' => 'failed',
                        'message' => 'Password is incorrect',
                    ], 404 );
                }
            } else {
                return response()->json( [
                    'status' => 'failed',
                    'message' => 'Email is not registered',
                ], 404 );
            }

        } catch ( Exception $e ) {
            $data = [
                'status' => 'failed',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ];
            return response()->json( $data, 500 );

        }
    }

    public function update(REquest $request){
        try {
            $user=$request->user();
            if($user){
                $validator = Validator::make( $request->all(), [
                    'name' =>'required|max:100',
                    'email' =>'required|email|unique:users',
                ] );
                if ( $validator->fails() ) {
                    $data = [
                        'status' => 'failed',
                        'message' => 'Validation failed',
                        'error' => $validator->errors()
                    ];
                    return response()->json( $data, 422 );
    
                }
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email
                ]);
                $data = [
                    'status' => 'passed',
                    'message' => 'User updated successfully',
                    'data' => $user
                ];
                return response()->json( $data, 200 );
            }else{
                $data = [
                    'status' => 'failed',
                    'message' => 'No user found',
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

    public function changePassword( Request $request ) {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required|min:5',
                'confirm_password' => 'required|same:new_password',
            ] );
            if ( $validator->fails() ) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Validation failed',
                    'error' => $validator->errors()
                ];
                return response()->json( $data, 422 );
            }

            $user = $request->user();
            if($user){
                if (Hash::check($request->old_password, $user->password)) {
                    $user->update([
                        'password' => Hash::make( $request->new_password )
                    ]);
                    $data = [
                        'status' => 'passed',
                        'message' => 'Passwrd updated successfully',
                    ];
                    return response()->json( $data, 200 );
                } else {
                    $data = [
                        'status' => 'failed',
                        'message' => 'Old password do not match',
                    ];
                    return response()->json( $data, 400 );
                }
                

            }else{
                $data = [
                    'status' => 'failed',
                    'message' => 'User not found',
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

    public function userDetails( Request $request ) {
        //searches for the user through the token
        try {

            $user = $request->user();
            //sending only the user name, email and id of the user.
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
            return response()->json( [
                'status' => 'passed',
                'message' => 'User  details found',
                'data' => $userData
            ], 200 );
        } catch ( \Exception $e ) {
            return response()->json( [
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500 );
        }

    }

    public function logout( Request $request ) {
        try {
            $user = $request->user();

            $user->currentAccessToken()->delete();
            return response()->json( [
                'status' => 'passed',
                'message' => 'User logged out'
            ], 200 );

        } catch ( \Exception $e ) {
            return response()->json( [
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500 );
        }
    }

}
