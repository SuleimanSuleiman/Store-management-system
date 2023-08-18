<?php

namespace App\Http\Controllers\Auth;

use App\Events\RegitserUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'password' => 'required',
                'email' => 'required|unique:admins|email'
            ]);

            if ($validator->fails()) {
                return  response()->json([
                    'success' => false,
                    'status' => 419,
                    'message' => $validator->errors()
                ])->setStatusCode(419);
            }

            $user = User::create($request->all());

            event(new RegitserUser($user));

            $credentials = request(['email', 'password']);

            if (!$token = Auth::guard('api-user')->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken($token);
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth('api-user')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    // public function refresh()
    // {

    //     $newToken = auth('api-admin')->refersh();
    //     return response()->json([
    //         'success' => True,
    //         'status' => 200,
    //         'data' => [
    //             'token' => $newToken
    //         ]
    //     ]);
    // }
}
