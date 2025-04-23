<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone_number'  => 'required|string|unique:users,phone_number',
            'address'       => 'nullable|string',
            'pin'           => 'required|string|min:4|max:6',
        ]);

        // Buat user baru
        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'phone_number'  => $request->phone_number,
            'address'       => $request->address,
            'pin'           => Hash::make($request->pin),
            'balance'       => 0,
        ]);

        // Response sukses
        return response()->json([
            'status' => 'SUCCESS',
            'result' => [
                'user_id'      => $user->id,
                'first_name'   => $user->first_name,
                'last_name'    => $user->last_name,
                'phone_number' => $user->phone_number,
                'address'      => $user->address,
                'created_date' => $user->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'phone_number' => 'required',
            'pin' => 'required',
        ]);

        // Cari user berdasarkan phone_number
        $user = User::where('phone_number', $request->phone_number)->first();

        // Cek kredensial
        if (!$user || !Hash::check($request->pin, $user->pin)) {
            return response()->json([
                'status' => 'FAILED',
                'message' => "Phone number and pin don't match."
            ], 401);
        }

        // Hapus token lama jika ada
        $user->tokens()->delete();

        // Buat token baru
        $accessToken = $user->createToken('access_token')->plainTextToken;

        // Response dengan token
        return response()->json([
            'status' => 'SUCCESS',
            'result' => [
                'access_token' => $accessToken,
                'user_id' => $user->id
            ]
        ]);
    }
}
