<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserHasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $userListRoute = 'users.index';

    public function index()
    {
        $users = User::query()
            // ->whereNotNull('tenant_id') // only school‑level users, not super_admin
            // ->with('tenant')
            ->latest()
            ->paginate(20);
        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function addUser()
    {
        $user = new User;
        $userTypes = User::getUserTypes();
        $tenants = Tenant::where('status', 'active')->get();
        // $roles = User::getUserTypes(); // you can separate this later if needed
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('users.user-form', [
            'tenants' => $tenants,
            'user' => $user,
            'userTypes' => $userTypes,
            'roles' => $roles,
        ]);
    }

    public function editUser($id)
    {
        $userTypes = User::getUserTypes();
        $tenants = Tenant::where('status', 'active')->get();
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();
        $user = User::getUserById($id);

        if (empty($user)) {
            abort(404);
        }
        $userRoleId = $user->roles->first()->id ?? null;
        return view('users.user-form', [
            'tenants' => $tenants,
            'user' => $user,
            'userTypes' => $userTypes,
            'roles' => $roles,
            'userRoleId' => $userRoleId,
        ]);
    }

    // public function saveUser(Request $request)
    // {
    //     // dd($request->all());
    //     $rules = [
    //         'first_name' => 'required|min:1|max:100',
    //         'last_name' => 'required|min:1|max:100',
    //         // 'role' => 'required',
    //         'email' => 'required|email:rfc,dns|max:100|unique:users,email,' . $request->get('id'),
    //         'phone' => 'required|min:10|max:15|unique:users,phone,' . $request->get('id'),
    //         // 'status' => 'required',
    //     ];

    //     $messages = [
    //         'first_name.required' => 'Please enter first name!',
    //         'first_name.min' => 'First name should be minimum of 1 character!',
    //         'first_name.max' => 'First name should be maximum of 100 characters!',
    //         'last_name.required' => 'Please enter last name!',
    //         'last_name.min' => 'Last name should be minimum of 1 character!',
    //         'last_name.max' => 'Last name should be maximum of 100 characters!',
    //         // 'role.required' => 'Please choose role!',
    //         'email.required' => 'Please enter an email!',
    //         'email.email' => 'Please enter a valid email!',
    //         'email.max' => 'Email should be maximum of 100 characters!',
    //         'email.unique' => 'Email already exists!',
    //         'phone.required' => 'Please enter phone number!',
    //         'phone.min' => 'Phone number should be at least 10 digits!',
    //         'phone.max' => 'Phone number should be maximum of 15 digits!',
    //         'phone.unique' => 'Phone number already exists!',
    //         'status.required' => 'Please choose status!',
    //     ];

    //     if (empty($request->get('id'))) {
    //         $rules['password'] = 'required|min:8|max:15';
    //         $messages['password.required'] = 'Please enter password!';
    //         $messages['password.min'] = 'Password must be 8-15 characters long!';
    //         $messages['password.max'] = 'Password must be 8-15 characters long!';
    //     }

    //     $validator = Validator::make($request->all(), $rules, $messages);

    //     if ($validator->fails()) {
    //         $errorMessage = $validator->errors()->first();
    //         return redirect()->back()->withErrors($errorMessage)->withInput();
    //     }

    //     if (!empty($request->get('id'))) {
    //         $user = User::getUserById($request->get('id'));
    //         if (empty($user)) {
    //             abort(404);
    //         }
    //         $redirect = [
    //             'url' => redirect()->back(),
    //             'msg' => 'User updated successfully',
    //         ];
    //     } else {
    //         $user = new User;
    //         $redirect = [
    //             'url' => redirect($this->userListRoute),
    //             'msg' => 'User created successfully',
    //         ];
    //         $user->password = Hash::make($request->get('password'));
    //     }

    //     $user->tenant_id = $request->get('tenant_id'); // set tenant if needed
    //     $user->first_name = trim($request->get('first_name'));
    //     $user->last_name = trim($request->get('last_name'));
    //     $user->email = trim($request->get('email'));
    //     $user->phone = trim($request->get('phone'));
    //     $user->role = $request->get('role_id');
    //     // $user->role = 'admin'; // set role as admin for school-level users
    //     $user->status = $request->get('status') ? 'active' : 'inactive';

    //     try {
    //         $user->save();
    //         return $redirect['url']->with('success', $redirect['msg']);
    //     } catch (\Exception $e) {
    //         echo $e->getMessage();exit;
    //         return redirect()->back()->with('error', 'Something went wrong, please try again later!');
    //     }
    // }

    public function saveUser(Request $request)
    {
        $rules = [
            'first_name' => 'required|min:1|max:100',
            'last_name'  => 'required|min:1|max:100',
            'email'      => 'required|email:rfc,dns|max:100|unique:users,email,' . $request->id,
            'phone'      => 'required|min:10|max:15|unique:users,phone,' . $request->id,
            'role_id'    => 'required|exists:roles,id',
        ];

        if (empty($request->id)) {
            $rules['password'] = 'required|min:8|max:15';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors()->first())
                ->withInput();
        }

        DB::beginTransaction();

        try {

            if (!empty($request->id)) {
                $user = User::findOrFail($request->id);
                $message = 'User updated successfully';
                $redirect = redirect()->back();
            } else {
                $user = new User();
                $user->password = Hash::make($request->password);
                $message = 'User created successfully';
                $redirect = redirect()->route($this->userListRoute);
            }

            // USER TABLE (USER TYPE)
            $user->tenant_id  = $request->tenant_id;
            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->email      = $request->email;
            $user->phone      = $request->phone;
            $user->role       = $request->user_type; // ✅ USER TYPE
            $user->status     = $request->status ? 'active' : 'inactive';
            $user->save();

            // RBAC ROLE
            UserHasRole::where('user_id', $user->id)->delete();

            UserHasRole::create([
                'user_id' => $user->id,
                'role_id' => $request->role_id,
            ]);

            DB::commit();
            return $redirect->with('success', $message);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;

            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function showUser($id)
    {
        $user = User::getUserById($id);

        if (empty($user)) {
            abort(404);
        }

        return view('users.show', compact('user'));
    }

    public function destroyUser($id)
    {
        $user = User::getUserById($id);

        if (empty($user)) {
            abort(404);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
