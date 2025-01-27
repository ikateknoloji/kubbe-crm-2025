<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\RoleUser;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{

    /**
     * Tüm rolleri getirir.
     * 

     * @return JsonResponse
     */
    public function getRoles(): JsonResponse
    {
        $roles = Role::all(); // Role modelinden tüm rolleri alıyoruz

        return response()->json([
            'message' => 'Roller başarıyla getirildi.',
            'data' => $roles,
        ], 200);
    }
    
    /**
     * Kullanıcı rollerini toplu olarak günceller.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserRoles(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $userId = $validated['user_id'];
        $newRoles = $validated['roles'];

        DB::beginTransaction();

        try {
            RoleUser::where('user_id', $userId)->delete();

            foreach ($newRoles as $roleId) {
                RoleUser::create([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Kullanıcı rolleri başarıyla güncellendi.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Kullanıcı rolleri güncellenirken bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
