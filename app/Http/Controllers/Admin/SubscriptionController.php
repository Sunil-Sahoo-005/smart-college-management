<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionListRoute = 'admin.subscriptions.index';

    public function index(Request $request)
    {
        $subscriptions = Subscription::with(['tenant', 'plan'])
            ->latest()
            ->paginate(20);

        return view('subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $subscription = new Subscription;
        $tenants = Tenant::where('status', 'active')->get();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('subscriptions.subscription-form', compact('subscription', 'tenants', 'plans'));
    }

    public function edit($id)
    {
        $subscription = Subscription::with(['tenant', 'plan'])->find($id);

        if (empty($subscription)) {
            abort(404);
        }

        $tenants = Tenant::where('status', 'active')->get();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('subscriptions.subscription-form', compact('subscription', 'tenants', 'plans'));
    }

    public function store(Request $request)
    {
        $rules = [
            'tenant_id' => 'required|exists:tenants,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after_or_equal:starts_at',
            'status' => 'required',
            'payment_method' => 'nullable|max:50',
        ];

        $messages = [
            'tenant_id.required' => 'Please choose tenant!',
            'tenant_id.exists' => 'Tenant not found!',
            'plan_id.required' => 'Please choose plan!',
            'plan_id.exists' => 'Plan not found!',
            'starts_at.required' => 'Please choose start date!',
            'starts_at.date' => 'Start date must be a valid date!',
            'expires_at.required' => 'Please choose expiry date!',
            'expires_at.date' => 'Expiry date must be a valid date!',
            'expires_at.after_or_equal' => 'Expiry date must be equal to or after start date!',
            'status.required' => 'Please choose status!',
            'payment_method.max' => 'Payment method should be maximum of 50 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $subscription = Subscription::find($request->get('id'));
            if (empty($subscription)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Subscription updated successfully',
            ];
        } else {
            $subscription = new Subscription;
            $redirect = [
                'url' => redirect($this->subscriptionListRoute),
                'msg' => 'Subscription created successfully',
            ];
        }

        $subscription->tenant_id = $request->get('tenant_id');
        $subscription->plan_id = $request->get('plan_id');
        $subscription->starts_at = $request->get('starts_at');
        $subscription->expires_at = $request->get('expires_at');
        $subscription->status = $request->get('status');
        $subscription->payment_method = $request->get('payment_method');

        try {
            $subscription->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function show($id)
    {
        $subscription = Subscription::with(['tenant', 'plan'])->find($id);

        if (empty($subscription)) {
            abort(404);
        }

        return view('subscriptions.show', compact('subscription'));
    }

    public function destroy($id)
    {
        $subscription = Subscription::find($id);

        if (empty($subscription)) {
            abort(404);
        }

        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully.');
    }
}
