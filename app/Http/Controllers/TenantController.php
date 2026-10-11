<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    protected $tenantListRoute = 'admin.tenants.index';
    
    // Define constants for better maintainability
    private const VALID_TIMEZONES = ['Asia/Kolkata', 'Asia/Dubai'];
    private const VALID_CURRENCIES = ['INR', 'USD', 'EUR'];
    private const VALID_STATUSES = ['active', 'inactive'];
    
    private const NAME_MAX_LENGTH = 100;
    private const PHONE_MIN_LENGTH = 10;
    private const PHONE_MAX_LENGTH = 15;
    private const EMAIL_MAX_LENGTH = 100;
    private const ADDRESS_MAX_LENGTH = 500;
    private const LOGO_URL_MAX_LENGTH = 255;

    /**
     * Display a listing of tenants.
     */
    public function index(Request $request)
    {
        $tenants = Tenant::latest()->paginate(20);
         $roles   = \App\Models\Role::orderBy('name')->get();
        return view('tenants.index', compact('tenants', 'roles'));
    }
public function roleUsers($tenantId, $roleId)
{
    $tenant = $this->findTenantOrFail($tenantId);
    $role   = \App\Models\Role::findOrFail($roleId);

    $users = \App\Models\User::where('tenant_id', $tenantId)
        ->whereHas('roles', fn($q) => $q->where('roles.id', $roleId))
        ->paginate(20);

    return view('tenants.role-users', compact('tenant', 'role', 'users'));
}
    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        $tenant = new Tenant();
        return view('tenants.tenant-form', compact('tenant'));
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request)
    {
        // Convert checkbox status before validation
        $this->prepareStatusForValidation($request);
        
        $validator = $this->validateTenant($request);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tenantId = $request->get('id');
        $isUpdate = !empty($tenantId);
        
        if ($isUpdate) {
            $tenant = $this->findTenantOrFail($tenantId);
            $redirect = redirect()->back();
            $successMessage = 'Tenant updated successfully';
        } else {
            $tenant = new Tenant();
            $redirect = redirect()->route($this->tenantListRoute);
            $successMessage = 'Tenant created successfully';
        }

        $this->fillTenantData($tenant, $request);

        try {
            $tenant->save();
            return $redirect->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Tenant save error: ' . $e->getMessage(), [
                'tenant_id' => $tenant->id ?? null,
                'request_data' => $request->except(['_token', '_method'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Something went wrong, please try again later!')
                ->withInput();
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show($id)
    {
        $tenant = $this->findTenantOrFail($id);

        // Fetch tenant-specific statistics
        $stats = [
            'students_count' => $tenant->students()->count(),
            'staff_count'   => $tenant->staff()->count(),
            'users_count'   => $tenant->users()->count(),
            'years_count'   => $tenant->academicYears()->count(),
        ];

        // Fetch assigned administrators for this tenant
        $admins = $tenant->users()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->latest()
            ->get();

        return view('tenants.show', compact('tenant', 'stats', 'admins'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit($id)
    {
        $tenant = $this->findTenantOrFail($id);
        return view('tenants.tenant-form', compact('tenant'));
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy($id)
    {
        $tenant = $this->findTenantOrFail($id);
        
        if ($this->hasAssociatedData($tenant)) {
            return redirect()->route($this->tenantListRoute)
                ->with('error', 'Cannot delete tenant because it has associated records (users, students, staff, departments, academic years, etc.)!');
        }

        try {
            $tenant->delete();
            return redirect()->route($this->tenantListRoute)
                ->with('success', 'Tenant deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Tenant deletion error: ' . $e->getMessage(), [
                'tenant_id' => $id
            ]);
            
            return redirect()->route($this->tenantListRoute)
                ->with('error', 'Unable to delete tenant. Please try again later.');
        }
    }
    
    /**
     * Prepare status field for validation (convert checkbox to valid status)
     */
    private function prepareStatusForValidation(Request $request): void
    {
        // Handle checkbox status - checkbox sends '1' when checked, nothing when unchecked
        if ($request->has('status') && $request->get('status') == '1') {
            $request->merge(['status' => 'active']);
        } elseif (!$request->has('status')) {
            // If status field is not present (unchecked checkbox), set to 'inactive'
            $request->merge(['status' => 'inactive']);
        }
        // If status is something else, keep as is (will be caught by validation)
    }
    
    /**
     * Validate tenant data
     */
    private function validateTenant(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $tenantId = $request->get('id');
        
        $rules = [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:' . self::NAME_MAX_LENGTH,
                Rule::unique('tenants', 'name')->ignore($tenantId, 'id')
            ],
            'phone' => [
                'nullable',
                'string',
                'min:' . self::PHONE_MIN_LENGTH,
                'max:' . self::PHONE_MAX_LENGTH,
                'regex:/^[0-9+\-\s]+$/',
                Rule::unique('tenants', 'phone')->ignore($tenantId, 'id')
            ],
            'email' => [
                'nullable',
                'string',
                'email:rfc,dns',
                'max:' . self::EMAIL_MAX_LENGTH,
                Rule::unique('tenants', 'email')->ignore($tenantId, 'id')
            ],
            'timezone' => 'required|string|in:' . implode(',', self::VALID_TIMEZONES),
            'currency' => 'required|string|size:3|in:' . implode(',', self::VALID_CURRENCIES),
            'academic_year_start_month' => 'required|integer|between:1,12',
            'status' => 'required|string|in:' . implode(',', self::VALID_STATUSES),
            'address' => 'nullable|string|max:' . self::ADDRESS_MAX_LENGTH,
            'logo_url' => 'nullable|url|max:' . self::LOGO_URL_MAX_LENGTH,
        ];

        $messages = $this->getValidationMessages();

        return Validator::make($request->all(), $rules, $messages);
    }
    
    /**
     * Fill tenant model with request data
     */
    private function fillTenantData(Tenant $tenant, Request $request): void
    {
        $tenant->name = trim($request->get('name'));
        $tenant->logo_url = $request->get('logo_url');
        $tenant->address = trim($request->get('address'));
        $tenant->phone = $request->get('phone');
        $tenant->email = $request->get('email');
        $tenant->timezone = $request->get('timezone');
        $tenant->currency = $request->get('currency');
        $tenant->academic_year_start_month = (int) $request->get('academic_year_start_month');
        $tenant->status = $request->get('status'); // Now this will be 'active' or 'inactive'
    }
    
    /**
     * Get validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            // Name validation messages
            'name.required' => 'Please enter tenant name!',
            'name.min' => 'Name should be minimum of 1 character!',
            'name.max' => 'Name should be maximum of ' . self::NAME_MAX_LENGTH . ' characters!',
            'name.unique' => 'This tenant name already exists!',
            
            // Phone validation messages
            'phone.min' => 'Phone number should be at least ' . self::PHONE_MIN_LENGTH . ' digits!',
            'phone.max' => 'Phone number should be maximum of ' . self::PHONE_MAX_LENGTH . ' digits!',
            'phone.regex' => 'Phone number can only contain numbers, +, -, and spaces!',
            'phone.unique' => 'This phone number is already registered with another tenant!',
            
            // Email validation messages
            'email.email' => 'Please enter a valid email address!',
            'email.max' => 'Email should be maximum of ' . self::EMAIL_MAX_LENGTH . ' characters!',
            'email.unique' => 'This email address is already registered with another tenant!',
            
            // Timezone validation messages
            'timezone.required' => 'Please choose timezone!',
            'timezone.in' => 'Please select a valid timezone!',
            
            // Currency validation messages
            'currency.required' => 'Please choose currency!',
            'currency.size' => 'Currency must be 3 characters!',
            'currency.in' => 'Please select a valid currency!',
            
            // Academic year validation messages
            'academic_year_start_month.required' => 'Please choose academic year start month!',
            'academic_year_start_month.integer' => 'Academic year start month must be a number!',
            'academic_year_start_month.between' => 'Academic year start month must be between 1 and 12!',
            
            // Status validation messages
            'status.required' => 'Please select status!',
            'status.in' => 'Invalid status value!',
            
            // Address validation messages
            'address.max' => 'Address should be maximum of ' . self::ADDRESS_MAX_LENGTH . ' characters!',
            
            // Logo URL validation messages
            'logo_url.url' => 'Please enter a valid URL for logo!',
            'logo_url.max' => 'Logo URL should be maximum of ' . self::LOGO_URL_MAX_LENGTH . ' characters!',
        ];
    }
    
    /**
     * Find tenant or fail with 404
     */
    private function findTenantOrFail($id): Tenant
    {
        $tenant = Tenant::find($id);
        
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        
        return $tenant;
    }
    
    /**
     * Check if tenant has any associated data
     * This method checks only the relationships that exist in the Tenant model
     */
    private function hasAssociatedData(Tenant $tenant): bool
    {
        // Check for associated records - only check relationships that exist in your Tenant model
        // From your Tenant model, these relationships exist:
        // - users
        // - students
        // - staff (not teachers)
        // - departments
        // - academicYears
        // - auditLogs (if model exists)
        // - dataBackups (if model exists)
        
        // Check users relationship
        if ($tenant->users()->exists()) {
            return true;
        }
        
        // Check students relationship
        if ($tenant->students()->exists()) {
            return true;
        }
        
        // Check staff relationship (not teachers)
        if ($tenant->staff()->exists()) {
            return true;
        }
        
        // Check departments relationship
        if ($tenant->departments()->exists()) {
            return true;
        }
        
        // Check academicYears relationship
        if ($tenant->academicYears()->exists()) {
            return true;
        }
        
        // Check auditLogs relationship only if the model exists
        if (class_exists(\App\Models\AuditLog::class) && $tenant->auditLogs()->exists()) {
            return true;
        }
        
        // Check dataBackups relationship only if the model exists
        if (class_exists(\App\Models\DataBackup::class) && $tenant->dataBackups()->exists()) {
            return true;
        }
        
        return false;
    }
}