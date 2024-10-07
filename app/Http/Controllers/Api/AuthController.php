<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      summary="Login to the system and get API Token",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Login successfull",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessAuth")
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorLoginValidation")
     *      )
     * )
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $token = auth()->user()->createToken("ApiToken")->accessToken;

        return response()->json([
            "message" => "Authenticated as " . auth()->user()->name,
            "token" => $token,
            "user" => auth()->user()
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/register",
     *      summary="Register user account",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Registration successfull",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessAuth")
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorRegisterValidation")
     *      )
     * )
     */
    public function register(RegistrationRequest $request)
    {
        $user = User::create($request->validated());

        $token = $user->createToken("ApiToken")->accessToken;

        return response()->json([
            "message" => "Authenticated as " . $user->name,
            "token" => $token,
            "user" => $user
        ]);
    }
}
