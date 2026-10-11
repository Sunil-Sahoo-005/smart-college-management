<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingInvoice;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class BillingInvoiceController extends Controller
{
    protected $invoiceListRoute = 'admin.billing-invoices.index';

    public function index(Request $request)
    {
        $invoices = BillingInvoice::with('tenant')
            ->latest()
            ->paginate(20);

        return view('billing-invoices.index', compact('invoices'));
    }

    public function create()
    {
        $invoice = new BillingInvoice;
        $tenants = Tenant::where('status', 'active')->get();

        return view('billing-invoices.invoice-form', compact('invoice', 'tenants'));
    }

    public function edit($id)
    {
        $invoice = BillingInvoice::with('tenant')->find($id);

        if (empty($invoice)) {
            abort(404);
        }

        $tenants = Tenant::where('status', 'active')->get();

        return view('billing-invoices.invoice-form', compact('invoice', 'tenants'));
    }

    public function store(Request $request)
    {
        $rules = [
            'tenant_id' => 'required|exists:tenants,id',
            'invoice_number' => 'required|unique:billing_invoices,invoice_number,' . $request->get('id'),
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|size:3',
            'due_date' => 'required|date',
            'paid_at' => 'nullable|date|after_or_equal:due_date',
            'status' => 'required',
        ];

        $messages = [
            'tenant_id.required' => 'Please choose tenant!',
            'tenant_id.exists' => 'Tenant not found!',
            'invoice_number.required' => 'Please enter invoice number!',
            'invoice_number.unique' => 'Invoice number already exists!',
            'amount.required' => 'Please enter amount!',
            'amount.numeric' => 'Amount must be a number!',
            'amount.min' => 'Amount must be greater than 0!',
            'currency.required' => 'Please choose currency!',
            'currency.size' => 'Currency must be 3 characters!',
            'due_date.required' => 'Please choose due date!',
            'due_date.date' => 'Due date must be a valid date!',
            'paid_at.date' => 'Paid date must be a valid date!',
            'paid_at.after_or_equal' => 'Paid date must be equal to or after due date!',
            'status.required' => 'Please choose status!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $invoice = BillingInvoice::find($request->get('id'));
            if (empty($invoice)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Invoice updated successfully',
            ];
        } else {
            $invoice = new BillingInvoice;
            $redirect = [
                'url' => redirect($this->invoiceListRoute),
                'msg' => 'Invoice created successfully',
            ];
        }

        $invoice->tenant_id = $request->get('tenant_id');
        $invoice->invoice_number = trim($request->get('invoice_number'));
        $invoice->amount = $request->get('amount');
        $invoice->currency = $request->get('currency');
        $invoice->due_date = $request->get('due_date');
        $invoice->paid_at = $request->get('paid_at');
        $invoice->status = $request->get('status');

        try {
            $invoice->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function show($id)
    {
        $invoice = BillingInvoice::with('tenant')->find($id);

        if (empty($invoice)) {
            abort(404);
        }

        return view('billing-invoices.show', compact('invoice'));
    }

    public function destroy($id)
    {
        $invoice = BillingInvoice::find($id);

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->delete();

        return redirect()->route('admin.billing-invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
