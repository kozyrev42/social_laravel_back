<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class RegistrationController extends Controller
{
    public function userRegistration(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        // если пользователь с таким email уже существует, то выкидываем ошибку
        if ($user) {
            throw ValidationException::withMessages([
                'email' => ['Пользователь с таким email уже существует.'],
            ]);
        }

        $data['password'] = Hash::make($data['password']); // хеширум пароль
        $user = User::create($data);

        // получаем токен по id нового пользователя
        $token = auth()->tokenById($user->id);

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);
    }
}
