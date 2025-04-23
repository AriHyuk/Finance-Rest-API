<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class TransactionController extends Controller
{
    public function topUp(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'FAILED',
                'message' => 'Unauthenticated'
            ], 401);
        }

        DB::transaction(function () use ($validated, $user) {
            $user->balance += $validated['amount'];
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $validated['amount'],
                'type' => 'Top Up'
            ]);
        });

        return response()->json(['status' => 'SUCCESS', 'message' => 'Top Up successful']);
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1'
        ]);

        $fromUser = Auth::user();
        $toUser = User::find($validated['to_user_id']);

        if ($fromUser->balance < $validated['amount']) {
            return response()->json(['status' => 'FAILED', 'message' => 'Insufficient balance']);
        }

        DB::transaction(function () use ($validated, $fromUser, $toUser) {
            $fromUser->balance -= $validated['amount'];
            $toUser->balance += $validated['amount'];
            $fromUser->save();
            $toUser->save();

            Transaction::create([
                'user_id' => $fromUser->id,
                'amount' => -$validated['amount'],
                'type' => 'Transfer'
            ]);
            Transaction::create([
                'user_id' => $toUser->id,
                'amount' => $validated['amount'],
                'type' => 'Transfer'
            ]);
        });

        return response()->json(['status' => 'SUCCESS', 'message' => 'Transfer successful']);
    }

    public function reportTransactions(Request $request)
    {
        $userId = Auth::id();
        $transactions = Transaction::where('user_id', $userId)->get();

        return response()->json(['status' => 'SUCCESS', 'result' => $transactions]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string',
            'address' => 'nullable|string',
        ]);

        $user = Auth::user();
        $user->update($validated);

        return response()->json(['status' => 'SUCCESS', 'result' => $user]);
    }
}