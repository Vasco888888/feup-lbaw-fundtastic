<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnbanAppeal;

class AppealController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'reason' => 'required|string|max:2000',
            'user_id' => 'nullable|integer',
        ]);

        try {
            UnbanAppeal::create([
                'user_id' => $request->input('user_id') ?: $request->user()?->id,
                'reason' => $request->input('reason'),
                'status' => 'pending',
            ]);

            return back()->with('flash_message', 'Your appeal has been submitted. Administrators will review it shortly.');
        } catch (\Throwable $e) {
            return back()->with('flash_error', 'Unable to submit appeal at the moment.');
        }
    }
}
