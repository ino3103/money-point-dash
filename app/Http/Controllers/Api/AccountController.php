<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\TransactionLine;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountController extends Controller
{
    /**
     * List all accounts
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $query = Account::with('user');

        if ($request->has('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $accounts = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $accounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'user_id' => $account->user_id,
                    'user_name' => $account->user->name ?? null,
                    'account_type' => $account->account_type,
                    'provider' => $account->provider,
                    'balance' => $account->account_type === 'float' ? abs($account->balance) : $account->balance,
                    'currency' => $account->currency,
                    'is_active' => $account->is_active,
                ];
            })
        ]);
    }

    /**
     * Get account ledger
     */
    public function ledger(Request $request, $id)
    {
        if ($request->user()->cannot('View Ledger')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $account = Account::findOrFail($id);

        $query = TransactionLine::where('account_id', $account->id)
            ->with(['transaction.user', 'transaction.tellerShift']);

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $lines = $query->orderBy('created_at', 'desc')->get();

        // Calculate running balance
        $balance = $account->balance;
        $ledger = $lines->map(function ($line) use (&$balance) {
            $balance -= $line->amount; // Subtract because lines are reversed
            return [
                'id' => $line->id,
                'date' => $line->created_at->toISOString(),
                'description' => $line->transaction->reference ?? $line->transaction->type,
                'debit' => $line->amount > 0 ? abs($line->amount) : 0,
                'credit' => $line->amount < 0 ? abs($line->amount) : 0,
                'balance' => $balance,
                'transaction_type' => $line->transaction->type,
                'user_name' => $line->transaction->user->name ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'account' => [
                    'id' => $account->id,
                    'account_type' => $account->account_type,
                    'provider' => $account->provider,
                    'current_balance' => $account->account_type === 'float' ? abs($account->balance) : $account->balance,
                ],
                'ledger' => $ledger,
            ]
        ]);
    }
}
