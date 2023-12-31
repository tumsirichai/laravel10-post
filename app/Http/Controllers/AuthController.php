<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
        * @OA\Post(
        * path="/auth/register",
        * operationId="Register",
        * tags={"Authentication"},
        * summary="User Register",
        * description="User Register here",
        *     @OA\RequestBody(
        *         @OA\JsonContent()
        *     ),
        *      @OA\Response(
        *          response=201,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */
    public function register( Request $request){
        $data = $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create($data);

        $expiresAt = $this->tokenExpireIn();
        $token = $user->createToken('api-token',['*'],$expiresAt)->plainTextToken;

        return response()->json([
            'token' =>$token,
            'Type' => 'Bearer'
        ]);
    }

    /**
        * @OA\Post(
        * path="/auth/login",
        * operationId="authLogin",
        * tags={"Authentication"},
        * summary="User Login",
        * description="Login User Here",
        *     @OA\RequestBody(
        *         @OA\JsonContent()
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Wrong credentials'
            ]);
        }

        $expiresAt = $this->tokenExpireIn();
        $token = $user->createToken('api-token',['*'],$expiresAt)->plainTextToken;

        return response()->json([
            'token' => $token,
            'name' => $user->name,
            'Type' => 'Bearer',
            'role' => $user->role
        ]);
    }
    
    /**
     * @OA\Post(
     * path="/auth/logout",
     * operationId="authLogout",
     * tags={"Authentication"},
     * summary="User Logout",
     * description="User Logout here",
     * @OA\Response(response="200", description="Success"),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
       return [
        'message' => 'logged out'
       ];
    }
    
    protected function tokenExpireIn(){
        // return $expiresAt = now()->addHour();
        return  now()->addDay();
    }
    
}
