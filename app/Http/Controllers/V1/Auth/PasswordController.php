<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


use App\Models\User;

class PasswordController extends Controller
{
    /**
     * Şifre güncelleme işlemi.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Doğrulama hatası.',
                'details' => $validator->errors(),
            ], 422);
        }
    
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'error' => 'Kullanıcı kimliği doğrulanamadı. Lütfen giriş yapın.',
            ], 401);
        }
    
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => 'Mevcut şifre yanlış. Lütfen doğru şifreyi girin.',
            ], 422);
        }
    
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        return response()->json([
            'message' => 'Şifre başarıyla güncellendi.',
        ], 200);
    }


    /**
     * Şifre sıfırlama işlemi (admin veya sistem tarafından).
     */
    public function reset(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($request->user_id);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Şifre başarıyla sıfırlandı.',
        ], 200);
    }
}
