<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return UserResource::collection($users);
    }
}
