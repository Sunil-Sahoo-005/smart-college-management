<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    protected $planListRoute = 'admin.subscription-plans.index';

    public function index(Request $request)
    {
        $plans = SubscriptionPlan::latest()->paginate(20);

        return view('subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        $plan = new SubscriptionPlan;

        return view('subscription-plans.plan-form', compact('plan'));
    }

    public function edit($id)
    {
        $plan = SubscriptionPlan::find($id);

        if (empty($plan)) {
            abort(404);
        }

        return view('subscription-plans.plan-form', compact('plan'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:1|max:100',
            'description' => 'nullable|max:500',
            'monthly_price' => 'required|numeric|min:0',
            'annual_price' => 'required|numeric|min:0',
            'max_users' => 'nullable|integer|min:0',
            'max_students' => 'nullable|integer|min:0',
            'max_staff' => 'nullable|integer|min:0',
            'is_active' => 'required',
        ];

        $messages = [
            'name.required' => 'Please enter name!',
            'name.min' => 'Name should be minimum of 1 character!',
            'name.max' => 'Name should be maximum of 100 characters!',
            'description.max' => 'Description should be maximum of 500 characters!',
            'monthly_price.required' => 'Please enter monthly price!',
            'monthly_price.numeric' => 'Monthly price must be a number!',
            'monthly_price.min' => 'Monthly price must be 0 or more!',
            'annual_price.required' => 'Please enter annual price!',
            'annual_price.numeric' => 'Annual price must be a number!',
            'annual_price.min' => 'Annual price must be 0 or more!',
            'max_users.integer' => 'Max users must be a number!',
            'max_users.min' => 'Max users must be 0 or more!',
            'max_students.integer' => 'Max students must be a number!',
            'max_students.min' => 'Max students must be 0 or more!',
            'max_staff.integer' => 'Max staff must be a number!',
            'max_staff.min' => 'Max staff must be 0 or more!',
            'is_active.required' => 'Please choose active status!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $plan = SubscriptionPlan::find($request->get('id'));
            if (empty($plan)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Subscription plan updated successfully',
            ];
        } else {
            $plan = new SubscriptionPlan;
            $redirect = [
                'url' => redirect($this->planListRoute),
                'msg' => 'Subscription plan created successfully',
            ];
        }

        $plan->name = trim($request->get('name'));
        $plan->description = $request->get('description');
        $plan->monthly_price = $request->get('monthly_price');
        $plan->annual_price = $request->get('annual_price');
        $plan->max_users = $request->get('max_users');
        $plan->max_students = $request->get('max_students');
        $plan->max_staff = $request->get('max_staff');
        $plan->is_active = $request->get('is_active') ? true : false;

        try {
            $plan->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function show($id)
    {
        $plan = SubscriptionPlan::find($id);

        if (empty($plan)) {
            abort(404);
        }

        return view('subscription-plans.show', compact('plan'));
    }

    public function destroy($id)
    {
        $plan = SubscriptionPlan::find($id);

        if (empty($plan)) {
            abort(404);
        }

        $plan->delete();

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Subscription plan deleted successfully.');
    }
}
