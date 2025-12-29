<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = UserResource::collection(User::all());
        return response()->json([
            'users' => $users,
            'message' => 'Users retrieved successfully',
        ]);
    }

    public function show(User $user)
    {
        return response()->json([
            'user' => UserResource::make($user),
            'message' => 'User retrieved successfully',
        ]);
    }
}
