<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ChangePasswordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $portalType = session('portal_type');

        // Auth user (admin/staff via Laravel Auth)
        if (auth()->check()) {
            $user = auth()->user();
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->update(['password' => Hash::make($request->new_password)]);

        // Session-based user (teacher)
        } elseif ($portalType === 'teacher' && session('user_id')) {
            $user = DB::table('staff')->where('id', session('user_id'))->first();
            if (!$user || !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            DB::table('staff')->where('id', $user->id)
                ->update(['password' => Hash::make($request->new_password)]);

        // Session-based user (student)
        } elseif (session('user_id')) {
            $user = DB::table('students')->where('id', session('user_id'))->first();
            if (!$user || !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            DB::table('students')->where('id', $user->id)
                ->update(['password' => Hash::make($request->new_password)]);

        } else {
            return back()->withErrors(['current_password' => 'User not found.']);
        }

        return back()->with('pp_success', 'Password updated successfully.');
    }
}