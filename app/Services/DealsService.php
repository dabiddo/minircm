<?php

namespace App\Services;

use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DealsService
{
    public function searchAndFilter(Request $request)
    {
        $deals = Deal::query();

        if ($request->filled('status')) {
            $validated = $request->validate(['status' => 'string|in:open,closed-won,closed-list']);
            if (! $validated) {
                throw ValidationException::withMessages(['status' => 'Invalid status value provided.']);
            }
            $deals->where('status', $request->input('status'));
        }

        if ($request->filled('currency')) {
            $deals->where('currency', $request->input('currency'));
        }

        if ($request->filled('amount')) {
            $deals->where('amount', $request->input('amount'));
        }

        if ($request->filled('title')) {
            $deals->where('title', 'LIKE', '%'.$request->input('title').'%');
        }

        return $deals->get();
    }
}
