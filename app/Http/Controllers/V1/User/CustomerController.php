<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Müşteri rolündeki kullanıcıları listele.
     */
    public function index()
    {
        // Müşteri rolüne sahip kullanıcıları çekiyoruz.
        $customers = User::whereHas('roles', function ($query) {
            $query->where('name', 'musteri'); 
        })->get(['id', 'name', 'email', 'profile_photo']); 

        return response()->json([
            'success' => true,
            'data' => $customers,
        ], 200);
    }
}
