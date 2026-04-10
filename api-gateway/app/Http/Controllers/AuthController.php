<?php

namespace App\Http\Controllers;

use App\Services\AuthServiceProxy;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceProxy $auth
    ) {
    }

    public function login(Request $request)
    {
        $response = $this->auth->login($request->all());

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function refresh(Request $request)
    {
        $response = $this->auth->refresh($request->all());

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function me(Request $request)
    {
        $response = $this->auth->me($request->bearerToken());

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function logout(Request $request)
    {
        $response = $this->auth->logout($request->bearerToken());
        return response()->json(
            $response->json(),
            $response->status()
        );
    }
}
