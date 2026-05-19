<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;

class UserRegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userData = User::create($request->validated());

        return response()->success('User Created Successfully.', $userData);
    }
}
