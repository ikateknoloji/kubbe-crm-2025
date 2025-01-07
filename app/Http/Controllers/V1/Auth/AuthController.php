<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Role;
use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Rules\PasswordRule;

class AuthController extends Controller
{
    /**
     * Kullanıcı kaydı oluşturma işlemi.
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $profilePhotoPath = null;
        if ($request->hasFile('profile_image')) {
            $profilePhotoPath = $this->storeProfileImage($request->file('profile_image'));
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'profile_photo' => $profilePhotoPath,
        ]);

        $roles = Role::whereIn('id', $request->role_ids)->get();
        foreach ($roles as $role) {
            $user->roles()->attach($role->id, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Kullanıcı başarıyla kaydedildi.',
            'user'    => $user,
        ], 201);
    }

    /**
     * Kullanıcı girişi.
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Giriş bilgileri yanlış.'],
            ]);
        }

        $user = Auth::user();
        $deviceName = $request->input('device_name', 'unknown device');
        $roles = $user->roles->pluck('name');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'user'  => $user,
            'roles' => $roles,
            'token' => $token,
        ], 200);
    }

    /**
     * Kullanıcı çıkışı.
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Çıkış başarılı.',
        ], 200);
    }

    /**
     * Profil fotoğrafı yükleme işlemi.
    */  
    private function storeProfileImage($file)
    {
        $path = $file->store('profile_photos', 'public');
        return asset('storage/' . $path);
    }
}
