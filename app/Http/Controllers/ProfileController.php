<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show()
    {
        $portalType = session('portal_type');
        $user = auth()->user();

        if (!$user && session('user_id')) {
            if ($portalType === 'teacher') {
                $user = DB::table('staff')->where('id', session('user_id'))->first();
            } else {
                $user = DB::table('students')->where('id', session('user_id'))->first();
            }
        }

        $tenantName = 'Default';
        if ($user && isset($user->tenant_id)) {
            $tenantName = DB::table('tenants')->where('id', $user->tenant_id)->value('name') ?? 'Default';
        }

        return view('profile.index', compact('user', 'portalType', 'tenantName'));
    }
}