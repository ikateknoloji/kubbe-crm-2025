<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\RoleUser;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
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
