<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Kullanıcı modelinde 'roles' ilişkisi tanımlı olmalı
        $users = User::with('roles')
        ->get(['id', 'name', 'email', 'profile_photo']);

        return response()->json($users);
    }
}
