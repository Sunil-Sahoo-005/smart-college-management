<?php


// use App\Http\Controllers\LoginController;

use App\Http\Controllers\Account\ExpenseHeadController;
use App\Http\Controllers\Account\ExpenseRecordController;
use App\Http\Controllers\Account\IncomeHeadController;
use App\Http\Controllers\Account\IncomeRecordController;
use App\Http\Controllers\Account\LedgerController;
use App\Http\Controllers\Account\ScholarshipController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AttendancePeriodController;
use App\Http\Controllers\Admin\BillingInvoiceController;
use App\Http\Controllers\Admin\BookFinesController;
use App\Http\Controllers\Admin\StudentAcademicQualificationController;
use App\Http\Controllers\Admin\BookIssuesController;
use App\Http\Controllers\Admin\AcademicGroupsController;
use App\Http\Controllers\Admin\HolidaysController;
use App\Http\Controllers\Admin\BooksController;
use App\Http\Controllers\Admin\LibraryMasterController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ClassStreamMappingController;
use App\Http\Controllers\Admin\ClassSubjectMappingController;
use App\Http\Controllers\Admin\ExamCenterController;
use App\Http\Controllers\Admin\ClassTeacherController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\BankAccountsController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\GradeLevelController;
use App\Http\Controllers\Admin\GradingSystemController;
use App\Http\Controllers\Admin\HostelAllocationController;
use App\Http\Controllers\Admin\HostelbedController;
use App\Http\Controllers\Admin\HostelController;
use App\Http\Controllers\Admin\HostelFeeController;
use App\Http\Controllers\Admin\Hostelroomcontroller;
use App\Http\Controllers\Admin\LibraryCardsController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\PortalSettingController;
use App\Http\Controllers\Admin\RoutesController;
use App\Http\Controllers\Admin\RouteStopsController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffDocumentController;
use App\Http\Controllers\Admin\StaffImportController;
use App\Http\Controllers\Admin\StreamController;
use App\Http\Controllers\Admin\StudentAddressController;
use App\Http\Controllers\Admin\StudentAttendanceController;
use App\Http\Controllers\Admin\StudentClassEnrollmentController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\StudentDocumentController;
use App\Http\Controllers\Admin\StudentParentController;
use App\Http\Controllers\Admin\StudentRouteMappingController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\Admin\TimetableSlotController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\StudentloginController;
use App\Http\Controllers\teacher\teacherController;
use App\Http\Controllers\Hr\HrDashboardController;
use App\Http\Controllers\Hr\StaffController as HrStaffController;
use App\Http\Controllers\Hr\AttendanceConfigController;
use App\Http\Controllers\Hr\AttendanceController;
use App\Http\Controllers\Hr\SalaryStructureController;
use App\Http\Controllers\Hr\StaffSalaryAssignmentController;
use App\Http\Controllers\Hr\PayrollController;
use App\Http\Controllers\Hr\LeaveRequestController;
use App\Http\Controllers\Hr\LeaveTypeController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\Admin\ExamTypeController;
use App\Http\Controllers\Admin\PracticalGroupController;
use App\Http\Controllers\Admin\PracticalTimetableController;
use App\Http\Controllers\Admin\GroupMasterController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamMarkController;
use App\Http\Controllers\Admin\FeeCategoryController;
use App\Http\Controllers\Admin\FeeStructureController;
use App\Http\Controllers\Admin\StudentFeePlanController;
use App\Http\Controllers\Admin\FeeInstallmentController;
use App\Http\Controllers\Admin\FeePaymentController;
use App\Http\Controllers\Admin\MergedClassScheduleController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\StudentRecordController;
use App\Http\Controllers\Admin\StudentAccountController;
use App\Http\Controllers\Admin\CenterDeviceController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\QuestionPaperTypeController;

// use App\Http\Controllers\Student\StudentloginController;
// use App\Http\Controllers\teacher\teacherController;
use App\Http\Controllers\Admin\NoticeBoardController; //new
use App\Http\Controllers\Admin\StockEntryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ChangePasswordController;

Route::get('/demo', function () {
    return view('Academics.index');
});
Route::get('/blank', function () {
    return view('blankpage');
});

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/otp-verify',  [LoginController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/otp-verify', [LoginController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/otp-resend', [LoginController::class, 'resendOtp'])->name('otp.resend');

Route::get('/my-profile', [App\Http\Controllers\ProfileController::class, 'show'])
    ->name('profile.show')
    ->middleware('web');
Route::get('/tenants/{id}/roles/{roleId}/users', [TenantController::class, 'roleUsers'])
    ->name('admin.tenants.role.users');
Route::post('/change-password', [App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])
    ->name('change.password')
    ->middleware('web');
    Route::post('admin/question-paper-types/{id}/toggle-status', 
    [App\Http\Controllers\Admin\QuestionPaperTypeController::class, 'toggleStatus']
)->name('admin.question_paper_types.toggle_status');
// user routes
Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');

Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');

Route::middleware(['auth'])->group(function () {
    // superdashboard 
    Route::get('/home', [DashboardController::class, 'superdashboard'])
        // ->middleware('permission:students.view')
        ->name('super_admin.dashboard');

    Route::get('/dashboard', [LoginController::class, 'dashboard'])
        // ->middleware('permission:View Dashboard')
        ->name('admin.dashboard');

    Route::get('/academic/dashboard', [DashboardController::class, 'AcademicDashboard'])
        // ->middleware('permission:view academic')
        ->name('academic.dashboard');

    // Route::get('/tenant/dashboard', [LoginController::class, 'dashboard'])->name('tenant.dashboard');
    // Route::get('/student/dashboard', [LoginController::class, 'dashboard'])->name('student.dashboard');
    // Route::get('/teacher/dashboard', [LoginController::class, 'dashboard'])->name('teacher.dashboard');

    Route::get('/accountant/dashboard', [DashboardController::class, 'AccountDashboard'])
        // ->middleware('permission:view account')
        ->name('accountant.dashboard');
    Route::get('/hr/dashboard', [HrDashboardController::class, 'index'])->name('hr.dashboard');
    Route::get('/hr/{tenantKey}', [HrDashboardController::class, 'tenantDashboard'])->name('hr.tenant.dashboard');
    Route::get('/hr/{tenantKey}/staff', [HrStaffController::class, 'index'])->name('hr.staff.index');
    Route::get('/hr/{tenantKey}/staff/create', [HrStaffController::class, 'create'])->name('hr.staff.create');
    Route::post('/hr/{tenantKey}/staff', [HrStaffController::class, 'store'])->name('hr.staff.store');
    Route::get('/hr/{tenantKey}/staff/{staff}/edit', [HrStaffController::class, 'edit'])->name('hr.staff.edit');
    Route::get('/hr/{tenantKey}/staff/{staff}', [HrStaffController::class, 'show'])->name('hr.staff.show');
    Route::delete('/hr/{tenantKey}/staff/{staff}', [HrStaffController::class, 'destroy'])->name('hr.staff.destroy');
    Route::get('/hr/{tenantKey}/attendance-config', [AttendanceConfigController::class, 'edit'])->name('hr.attendance-config.edit');
    Route::put('/hr/{tenantKey}/attendance-config', [AttendanceConfigController::class, 'update'])->name('hr.attendance-config.update');
    Route::get('/hr/{tenantKey}/attendance', [AttendanceController::class, 'index'])->name('hr.attendance.index');
    Route::get('/hr/{tenantKey}/attendance/export', [AttendanceController::class, 'export'])->name('hr.attendance.export');
    Route::get('/hr/{tenantKey}/attendance/template', [AttendanceController::class, 'downloadTemplate'])->name('hr.attendance.template');
    Route::post('/hr/{tenantKey}/attendance/import', [AttendanceController::class, 'import'])->name('hr.attendance.import');
    Route::get('/hr/{tenantKey}/salary-structures', [SalaryStructureController::class, 'index'])->name('hr.salary-structures.index');
    Route::get('/hr/{tenantKey}/salary-structures/create', [SalaryStructureController::class, 'create'])->name('hr.salary-structures.create');
    Route::post('/hr/{tenantKey}/salary-structures', [SalaryStructureController::class, 'store'])->name('hr.salary-structures.store');
    Route::get('/hr/{tenantKey}/salary-structures/{salaryStructure}/edit', [SalaryStructureController::class, 'edit'])->name('hr.salary-structures.edit');
    Route::put('/hr/{tenantKey}/salary-structures/{salaryStructure}', [SalaryStructureController::class, 'update'])->name('hr.salary-structures.update');
    Route::delete('/hr/{tenantKey}/salary-structures/{salaryStructure}', [SalaryStructureController::class, 'destroy'])->name('hr.salary-structures.destroy');
    Route::get('/hr/{tenantKey}/salary-assignments', [StaffSalaryAssignmentController::class, 'index'])->name('hr.staff-salary-assignments.index');
    Route::get('/hr/{tenantKey}/salary-assignments/create', [StaffSalaryAssignmentController::class, 'create'])->name('hr.staff-salary-assignments.create');
    Route::post('/hr/{tenantKey}/salary-assignments', [StaffSalaryAssignmentController::class, 'store'])->name('hr.staff-salary-assignments.store');
    Route::get('/hr/{tenantKey}/salary-assignments/{assignment}/edit', [StaffSalaryAssignmentController::class, 'edit'])->name('hr.staff-salary-assignments.edit');
    Route::put('/hr/{tenantKey}/salary-assignments/{assignment}', [StaffSalaryAssignmentController::class, 'update'])->name('hr.staff-salary-assignments.update');
    Route::delete('/hr/{tenantKey}/salary-assignments/{assignment}', [StaffSalaryAssignmentController::class, 'destroy'])->name('hr.staff-salary-assignments.destroy');
    Route::get('/hr/{tenantKey}/salary-assignments/lookup', [StaffSalaryAssignmentController::class, 'lookup'])->name('hr.staff-salary-assignments.lookup');
    Route::get('/hr/{tenantKey}/payrolls', [PayrollController::class, 'index'])->name('hr.payrolls.index');
    Route::get('/hr/{tenantKey}/payrolls/create', [PayrollController::class, 'create'])->name('hr.payrolls.create');
    Route::post('/hr/{tenantKey}/payrolls', [PayrollController::class, 'store'])->name('hr.payrolls.store');
    Route::get('/hr/{tenantKey}/payrolls/{payrollRun}', [PayrollController::class, 'show'])->name('hr.payrolls.show');
    Route::post('/hr/{tenantKey}/payslips/{payslip}/publish', [PayrollController::class, 'publishPayslip'])->name('hr.payslips.publish');
    Route::post('/hr/{tenantKey}/payslips/{payslip}/payment', [PayrollController::class, 'recordPayment'])->name('hr.payslips.payment.store');
    Route::get('/hr/{tenantKey}/payslips/{payslip}', [PayrollController::class, 'showPayslip'])->name('hr.payslips.show');
    Route::get('/hr/{tenantKey}/payslips/{payslip}/edit', [PayrollController::class, 'editPayslip'])->name('hr.payslips.edit');
    Route::put('/hr/{tenantKey}/payslips/{payslip}', [PayrollController::class, 'updatePayslip'])->name('hr.payslips.update');
    Route::get('/hr/{tenantKey}/leave-requests', [LeaveRequestController::class, 'index'])->name('hr.leave-requests.index');
    Route::get('/hr/{tenantKey}/leave-requests/create', [LeaveRequestController::class, 'create'])->name('hr.leave-requests.create');
    Route::post('/hr/{tenantKey}/leave-requests', [LeaveRequestController::class, 'store'])->name('hr.leave-requests.store');
    Route::get('/hr/{tenantKey}/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('hr.leave-requests.show');
    Route::get('/hr/{tenantKey}/leave-requests/{leaveRequest}/edit', [LeaveRequestController::class, 'edit'])->name('hr.leave-requests.edit');
    Route::put('/hr/{tenantKey}/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'update'])->name('hr.leave-requests.update');
    Route::delete('/hr/{tenantKey}/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('hr.leave-requests.destroy');
    Route::post('/hr/{tenantKey}/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('hr.leave-requests.approve');
    Route::post('/hr/{tenantKey}/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('hr.leave-requests.reject');
    Route::get('/hr/{tenantKey}/leave-types', [LeaveTypeController::class, 'index'])
        ->name('hr.leave-types.index');
    Route::get('/hr/{tenantKey}/leave-types/create', [LeaveTypeController::class, 'create'])
        ->name('hr.leave-types.create');
    Route::post('/hr/{tenantKey}/leave-types', [LeaveTypeController::class, 'store'])
        ->name('hr.leave-types.store');
    Route::get('/hr/{tenantKey}/leave-types/{leaveType}/edit', [LeaveTypeController::class, 'edit'])
        ->name('hr.leave-types.edit');
    Route::put('/hr/{tenantKey}/leave-types/{leaveType}', [LeaveTypeController::class, 'update'])
        ->name('hr.leave-types.update');
    Route::delete('/hr/{tenantKey}/leave-types/{leaveType}', [LeaveTypeController::class, 'destroy'])
        ->name('hr.leave-types.destroy');

    Route::get('/hr/{tenantKey}/holidays', [App\Http\Controllers\Hr\HrHolidaysController::class, 'index'])
        ->name('hr.holidays.index');
        
Route::middleware(['auth'])->group(function () {
    Route::get('accountants/scholarships', [App\Http\Controllers\ScholarshipAssignmentController::class, 'index'])->name('scholarships.list');
    Route::get('accountants/scholarships/assign', [App\Http\Controllers\ScholarshipAssignmentController::class, 'showForm'])->name('scholarships.assign.form');
    Route::post('accountants/scholarships/assign', [App\Http\Controllers\ScholarshipAssignmentController::class, 'assign'])->name('scholarships.assign');
    Route::post('accountants/scholarships/{id}/approve', [App\Http\Controllers\ScholarshipAssignmentController::class, 'approve'])->name('scholarships.approve');
    Route::post('accountants/scholarships/{id}/reject', [App\Http\Controllers\ScholarshipAssignmentController::class, 'reject'])->name('scholarships.reject');
    Route::post('accountants/scholarships/fetch-student', [App\Http\Controllers\ScholarshipAssignmentController::class, 'fetchStudent'])->name('scholarships.fetchStudent');
});
    Route::get('/librarian/dashboard', [DashboardController::class, 'LibraryDashboard'])
        // ->middleware('permission:view library')
        ->name('library.dashboard');
    Route::get('/travels/dashboard', [DashboardController::class, 'TravelsDashboard'])
        // ->middleware('permission:View Transportation')
        ->name('travels.dashboard');

    Route::get('/communications/dashboard', [DashboardController::class, 'CommunicationsDashboard'])->name('communications.dashboard');
    Route::get('/hostel/dashboard', [DashboardController::class, 'HostelDashboard'])
        // ->middleware('permission:view hostell')
        ->name('hostel.dashboard');

    // users 
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:view user')
        ->name('users.index');
    Route::get('/users/create', [UserController::class, 'addUser'])
        ->middleware('permission:create user')
        ->name('users.create');
    Route::get('/users/{id}/edit', [UserController::class, 'editUser'])
        ->middleware('permission:edit user')
        ->name('users.edit');
    Route::post('/users/store', [UserController::class, 'saveUser'])
        ->middleware('permission:create user')
        ->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'showUser'])
        ->middleware('permission:view user')
        ->name('users.show');
    Route::delete('/users/{id}', [UserController::class, 'destroyUser'])
        ->middleware('permission:delete user')
        ->name('users.destroy');

    // tenants 
    Route::get('/tenants', [TenantController::class, 'index'])
        // ->middleware('permission:view tenant')
        ->name('admin.tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])
        // ->middleware('permission:create tenant')
        ->name('admin.tenants.create');
    Route::get('/tenants/{id}/edit', [TenantController::class, 'edit'])
        // ->middleware('permission:edit tenant')
        ->name('admin.tenants.edit');
    Route::post('/tenants/store', [TenantController::class, 'store'])
        // ->middleware('permission:create tenant')
        ->name('admin.tenants.store');
    Route::get('/tenants/{id}', [TenantController::class, 'show'])
        // ->middleware('permission:view tenant')
        ->name('admin.tenants.show');
    Route::delete('/tenants/{id}', [TenantController::class, 'destroy'])
        // ->middleware('permission:delete tenant')
        ->name('admin.tenants.destroy');

    // subscription-plans
    Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name('admin.subscription-plans.index');
    Route::get('/subscription-plans/create', [SubscriptionPlanController::class, 'create'])->name('admin.subscription-plans.create');
    Route::get('/subscription-plans/{id}/edit', [SubscriptionPlanController::class, 'edit'])->name('admin.subscription-plans.edit');
    Route::post('/subscription-plans/store', [SubscriptionPlanController::class, 'store'])->name('admin.subscription-plans.store');
    Route::get('/subscription-plans/{id}', [SubscriptionPlanController::class, 'show'])->name('admin.subscription-plans.show');
    Route::delete('/subscription-plans/{id}', [SubscriptionPlanController::class, 'destroy'])->name('admin.subscription-plans.destroy');

    // subscriptions 
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
    Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('admin.subscriptions.create');
    Route::get('/subscriptions/{id}/edit', [SubscriptionController::class, 'edit'])->name('admin.subscriptions.edit');
    Route::post('/subscriptions/store', [SubscriptionController::class, 'store'])->name('admin.subscriptions.store');
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])->name('admin.subscriptions.show');
    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy'])->name('admin.subscriptions.destroy');

    // billing-invoices
    Route::get('/billing-invoices', [BillingInvoiceController::class, 'index'])->name('admin.billing-invoices.index');
    Route::get('/billing-invoices/create', [BillingInvoiceController::class, 'create'])->name('admin.billing-invoices.create');
    Route::get('/billing-invoices/{id}/edit', [BillingInvoiceController::class, 'edit'])->name('admin.billing-invoices.edit');
    Route::post('/billing-invoices/store', [BillingInvoiceController::class, 'store'])->name('admin.billing-invoices.store');
    Route::get('/billing-invoices/{id}', [BillingInvoiceController::class, 'show'])->name('admin.billing-invoices.show');
    Route::delete('/billing-invoices/{id}', [BillingInvoiceController::class, 'destroy'])->name('admin.billing-invoices.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])
        // ->middleware('permission:view role')
        ->name('admin.roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])
        // ->middleware('permission:create role')
        ->name('admin.roles.create');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])
        // ->middleware('permission:edit role')
        ->name('admin.roles.edit');
    Route::post('/roles/store', [RoleController::class, 'store'])
        // ->middleware('permission:create role')
        ->name('admin.roles.store');
    Route::get('/roles/{id}', [RoleController::class, 'show'])
        // ->middleware('permission:view role')
        ->name('admin.roles.show');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
        // ->middleware('permission:delete role')
        ->name('admin.roles.destroy');

    // Permissions 
    Route::get('/permissions', [PermissionController::class, 'index'])
        // ->middleware('permission:view permission')
        ->name('admin.permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        // ->middleware('permission:create permission')
        ->name('admin.permissions.create');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])
        // ->middleware('permission:edit permission')
        ->name('admin.permissions.edit');
    Route::post('/permissions/store', [PermissionController::class, 'store'])
        // ->middleware('permission:create permission')
        ->name('admin.permissions.store');
    Route::get('/permissions/{id}', [PermissionController::class, 'show'])
        // ->middleware('permission:view permission')
        ->name('admin.permissions.show');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])
        // ->middleware('permission:delete permission')
        ->name('admin.permissions.destroy');

    // students 
    // Route::get('/students', [StudentController::class, 'index'])->name('admin.students.index')->middleware('permission:create user');
    Route::post('students/preview', [StudentController::class, 'preview'])->name('admin.students.preview');
    Route::get('/students', [StudentController::class, 'index'])->name('admin.students.index')->middleware('permission:View Student');
    Route::get('/students/create', [StudentController::class, 'create'])->name('admin.students.create')->middleware('permission:Create Student');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('admin.students.edit')->middleware('permission:Edit Student');
    Route::get('/students/streams/{courseId}', [StudentController::class, 'getStreams'])->name('admin.students.streams');
    Route::get('/students/classes/{courseId}/{streamId}', [StudentController::class, 'getClasses'])->name('admin.students.classes');
    Route::get('/students/sections/{courseId}/{streamId}/{classId}', [StudentController::class, 'getSections'])->name('admin.students.sections');
    Route::get('/students/seat-availability', [StudentController::class, 'seatAvailability'])->name('admin.students.seat-availability');
    Route::post('/students/store', [StudentController::class, 'store'])->name('admin.students.store')->middleware('permission:Create Student');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('student/template', [StudentController::class, 'downloadTemplate'])->name('admin.students.template');
    Route::get('/admin/students/{student}/check-fees', [StudentController::class, 'checkPendingFees'])->name('admin.students.check-fees');
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('admin.students.show')->middleware('permission:View Student');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('admin.students.destroy')->middleware('permission:Delete Student');

    Route::prefix('students')->name('admin.students.')->group(function () {
        Route::get('create/import',          [StudentImportController::class, 'showImport'])->name('import.show');
        Route::get('import/template', [StudentImportController::class, 'downloadTemplate'])->name('import.template');
        Route::post('create/import',          [StudentImportController::class, 'import'])->name('import.process');
    });

    // Student Parents
    Route::post('/students/{student}/parents', [StudentParentController::class, 'store'])
        ->name('admin.students.parents.store');

    Route::put('/students/{student}/parents/{parent}', [StudentParentController::class, 'update'])
        ->name('admin.students.parents.update');

    Route::delete('/students/{student}/parents/{parent}', [StudentParentController::class, 'destroy'])
        ->name('admin.students.parents.destroy');

    // Student Address Routes
    Route::post('/admin/students/{student}/addresses', [StudentAddressController::class, 'store'])->name('admin.students.addresses.store');
    Route::put('/admin/students/{student}/addresses/{address}', [StudentAddressController::class, 'update'])->name('admin.students.addresses.update');
    Route::delete('/admin/students/{student}/addresses/{address}', [StudentAddressController::class, 'destroy'])->name('admin.students.addresses.destroy');

    // Student Document Routes
    Route::post('/admin/students/{student}/documents', [StudentDocumentController::class, 'store'])->name('admin.students.documents.store');
    Route::delete('/admin/students/{student}/documents/{document}', [StudentDocumentController::class, 'destroy'])->name('admin.students.documents.destroy');

    Route::get('/admin/routes/{route}/stops', [StudentController::class, 'getStops'])->name('admin.routes.stops');
    Route::post('/admin/student/{student}/transport', [StudentController::class, 'storeTransport'])->name('admin.student.transport.store');
    Route::post('/admin/student/{student}/transport/destroy', [StudentController::class, 'destroyTransport'])->name('admin.student.transport.destroy');

    Route::post('/admin/student/{student}/hostel', [StudentController::class, 'storeHostel'])->name('admin.student.hostel.store');
    // Route::get('/admin/hostels/{hostel}/rooms', [StudentController::class, 'getHostelRooms'])->name('admin.hostels.rooms');
    Route::get('/admin/rooms/{room}/beds', [StudentController::class, 'getHostelBeds'])->name('admin.rooms.beds');

    // ✅ Student Records
    Route::get('/students/{student}/records', [StudentRecordController::class, 'index'])->name('admin.student.records.index');
    Route::get('/students/{student}/records/create', [StudentRecordController::class, 'create'])->name('admin.student.records.create');
    Route::get('/students/{student}/records/{record}/edit', [StudentRecordController::class, 'edit'])->name('admin.student.records.edit');
    Route::post('/students/{student}/records', [StudentRecordController::class, 'store'])->name('admin.student.records.store');
    Route::delete('/students/records/{record}', [StudentRecordController::class, 'destroy'])->name('admin.student.records.destroy');

    // marks
    // Route::get('/students/{student}/exams', [StudentExamController::class, 'index'])->name('admin.student.exam_marks.index');

    // departments
    Route::get('/departments', [DepartmentController::class, 'index'])->name('admin.departments.index')->middleware('permission:View Department');
    Route::get('/departments/create', [DepartmentController::class, 'create'])->name('admin.departments.create')->middleware('permission:Create Department');
    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('admin.departments.edit')->middleware('permission:Edit Department');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('admin.departments.store')->middleware('permission:Create Department');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy')->middleware('permission:Delete Department');



    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/designations', [DesignationController::class, 'index'])->name('admin.designations.index')->middleware('permission:View Designation');
    Route::get('/designations/create', [DesignationController::class, 'create'])->name('admin.designations.create')->middleware('permission:Create Designation');
    Route::get('/designations/{designation}/edit', [DesignationController::class, 'edit'])->name('admin.designations.edit')->middleware('permission:Edit Designation');
    Route::post('/designations', [DesignationController::class, 'store'])->name('admin.designations.store')->middleware('permission:Create Designation');
    Route::delete('/designations/{designation}', [DesignationController::class, 'destroy'])->name('admin.designations.destroy')->middleware('permission:Delete Designation');
    // });


    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/staff', [StaffController::class, 'index'])->name('admin.staff.index')->middleware('permission:view Staff');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('admin.staff.create')->middleware('permission:Create Staff');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('admin.staff.edit')->middleware('permission:Edit Staff');
    Route::post('/staff', [StaffController::class, 'store'])->name('admin.staff.store')->middleware('permission:Create Staff');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('admin.staff.destroy')->middleware('permission:Delete Staff');
    Route::get('/staff/{staff}/show', [StaffController::class, 'show'])->name('admin.staff.show')->middleware('permission:view Staff');


    Route::get('/staff/{staff}/classes', [StaffController::class, 'showClassesForTimetable'])->name('admin.staff.classes');
    Route::get('/staff/{staff}/attendance/{class}/{section?}', [StaffController::class, 'showAttendance'])->name('admin.staff.attendance');
    Route::post('/staff/{staff}/attendance/{class}/{section?}', [StaffController::class, 'storeAttendance'])->name('admin.staff.attendance.store');
    Route::post('/students/import', [StudentController::class, 'import'])->name('admin.students.import');
    // });

    Route::prefix('staff')->name('admin.staff.')->group(function () {
        Route::get('import',          [StaffImportController::class, 'showImport'])->name('import.show');
        Route::get('import/template', [StaffImportController::class, 'downloadTemplate'])->name('import.template');
        Route::post('import',          [StaffImportController::class, 'import'])->name('import.process');
    });

    // Staff Documents
    Route::get('/staff/{staff}/documents', [StaffDocumentController::class, 'index'])->name('admin.staff.documents.index');
    Route::get('/staff/{staff}/documents/create', [StaffDocumentController::class, 'create'])->name('admin.staff.documents.create');
    Route::post('/staff/{staff}/documents', [StaffDocumentController::class, 'store'])->name('admin.staff.documents.store');
    Route::get('/staff/{staff}/documents/{document}/edit', [StaffDocumentController::class, 'edit'])->name('admin.staff.documents.edit');
    Route::put('/staff/{staff}/documents/{document}', [StaffDocumentController::class, 'update'])->name('admin.staff.documents.update');
    Route::delete('/staff/{staff}/documents/{document}', [StaffDocumentController::class, 'destroy'])->name('admin.staff.documents.destroy');


    // Staff Schedule Documents
    Route::post('/staff/{staff}/schedule-documents', [StaffDocumentController::class, 'storeScheduleDocument'])->name('admin.staff.schedule-documents.store');
    Route::delete('/staff/schedule-documents/{id}', [StaffDocumentController::class, 'deleteScheduleDocument'])->name('admin.staff.schedule-documents.destroy');

    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('admin.academic_years.index');
    Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('admin.academic_years.create');
    Route::get('/academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('admin.academic_years.edit');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('admin.academic_years.store');
    Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'store'])->name('admin.academic_years.update'); // ADD THIS LINE
    Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('admin.academic_years.destroy');
    // });





    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/terms', [TermController::class, 'index'])->name('admin.terms.index');
    Route::get('/terms/create', [TermController::class, 'create'])->name('admin.terms.create');
    Route::get('/terms/{term}/edit', [TermController::class, 'edit'])->name('admin.terms.edit');
    Route::post('/terms', [TermController::class, 'store'])->name('admin.terms.store');
    Route::delete('/terms/{term}', [TermController::class, 'destroy'])->name('admin.terms.destroy');
    // });


    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/classes', [ClassController::class, 'index'])->name('admin.classes.index')->middleware('permission:View Branch');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('admin.classes.create')->middleware('permission:View Branch');
    Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('admin.classes.edit')->middleware('permission:View Branch');
    Route::post('/classes', [ClassController::class, 'store'])->name('admin.classes.store')->middleware('permission:View Branch');
    Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('admin.classes.destroy')->middleware('permission:View Branch');
    // });


    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/sections', [SectionController::class, 'index'])->name('admin.sections.index');
    Route::get('/sections/create', [SectionController::class, 'create'])->name('admin.sections.create');
    Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('admin.sections.edit');
    Route::post('/sections', [SectionController::class, 'store'])->name('admin.sections.store');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('admin.sections.destroy');
    // });

    Route::prefix('courses')->name('admin.courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index')->middleware('permission:View Courses');
        Route::get('/create', [CourseController::class, 'create'])->name('create')->middleware('permission:Create Courses');
        Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit')->middleware('permission:Edit Course');
        Route::post('/store', [CourseController::class, 'store'])->name('store')->middleware('permission:Create Courses');
        Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Course');
    });

    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Route::get('/streams', [StreamController::class, 'index'])->name('admin.streams.index');
    // Route::get('/streams/create', [StreamController::class, 'create'])->name('admin.streams.create');
    // Route::get('/streams/{stream}/edit', [StreamController::class, 'edit'])->name('admin.streams.edit');
    // Route::post('/streams', [StreamController::class, 'store'])->name('admin.streams.store');
    // Route::delete('/streams/{stream}', [StreamController::class, 'destroy'])->name('admin.streams.destroy');
    // // });
    // Route::get('streams/{stream}/manage',            [StreamController::class, 'manage'])       ->name('admin.streams.manage');
    // Route::post('streams/{stream}/add-class',        [StreamController::class, 'addClass'])     ->name('admin.streams.addClass');
    // Route::delete('streams/{stream}/remove-class/{class}', [StreamController::class, 'removeClass']) ->name('admin.streams.removeClass');

    // Route::post('streams/{stream}/mappings/{mapping}/sections', [StreamController::class, 'storeSection'])->name('admin.streams.mappings.sections.store');
    // Route::delete('streams/{stream}/mappings/{mapping}/sections/{section}', [StreamController::class, 'destroySection'])->name('admin.streams.mappings.sections.destroy');

    // Route::post('streams/{stream}/mappings/{mapping}/subjects', [StreamController::class, 'storeSubject'])->name('admin.streams.mappings.subjects.store');
    // Route::delete('streams/{stream}/mappings/{mapping}/subjects/{subjectMapping}', [StreamController::class, 'destroySubject'])->name('admin.streams.mappings.subjects.destroy');
    Route::prefix('streams')->name('admin.streams.')->group(function () {

        // Stream CRUD
        Route::get('/',              [StreamController::class, 'index'])->name('index')->middleware('permission:View Branch');
        Route::get('/create',        [StreamController::class, 'create'])->name('create')->middleware('permission:Create Branch');
        Route::get('/{stream}/edit', [StreamController::class, 'edit'])->name('edit')->middleware('permission:Edit Branch');
        Route::post('/store',        [StreamController::class, 'store'])->name('store')->middleware('permission:Create Branch');
        Route::delete('/{stream}',   [StreamController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Branch');

        // Manage — scoped to a course_stream pivot row
        // {courseStream} = id from `course_stream` table
        Route::get('/manage/{courseStream}', [StreamController::class, 'manage'])->name('manage');

        // Class ↔ CourseStream
        Route::post('/manage/{courseStream}/classes',                [StreamController::class, 'addClass'])->name('addClass');
        Route::delete('/manage/{courseStream}/classes/{class}',      [StreamController::class, 'removeClass'])->name('removeClass');

        // Sections
        Route::post(
            '/manage/{courseStream}/mappings/{mapping}/sections',
            [StreamController::class, 'storeSection']
        )->name('mappings.sections.store');

        Route::delete(
            '/manage/{courseStream}/mappings/{mapping}/sections/{section}',
            [StreamController::class, 'destroySection']
        )->name('mappings.sections.destroy');

        // Subjects
        Route::post(
            '/manage/{courseStream}/mappings/{mapping}/subjects',
            [StreamController::class, 'storeSubject']
        )->name('mappings.subjects.store');

        Route::delete(
            '/manage/{courseStream}/mappings/{mapping}/subjects/{subjectMapping}',
            [StreamController::class, 'destroySubject']
        )->name('mappings.subjects.destroy');
    });

    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/class-stream-mappings', [ClassStreamMappingController::class, 'index'])->name('admin.class_stream_mappings.index');
    Route::get('/class-stream-mappings/create', [ClassStreamMappingController::class, 'create'])->name('admin.class_stream_mappings.create');
    Route::get('/class-stream-mappings/{mapping}/edit', [ClassStreamMappingController::class, 'edit'])->name('admin.class_stream_mappings.edit');
    Route::post('/class-stream-mappings', [ClassStreamMappingController::class, 'store'])->name('admin.class_stream_mappings.store');
    Route::delete('/class-stream-mappings/{mapping}', [ClassStreamMappingController::class, 'destroy'])->name('admin.class_stream_mappings.destroy');
    // });



    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/subjects', [SubjectController::class, 'index'])->name('admin.subjects.index')->middleware('permission:View Subject');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('admin.subjects.create')->middleware('permission:Create Subject');
    Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('admin.subjects.edit')->middleware('permission:Edit Subject');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('admin.subjects.store')->middleware('permission:Create Subject');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('admin.subjects.destroy')->middleware('permission:Delete Subject');
    // });



    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/class-subject-mappings', [ClassSubjectMappingController::class, 'index'])->name('admin.class_subject_mappings.index');
    Route::get('/class-subject-mappings/create', [ClassSubjectMappingController::class, 'create'])->name('admin.class_subject_mappings.create');
    Route::get('/class-subject-mappings/{mapping}/edit', [ClassSubjectMappingController::class, 'edit'])->name('admin.class_subject_mappings.edit');
    Route::post('/class-subject-mappings', [ClassSubjectMappingController::class, 'store'])->name('admin.class_subject_mappings.store');
    Route::delete('/class-subject-mappings/{mapping}', [ClassSubjectMappingController::class, 'destroy'])->name('admin.class_subject_mappings.destroy');
    // });



    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/grading-systems', [GradingSystemController::class, 'index'])->name('admin.grading_systems.index');
    Route::get('/grading-systems/create', [GradingSystemController::class, 'create'])->name('admin.grading_systems.create');
    Route::get('/grading-systems/{gradingSystem}/edit', [GradingSystemController::class, 'edit'])->name('admin.grading_systems.edit');
    Route::post('/grading-systems', [GradingSystemController::class, 'store'])->name('admin.grading_systems.store');
    Route::delete('/grading-systems/{gradingSystem}', [GradingSystemController::class, 'destroy'])->name('admin.grading_systems.destroy');
    // });



    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/grade-levels', [GradeLevelController::class, 'index'])->name('admin.grade_levels.index');
    Route::get('/grade-levels/create', [GradeLevelController::class, 'create'])->name('admin.grade_levels.create');
    Route::get('/grade-levels/{gradeLevel}/edit', [GradeLevelController::class, 'edit'])->name('admin.grade_levels.edit');
    Route::post('/grade-levels', [GradeLevelController::class, 'store'])->name('admin.grade_levels.store');
    Route::delete('/grade-levels/{gradeLevel}', [GradeLevelController::class, 'destroy'])->name('admin.grade_levels.destroy');
    // });



    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/class-teachers', [ClassTeacherController::class, 'index'])->name('admin.class_teachers.index')->middleware('permission:View Semester');
    Route::get('/class-teachers/create', [ClassTeacherController::class, 'create'])->name('admin.class_teachers.create')->middleware('permission:Create Semester');
    Route::get('/class-teachers/{classTeacher}/edit', [ClassTeacherController::class, 'edit'])->name('admin.class_teachers.edit')->middleware('permission:Edit Semester');
    Route::post('/class-teachers', [ClassTeacherController::class, 'store'])->name('admin.class_teachers.store')->middleware('permission:Create Semester Teachers');
    Route::delete('/class-teachers/{classTeacher}', [ClassTeacherController::class, 'destroy'])->name('admin.class_teachers.destroy')->middleware('permission:Delete Semester');
    // });


    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/student-class-enrollments', [StudentClassEnrollmentController::class, 'index'])->name('admin.student_class_enrollments.index');
    Route::get('/student-class-enrollments/create', [StudentClassEnrollmentController::class, 'create'])->name('admin.student_class_enrollments.create');
    Route::get('/student-class-enrollments/{enrollment}/edit', [StudentClassEnrollmentController::class, 'edit'])->name('admin.student_class_enrollments.edit');
    Route::post('/student-class-enrollments', [StudentClassEnrollmentController::class, 'store'])->name('admin.student_class_enrollments.store');
    Route::delete('/student-class-enrollments/{enrollment}', [StudentClassEnrollmentController::class, 'destroy'])->name('admin.student_class_enrollments.destroy');
    // });
    Route::get('admin/student-class-enrollments/ajax-fee-structures', [StudentClassEnrollmentController::class, 'ajaxFeeStructures'])
        ->name('admin.student_class_enrollments.ajax_fee_structures');

    // Route::get('admin/promotions/', [PromotionController::class, 'index'])->name('admin.promotions.index');
    // Route::post('admin/promotions/classes', [PromotionController::class, 'getClasses'])->name('admin.promotions.classes');
    // Route::post('admin/promotions/sections', [PromotionController::class, 'getSections'])->name('admin.promotions.sections');
    // Route::post('admin/promotions/students', [PromotionController::class, 'getStudents'])->name('admin.promotions.students');
    // Route::post('admin/promotions/promote', [PromotionController::class, 'promote'])->name('admin.promotions.promote');
    // // Route::post('/admin/promotion/store', [PromotionController::class, 'store'])->name('admin.promotion.store');
    // Route::get('admin/promotions/recent', [PromotionController::class, 'recentPromotions'])->name('admin.promotions.recent');
    // Route::get('admin/promotions/student/{studentId}', [PromotionController::class, 'studentHistory'])->name('admin.promotions.history');
    // Route::middleware(['auth'])->name('admin.promotions.')->prefix('admin/promotions')->group(function () {
    //     Route::get('/', [PromotionController::class, 'index'])->name('index');
    //     Route::post('/classes', [PromotionController::class, 'getClasses'])->name('classes');
    //     Route::post('/sections', [PromotionController::class, 'getSections'])->name('sections');
    //     Route::post('/students', [PromotionController::class, 'getStudents'])->name('students');
    //     Route::post('/target-options', [PromotionController::class, 'getTargetOptions'])->name('target-options');
    //     Route::post('/promote', [PromotionController::class, 'promote'])->name('promote');
    //     Route::post('/target-classes', [PromotionController::class, 'getTargetClasses'])->name('target-classes');
    // });
    Route::prefix('admin/promotions')->name('admin.promotions.')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('index');

        Route::post('/courses', [PromotionController::class, 'getCourses'])->name('courses');
        Route::post('/streams', [PromotionController::class, 'getStreams'])->name('streams');
        Route::post('/classes', [PromotionController::class, 'getClasses'])->name('classes');
        Route::post('/sections', [PromotionController::class, 'getSections'])->name('sections');
        Route::post('/students', [PromotionController::class, 'getStudents'])->name('students');

        Route::post('/target-streams', [PromotionController::class, 'getTargetStreams'])->name('target-streams');
        Route::post('/target-classes', [PromotionController::class, 'getTargetClasses'])->name('target-classes');
        Route::post('/target-sections', [PromotionController::class, 'getTargetSections'])->name('target-sections');

        Route::post('/promote', [PromotionController::class, 'promote'])->name('promote');
    });
    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/attendance-periods', [AttendancePeriodController::class, 'index'])->name('admin.attendance_periods.index')->middleware('permission:View Attendance Periods');
    Route::get('/attendance-periods/create', [AttendancePeriodController::class, 'create'])->name('admin.attendance_periods.create')->middleware('permission:Create Attendance Periods');
    Route::get('/attendance-periods/{attendancePeriod}/edit', [AttendancePeriodController::class, 'edit'])->name('admin.attendance_periods.edit')->middleware('permission:Edit Attendance Periods');
    Route::post('/attendance-periods', [AttendancePeriodController::class, 'store'])->name('admin.attendance_periods.store')->middleware('permission:Create Attendance Periods');
    Route::delete('/attendance-periods/{attendancePeriod}', [AttendancePeriodController::class, 'destroy'])->name('admin.attendance_periods.destroy')->middleware('permission:Delete Attendance Periods');
    // });


    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/student-attendance/mark/{slot}/{date}', [StudentAttendanceController::class, 'markDirect'])->name('admin.student_attendance.mark_direct');
    Route::get('/student-attendance/merged/{schedule}/{date}', [StudentAttendanceController::class, 'markMergedDirect'])->name('admin.student_attendance.mark_merged_direct');
    Route::post('/student-attendance/merged/store', [StudentAttendanceController::class, 'storeMerged'])->name('admin.student_attendance.store_merged');
    Route::get('/student-attendance', [StudentAttendanceController::class, 'index'])->name('admin.student_attendance.index');
    Route::get('/student-attendance/create', [StudentAttendanceController::class, 'create'])->name('admin.student_attendance.create');
    Route::get('/student-attendance/{attendance}/edit', [StudentAttendanceController::class, 'edit'])->name('admin.student_attendance.edit');
    Route::post('/student-attendance', [StudentAttendanceController::class, 'store'])->name('admin.student_attendance.store');
    Route::delete('/student-attendance/{attendance}', [StudentAttendanceController::class, 'destroy'])->name('admin.student_attendance.destroy');
    Route::get('/student-attendance/date/export/excel', [StudentAttendanceController::class, 'exportExcel'])
        ->name('admin.student_attendance.date.export.excel');

    Route::get('/student-attendance/date/export/pdf', [StudentAttendanceController::class, 'exportPdf'])
        ->name('admin.student_attendance.date.export.pdf');

    Route::get('/admin/student-attendance/date', [StudentAttendanceController::class, 'attendanceByDate'])->name('admin.student_attendance.date');
    // Route::get('/admin/student-attendance/date/export/excel', [StudentAttendanceController::class, 'exportDateAttendanceExcel'])->name('admin.student_attendance.date.export.excel');
    // Route::get('/admin/student-attendance/date/export/pdf', [StudentAttendanceController::class, 'exportDateAttendancePdf'])->name('admin.student_attendance.date.export.pdf');
    // AJAX endpoints
    Route::post('/student-attendance/get-streams', [StudentAttendanceController::class, 'getStreams'])->name('admin.student_attendance.get_streams');
    Route::post('/student-attendance/get-classes', [StudentAttendanceController::class, 'getClasses'])->name('admin.student_attendance.get_classes');
    Route::post('/student-attendance/get-sections', [StudentAttendanceController::class, 'getSections'])->name('admin.student_attendance.get_sections');
    Route::post('/student-attendance/get-timetable', [StudentAttendanceController::class, 'getTimetableForDate'])->name('admin.student_attendance.get_timetable');
    // Route::post('/student-attendance/get-students', [StudentAttendanceController::class, 'getStudents'])->name('admin.student_attendance.get_students');
    Route::post('/attendance/students/{slot}', [StudentAttendanceController::class, 'getStudents'])->name('admin.student_attendance.get_students');

    Route::post('/attendance/slots', [StudentAttendanceController::class, 'getSlots'])->name('admin.student_attendance.get_slots');
    // });




    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/rooms', [RoomController::class, 'index'])->name('admin.rooms.index')->middleware('permission:view Room');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('admin.rooms.create')->middleware('permission:create Room');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('admin.rooms.edit')->middleware('permission:edit room');
    Route::post('/rooms', [RoomController::class, 'store'])->name('admin.rooms.store')->middleware('permission:create Branch');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('admin.rooms.destroy')->middleware('permission:Delete Room');
    // });



    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/timetables', [TimetableController::class, 'index'])->name('admin.timetables.index')->middleware('permission:view time table');
    Route::get('/timetables/create', [TimetableController::class, 'create'])->name('admin.timetables.create')->middleware('permission:create time table');
    Route::get('/timetables/{timetable}', [TimetableController::class, 'show'])->name('admin.timetables.show')->middleware('permission:view time table');
    Route::get('/timetables/{timetable}/edit', [TimetableController::class, 'edit'])->name('admin.timetables.edit')->middleware('permission:edit time table');
    Route::put('/timetables/{timetable}', [TimetableController::class, 'update'])->name('admin.timetables.update')->middleware('permission:edit time table');
    Route::post('/timetables', [TimetableController::class, 'store'])->name('admin.timetables.store')->middleware('permission:create time table');
    Route::delete('/timetables/{timetable}', [TimetableController::class, 'destroy'])->name('admin.timetables.destroy')->middleware('permission:Delete time table');

    // Route::prefix('admin')->group(function () {
    Route::get('/timetables/streams/{courseId}', [TimetableController::class, 'getStreams'])->name('admin.timetables.streams');
    Route::get('/timetables/classes/{courseId}/{streamId}', [TimetableController::class, 'getClasses'])->name('admin.timetables.classes');
    Route::get('/timetables/subjects/{courseId}/{streamId}/{classId}', [TimetableController::class, 'getSubjects'])->name('admin.timetables.subjects');
    Route::get('/timetables/sections/{courseId}/{streamId}/{classId}', [TimetableController::class, 'getSections'])->name('admin.timetables.sections');
    // });


    Route::get('/timetable-slots', [TimetableSlotController::class, 'index'])->name('admin.timetable_slots.index');
    Route::get('/timetable-slots/create', [TimetableSlotController::class, 'create'])->name('admin.timetable_slots.create');
    Route::get('/timetable-slots/{slot}/edit', [TimetableSlotController::class, 'edit'])->name('admin.timetable_slots.edit');
    Route::post('/timetable-slots', [TimetableSlotController::class, 'store'])->name('admin.timetable_slots.store');
    Route::delete('/timetable-slots/{slot}', [TimetableSlotController::class, 'destroy'])->name('admin.timetable_slots.destroy');
    // });
    Route::resource('merged-classes', MergedClassScheduleController::class);
    Route::get('/admin/timetables/classes/{streamId}', [MergedClassScheduleController::class, 'getClassesByStream']);
    Route::get('/admin/timetables/sections/{classId}', [MergedClassScheduleController::class, 'getSectionsByClass']);



    //vehicle 
    Route::get('/vehicle', [VehicleController::class, 'index'])->name('admin.vehicle.index')->middleware('permission:View Vehicles');
    Route::get('/vehicle/create', [VehicleController::class, 'create'])->name('admin.vehicle.create')->middleware('permission:Create Vehicles');
    Route::post('/vehicle', [VehicleController::class, 'store'])->name('admin.vehicle.store')->middleware('permission:Create Vehicles');
    Route::get('/vehicle/{vehicle}/edit', [VehicleController::class, 'edit'])->name('admin.vehicle.edit')->middleware('permission:Edit Vehicles');
    Route::put('/vehicle/{vehicle}', [VehicleController::class, 'update'])->name('admin.vehicle.update')->middleware('permission:Edit Vehicles');
    Route::delete('/vehicle/{vehicle}', [VehicleController::class, 'destroy'])->name('admin.vehicle.destroy')->middleware('permission:Delete Vehicles');

    //driver
    Route::get('/driver', [DriverController::class, 'index'])->name('admin.driver.index')->middleware('permission:View Driver');
    Route::get('/driver/create', [DriverController::class, 'create'])->name('admin.driver.create')->middleware('permission:Create Driver');
    Route::post('/driver', [DriverController::class, 'store'])->name('admin.driver.store')->middleware('permission:Create Driver');
    Route::get('/driver/{driver}/edit', [DriverController::class, 'edit'])->name('admin.driver.edit')->middleware('permission:Edit Driver');
    Route::put('/driver/{driver}', [DriverController::class, 'update'])->name('admin.driver.update')->middleware('permission:Edit Driver');
    Route::delete('/driver/{driver}', [DriverController::class, 'destroy'])->name('admin.driver.destroy')->middleware('permission:Delete Driver');

    //routes
    Route::get('/route', [RoutesController::class, 'index'])->name('admin.routes.index')->middleware('permission:View Routes');
    Route::get('/routes/create', [RoutesController::class, 'create'])->name('admin.routes.create')->middleware('permission:Create Routes');
    Route::post('/route', [RoutesController::class, 'store'])->name('admin.routes.store')->middleware('permission:Create Routes');
    Route::get('/routes/{routes}/edit', [RoutesController::class, 'edit'])->name('admin.routes.edit')->middleware('permission:Edit Routes');
    Route::get('/routes/{id}/show', [RoutesController::class, 'show'])->name('admin.routes.show')->middleware('permission:View Routes');
    Route::get('/get-buses-by-stop/{stop_id}', [StudentRouteMappingController::class, 'getBusesByStop'])->name('get.buses.by.stop')->middleware('permission:View Student Route Mappings');
    Route::put('/routes/{routes}', [RoutesController::class, 'update'])->name('admin.routes.update')->middleware('permission:Edit Routes');
    Route::delete('/routes/{routes}', [RoutesController::class, 'destroy'])->name('admin.routes.destroy')->middleware('permission:Delete Routes');


    //stoppage
    Route::get('/routestops', [RouteStopsController::class, 'index'])->name('admin.routestops.index')->middleware('permission:View Route Stops');
    Route::get('/routestops/create', [RouteStopsController::class, 'create'])->name('admin.routestops.create')->middleware('permission:Create Route Stops');
    Route::post('/routestops', [RouteStopsController::class, 'store'])->name('admin.routestops.store')->middleware('permission:Create Route Stops');
    Route::get('/routestops/{routestops}/edit', [RouteStopsController::class, 'edit'])->name('admin.routestops.edit')->middleware('permission:Edit Route Stops');
    Route::put('/routestops/{routestops}', [RouteStopsController::class, 'update'])->name('admin.routestops.update')->middleware('permission:Edit Route Stops');
    Route::delete('/routestops/{routestops}', [RouteStopsController::class, 'destroy'])->name('admin.routestops.destroy')->middleware('permission:Delete Route Stops');

    //Student routemap
    Route::get('/studentroutemapping', [StudentRouteMappingController::class, 'index'])->name('admin.studentroutemapping.index')->middleware('permission:View Student Route Mapping');
    Route::get('/studentroutemapping/create', [StudentRouteMappingController::class, 'create'])->name('admin.studentroutemapping.create')->middleware('permission:Create Student Route Mapping');
    Route::get('/studentroutemapping/{id}/show', [StudentRouteMappingController::class, 'show'])->name('admin.studentroutemapping.show')->middleware('permission:View Student Route Mapping');
    Route::post('/studentroutemapping', [StudentRouteMappingController::class, 'store'])->name('admin.studentroutemapping.store')->middleware('permission:Create Student Route Mapping');
    Route::get('/studentroutemapping/{studentroutemapping}/edit', [StudentRouteMappingController::class, 'edit'])->name('admin.studentroutemapping.edit')->middleware('permission:Edit Student Route Mapping');
    Route::put('/studentroutemapping/{studentroutemapping}', [StudentRouteMappingController::class, 'update'])->name('admin.studentroutemapping.update')->middleware('permission:Edit Student Route Mapping');
    Route::delete('/studentroutemapping/{studentroutemapping}', [StudentRouteMappingController::class, 'destroy'])->name('admin.studentroutemapping.destroy')->middleware('permission:Delete Student Route Mapping');
    Route::get('/get-stops/{route_id}', [StudentRouteMappingController::class, 'getStops'])->middleware('permission:View Student Route Mapping');
    Route::get('/find-student-by-reg', [StudentRouteMappingController::class, 'findStudentByRegNumber'])->name('find.student.by.reg')->middleware('permission:View Student Route Mapping');



    // Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->group(function () {
    Route::get('/hostel', [HostelController::class, 'index'])->name('admin.hostel.index')->middleware('permission:view hostell');
    Route::get('/hostel/create', [HostelController::class, 'create'])->name('admin.hostel.create')->middleware('permission:Create Hostel');
    Route::get('/hostel/{hostel}/edit', [HostelController::class, 'edit'])->name('admin.hostel.edit')->middleware('permission:Edit Hostel');
    Route::post('/hostel', [HostelController::class, 'store'])->name('admin.hostel.store')->middleware('permission:Create Hostel');
    Route::delete('/hostel/{hostel}', [HostelController::class, 'destroy'])->name('admin.hostel.destroy')->middleware('permission:Delete Hostel');
    Route::get('/hostel/{id}', [HostelController::class, 'show'])->name('admin.hostel.show')->middleware('permission:view hostel');

    // });
    // Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->group(function () {
    Route::get('/hostelroom', [HostelroomController::class, 'index'])->name('admin.hostelroom.index')->middleware('permission:View Hostel Room');
    Route::get('/hostelroom/create', [HostelroomController::class, 'create'])->name('admin.hostelroom.create')->middleware('permission:Create Hostel Room');
    Route::get('/hostelroom/{hostelroom}/edit', [HostelroomController::class, 'edit'])->name('admin.hostelroom.edit')->middleware('permission:Edit Hostel Room');
    Route::post('/hostelroom', [HostelroomController::class, 'store'])->name('admin.hostelroom.store')->middleware('permission:Create Hostel Room');
    Route::put('/hostelroom/{hostelroom}', [HostelroomController::class, 'update'])->name('admin.hostelroom.update')->middleware('permission:edit_hostel_rooms'); // Add this line
    Route::delete('/hostelroom/{hostelroom}', [HostelroomController::class, 'destroy'])->name('admin.hostelroom.destroy')->middleware('permission:Delete Hostel Room');

    // });
    Route::get('/hostelbed', [HostelbedController::class, 'index'])->name('admin.hostelbed.index')->middleware('permission:View Hostel Bed');
    Route::get('/hostelbed/create', [HostelbedController::class, 'create'])->name('admin.hostelbed.create')->middleware('permission:Create Hostel Bed');
    Route::get('/hostelbed/{hostelbed}/edit', [HostelbedController::class, 'edit'])->name('admin.hostelbed.edit')->middleware('permission:Edit Hostel Bed');
    Route::post('/hostelbed', [HostelbedController::class, 'store'])->name('admin.hostelbed.store')->middleware('permission:Create Hostel Bed');
    Route::delete('/hostelbed/{hostelbed}', [HostelbedController::class, 'destroy'])->name('admin.hostelbed.destroy')->middleware('permission:Delete Hostel Bed');
    Route::get('/admin/hostels/{hostel}/rooms', [HostelbedController::class, 'getRoomsByHostel'])->name('admin.hostels.rooms');

    //hostel allocation

Route::get('/hostelallocation', [HostelAllocationController::class, 'index'])->name('admin.hostelallocation.index')->middleware('permission:View Hostel Allocation');
Route::get('/hostelallocation/create', [Hostelallocationcontroller::class, 'create'])->name('admin.hostelallocation.create')->middleware('permission:Create Hostel Allocation');
Route::get('/hostelallocation/{hostelallocation}/edit', [Hostelallocationcontroller::class, 'edit'])->name('admin.hostelallocation.edit')->middleware('permission:Edit Hostel Allocation');
Route::post('/hostelallocation', [Hostelallocationcontroller::class, 'store'])->name('admin.hostelallocation.store')->middleware('permission:Create Hostel Allocation');
Route::delete('/hostelallocation/{hostelallocation}', [Hostelallocationcontroller::class, 'destroy'])->name('admin.hostelallocation.destroy')->middleware('permission:Delete Hostel Allocation');
Route::get('admin/hostelallocation/student/{student}', 
    [HostelAllocationController::class, 'getStudentAllocation']
)->name('admin.hostelallocation.student');
// In web.php or routes file
Route::get('/admin/hostelallocation/rooms/{hostel}', [HostelAllocationController::class, 'getRoomsByHostel'])->name('admin.hostelallocation.rooms');
Route::get('/admin/hostelallocation/beds/{room}', [HostelAllocationController::class, 'getBedsByRoom'])->name('admin.hostelallocation.beds');
/* Hostel Fees Routes */

    Route::get('/hostelfees', [HostelFeeController::class, 'index'])->name('admin.hostelfees.index');
    Route::get('/hostelfees/create', [HostelFeeController::class, 'create'])->name('admin.hostelfees.create');
    Route::post('/hostelfees', [HostelFeeController::class, 'store'])->name('admin.hostelfees.store');
    Route::get('/hostelfees/{hostelfees}/edit', [HostelFeeController::class, 'edit'])->name('admin.hostelfees.edit');
    Route::delete('/hostelfees/{hostelfee}', [HostelFeeController::class, 'destroy'])->name('admin.hostelfees.destroy');

    // books admin.book_table
    Route::get('/books', [BooksController::class, 'index'])->name('admin.books.index')->middleware('permission:View Book');
    Route::get('/books/create', [BooksController::class, 'create'])->name('admin.books.create')->middleware('permission:Create Book');
    Route::get('/books/{book}/edit', [BooksController::class, 'edit'])->name('admin.books.edit')->middleware('permission:Edit Book');
    Route::post('/books/store', [BooksController::class, 'store'])->name('admin.books.store')->middleware('permission:Create Book');
    Route::delete('/books/{book}', [BooksController::class, 'destroy'])->name('admin.books.destroy')->middleware('permission:Delete Book');
    Route::get('/books/scan', [BooksController::class, 'scanPage'])->name('admin.books.scan')->middleware('permission:Create Book');
    Route::post('/books/scan-barcode', [BooksController::class, 'scanBarcode'])->name('admin.books.scan-barcode');
    Route::get('/books/{bookKey}', [BooksController::class, 'show'])->where('bookKey', '.+')->name('admin.books.show');
    // Route::get('/books/{isbn}', [BooksController::class, 'show'])->name('admin.books.show');
    // Route::get('/book/buy', [BooksController::class, 'buyBook'])->name('admin.books.buy');
    // Route::post('/book/stockStore', [BooksController::class, 'stockStore'])->name('admin.library.stock.store');
    Route::get('/admin/library/stock/create',  [BooksController::class, 'buyBook'])->name('admin.library.stock.create')->middleware('permission:Create Library Stock Entry');
    Route::post('/admin/library/stock/store',  [BooksController::class, 'stockStore'])->name('admin.library.stock.store')->middleware('permission:Create Library Stock Entry');

    Route::get('/library/stock',          [StockEntryController::class, 'index'])->name('admin.library.stock.index')->middleware('permission:View Library Stock');
    Route::get('/library/stock/create',   [BooksController::class,      'buyBook'])->name('admin.library.stock.create')->middleware('permission:Create Library Stock Entry');
    Route::post('/library/stock',         [BooksController::class,      'stockStore'])->name('admin.library.stock.store')->middleware('permission:Create Library Stock Entry');
    Route::get('/library/stock/{orderId}', [StockEntryController::class, 'show'])->name('admin.library.stock.show')->middleware('permission:View Library Stock');
    Route::get('/library/stock/{orderId}/edit',   [StockEntryController::class, 'edit'])->name('admin.library.stock.edit')->middleware('permission:Edit Library Stock Entry');
    Route::put('/library/stock/{orderId}',        [StockEntryController::class, 'update'])->name('admin.library.stock.update')->middleware('permission:Edit Library Stock Entry');
    Route::delete('/library/stock/{orderId}',        [StockEntryController::class, 'destroy'])->name('admin.library.stock.destroy')->middleware('permission:Delete Library Stock Entry');

    Route::get('books/excel/import',            [BooksController::class, 'importPage'])->name('admin.books.import');
    Route::post('books/import',           [BooksController::class, 'importBooks'])->name('admin.books.import.post');
    Route::get('books/template/download', [BooksController::class, 'downloadTemplate'])->name('admin.books.template');
    Route::get('/books/{bookKey}', [BooksController::class, 'show'])->name('admin.books.show')->where('bookKey', '.+');

    Route::get('/library_cards', [LibraryCardsController::class, 'index'])->name('admin.library_cards.index')->middleware('permission:View Library Card');
    Route::get('/library_cards/create', [LibraryCardsController::class, 'create'])->name('admin.library_cards.create')->middleware('permission:Create Library Card');
    Route::get('/library_cards/{library_card}/edit', [LibraryCardsController::class, 'edit'])->name('admin.library_cards.edit')->middleware('permission:Edit Library Card');
    Route::post('/library_cards/store', [LibraryCardsController::class, 'store'])->name('admin.library_cards.store')->middleware('permission:Create Library Card');
    Route::delete('/library_cards/{library_card}', [LibraryCardsController::class, 'destroy'])->name('admin.library_cards.destroy')->middleware('permission:Delete Library Card');


    // Route::put('/library_cards/{library_card}', [LibraryCardsController::class, 'update'])->name('admin.library_cards.update'); 

    // book_issues admin.book_issues
    Route::get('/book_issues', [BookIssuesController::class, 'index'])->name('admin.book_issues.index')->middleware('permission:View Book Issues');
    Route::get('/book_issues/create', [BookIssuesController::class, 'create'])->name('admin.book_issues.create')->middleware('permission:Create Book Issues');
    Route::post('/book_issues/store', [BookIssuesController::class, 'store'])->name('admin.book_issues.store')->middleware('permission:Create Book Issues');
    Route::get('/book_issues/{id}/edit', [BookIssuesController::class, 'edit'])->name('admin.book_issues.edit')->middleware('permission:Edit Book Issues');
    Route::put('/book_issues/{id}', [BookIssuesController::class, 'update'])->name('admin.book_issues.update')->middleware('permission:Edit Book Issues');
    Route::delete('/book_issues/{id}', [BookIssuesController::class, 'destroy'])->name('admin.book_issues.destroy')->middleware('permission:Delete Book Issues');


    Route::get('/academic_groups', [AcademicGroupsController::class, 'index'])->name('admin.academic_groups.index')->middleware('permission:View Academic Group');
    Route::get('/academic_groups/create', [AcademicGroupsController::class, 'create'])->name('admin.academic_groups.create')->middleware('permission:Create Academic Group');
    Route::post('/academic_groups/store', [AcademicGroupsController::class, 'store'])->name('admin.academic_groups.store')->middleware('permission:Create Academic Group');
    Route::get('/academic_groups/{id}/edit', [AcademicGroupsController::class, 'edit'])->name('admin.academic_groups.edit')->middleware('permission:Edit Academic Group');
    Route::put('/academic_groups/{id}', [AcademicGroupsController::class, 'update'])->name('admin.academic_groups.update')->middleware('permission:Edit Academic Group');
    Route::delete('/academic_groups/{id}', [AcademicGroupsController::class, 'destroy'])->name('admin.academic_groups.destroy')->middleware('permission:Delete Academic Group');



    Route::get('/admin/books/scan', [BooksController::class, 'scanPage'])->name('admin.books.scan');
    Route::post('/admin/books/scan-barcode', [BooksController::class, 'scanBarcode'])->name('admin.books.scan-barcode');

    Route::get('/admin/issues/search-student', [BookIssuesController::class, 'searchStudent'])->name('admin.book_issues.search_student');
    Route::get('/admin/issues/search-books', [BookIssuesController::class, 'searchBooks'])->name('admin.book_issues.search_books');

    Route::get('/admin/book_issues/return', [BookIssuesController::class, 'returnIndex'])->name('admin.book_issues.return');
    Route::get('/admin/book_issues/return/search_student', [BookIssuesController::class, 'returnSearchStudent'])->name('admin.book_issues.return.search_student');
    Route::get('/admin/book_issues/return/search_books', [BookIssuesController::class, 'returnSearchBooks'])->name('admin.book_issues.return.search_books');
    Route::post('/admin/book_issues/return/store', [BookIssuesController::class, 'returnStore'])->name('admin.book_issues.return.store');


    Route::get('/library_master', [LibraryMasterController::class, 'index'])->name('admin.library_master.index')->middleware('permission:View Master Data');
    Route::get('/library_master/create', [LibraryMasterController::class, 'create'])->name('admin.library_master.create')->middleware('permission:Create Master Data');
    Route::post('/library_master/store', [LibraryMasterController::class, 'store'])->name('admin.library_master.store')->middleware('permission:Create Master Data');
    Route::get('/library_master/{id}/edit', [LibraryMasterController::class, 'edit'])->name('admin.library_master.edit')->middleware('permission:Edit Master Data');
    Route::put('/library_master/{id}', [LibraryMasterController::class, 'update'])->name('admin.library_master.update')->middleware('permission:Edit Master Data');
    Route::delete('/library_master/{id}', [LibraryMasterController::class, 'destroy'])->name('admin.library_master.destroy')->middleware('permission:Delete Master Data');

    Route::get('/admin/library-master/parents', [LibraryMasterController::class, 'getParents'])
        ->name('admin.library_master.parents');

    Route::get('admin/books/rows-by-room', [LibraryMasterController::class, 'getRowsByRoom'])
        ->name('admin.books.rows_by_room');

    Route::get('admin/books/shelves-by-row', [LibraryMasterController::class, 'getShelvesByRow'])
        ->name('admin.books.shelves_by_row');


    Route::get('/holidays', [HolidaysController::class, 'index'])->name('admin.holidays.index')->middleware('permission:View Holidays');
    Route::get('/holidays/create', [HolidaysController::class, 'create'])->name('admin.holidays.create')->middleware('permission:Create Holidays');
    Route::post('/holidays/store', [HolidaysController::class, 'store'])->name('admin.holidays.store')->middleware('permission:Create Holidays');
    Route::get('/holidays/{id}/edit', [HolidaysController::class, 'edit'])->name('admin.holidays.edit')->middleware('permission:Edit Holidays');
    Route::put('/holidays/{id}', [HolidaysController::class, 'update'])->name('admin.holidays.update')->middleware('permission:Edit Holidays');
    Route::delete('/holidays/{id}', [HolidaysController::class, 'destroy'])->name('admin.holidays.destroy')->middleware('permission:Delete Holidays');



    // book_fines admin.book_fines
    Route::get('/book_fines', [BookFinesController::class, 'index'])->name('admin.book_fines.index')->middleware('permission:View Book Fines');
    Route::get('/book_fines/create', [BookFinesController::class, 'create'])->name('admin.book_fines.create')->middleware('permission:Create Book Fines');
    Route::post('/book_fines/store', [BookFinesController::class, 'store'])->name('admin.book_fines.store')->middleware('permission:Create Book Fines');
    Route::get('/book_fines/{book_fine}/edit', [BookFinesController::class, 'edit'])->name('admin.book_fines.edit')->middleware('permission:Edit Book Fines');
    Route::put('/book_fines/{book_fine}', [BookFinesController::class, 'update'])->name('admin.book_fines.update')->middleware('permission:Edit Book Fines');
    Route::delete('/book_fines/{book_fine}', [BookFinesController::class, 'destroy'])->name('admin.book_fines.destroy')->middleware('permission:Delete Book Fines');
    Route::put('/book_fines/{book_fine}', [BookFinesController::class, 'update'])->name('admin.book_fines.update');




    // Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/exam-types', [ExamTypeController::class, 'index'])->name('admin.exam_types.index');
    Route::get('/exam-types/create', [ExamTypeController::class, 'create'])->name('admin.exam_types.create');
    Route::post('/exam-types', [ExamTypeController::class, 'store'])->name('admin.exam_types.store');
    Route::get('/exam-types/{examType}/edit', [ExamTypeController::class, 'edit'])->name('admin.exam_types.edit');
    Route::delete('/exam-types/{examType}', [ExamTypeController::class, 'destroy'])->name('admin.exam_types.destroy');
    // });



    Route::get('/exam', [ExamController::class, 'index'])->name('admin.exams.index')->middleware('permission:View Exam list');
    Route::get('/exam/create', [ExamController::class, 'create'])->name('admin.exams.create')->middleware('permission:Create Exam list');
    Route::post('/exam', [ExamController::class, 'store'])->name('admin.exams.store')->middleware('permission:Create Exam list');
    Route::get('/exam/{exam}/edit', [ExamController::class, 'edit'])->name('admin.exams.edit')->middleware('permission:Edit Exam list');
    Route::put('/exam/{exam}', [ExamController::class, 'update'])->name('admin.exams.update')->middleware('permission:Edit Exam list');
    Route::delete('/exam/{exam}', [ExamController::class, 'destroy'])->name('admin.exams.destroy')->middleware('permission:Delete Exam list');
    Route::get('/exams/{id}', [ExamController::class, 'show'])->name('admin.exams.show');
    Route::get('exams/{id}/marks',  [ExamController::class, 'marks'])->name('admin.exams.marks');
    Route::post('exam-marks/save',  [ExamController::class, 'saveMarks'])->name('admin.exam-marks.save');


    // Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/exam-marks', [ExamMarkController::class, 'index'])->name('admin.exam_marks.index');
    // Route::get('/exam-marks/create', [ExamMarkController::class, 'create'])->name('admin.exam_marks.create');
    // Route::post('/exam-marks', [ExamMarkController::class, 'store'])->name('admin.exam_marks.store');
    // Route::get('/exam-marks/{examMark}/edit', [ExamMarkController::class, 'edit'])->name('admin.exam_marks.edit');
    // Route::put('/exam-marks/{examMark}', [ExamMarkController::class, 'update'])->name('admin.exam_marks.update');
    // Route::delete('/exam-marks/{examMark}', [ExamMarkController::class, 'destroy'])->name('admin.exam_marks.destroy');
    // });

    // Specific routes PEHLE (create se bhi pehle)
    Route::get('/exam-marks/students-by-exam', [ExamMarkController::class, 'getStudentsByExam'])->name('admin.exam_marks.students_by_exam');
    Route::get('/exam-marks/import',           [ExamMarkController::class, 'import'])->name('admin.exam_marks.import');
    Route::post('/exam-marks/import',          [ExamMarkController::class, 'importStore'])->name('admin.exam_marks.import.store');
    Route::get('/exam-marks/export/pdf',       [ExamMarkController::class, 'exportPdf'])->name('admin.exam_marks.export.pdf');

    Route::get('/exam-marks',                  [ExamMarkController::class, 'index'])->name('admin.exam_marks.index');
    Route::get('/exam-marks/create',           [ExamMarkController::class, 'create'])->name('admin.exam_marks.create');
    Route::post('/exam-marks',                 [ExamMarkController::class, 'store'])->name('admin.exam_marks.store');
    Route::get('/exam-marks/{examMark}/edit',  [ExamMarkController::class, 'edit'])->name('admin.exam_marks.edit');
    Route::put('/exam-marks/{examMark}',       [ExamMarkController::class, 'update'])->name('admin.exam_marks.update');
    Route::delete('/exam-marks/{examMark}',    [ExamMarkController::class, 'destroy'])->name('admin.exam_marks.destroy');

    Route::get('/ajax/exams-by-filters', [ExamMarkController::class, 'getExamsByFilters'])->name('admin.exam_marks.exams_by_filters');
    Route::get('/ajax/students-by-exam',  [ExamMarkController::class, 'getStudentsByExam'])->name('admin.exam_marks.students_by_exam');

//Question papers
Route::get('/question-papers',                      [QuestionBankController::class, 'index'])->name('admin.question_banks.index');
Route::get('/question-papers/create',               [QuestionBankController::class, 'create'])->name('admin.question_banks.create');
Route::post('/question-papers',                     [QuestionBankController::class, 'store'])->name('admin.question_banks.store');
Route::get('/question-papers/{questionPaper}/edit', [QuestionBankController::class, 'edit'])->name('admin.question_banks.edit');
Route::put('/question-papers/{questionPaper}',      [QuestionBankController::class, 'update'])->name('admin.question_banks.update');
Route::delete('/question-papers/{questionPaper}',   [QuestionBankController::class, 'destroy'])->name('admin.question_banks.destroy');

// ✅ Question Paper Types (Next step after Question Bank)
// Route::get('/question-paper-types/create', [QuestionPaperTypeController::class, 'create'])->name('admin.question_paper_types.create');
// Route::post('/question-paper-types/store', [QuestionPaperTypeController::class, 'store'])->name('admin.question_paper_types.store');

// // ✅ YE RAKHO
// Route::get('/question-paper-types',           [QuestionPaperTypeController::class, 'index'])->name('admin.question_paper_types.index');
// Route::get('/question-paper-types/create',    [QuestionPaperTypeController::class, 'create'])->name('admin.question_paper_types.create');
// Route::post('/question-paper-types',          [QuestionPaperTypeController::class, 'store'])->name('admin.question_paper_types.store');
// Route::get('/question-paper-types/{id}/edit', [QuestionPaperTypeController::class, 'edit'])->name('admin.question_paper_types.edit');
// Route::delete('/question-paper-types/{id}',   [QuestionPaperTypeController::class, 'destroy'])->name('admin.question_paper_types.destroy');
// // Add this route inside your admin routes group
// Route::get('/admin/question-paper-types/questions', [App\Http\Controllers\Admin\QuestionPaperTypeController::class, 'getQuestions'])
//     ->name('admin.question_paper_types.questions');
// Route::post('admin/question-paper-types/{id}/toggle-status', 
//     [App\Http\Controllers\Admin\QuestionPaperTypeController::class, 'toggleStatus']
// )->name('admin.question_paper_types.toggle_status');
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/question-paper-types',              [QuestionPaperTypeController::class, 'index'])->name('admin.question_paper_types.index');
    Route::get('/question-paper-types/create',       [QuestionPaperTypeController::class, 'create'])->name('admin.question_paper_types.create');
    Route::post('/question-paper-types',             [QuestionPaperTypeController::class, 'store'])->name('admin.question_paper_types.store');
    Route::get('/question-paper-types/{id}/edit',    [QuestionPaperTypeController::class, 'edit'])->name('admin.question_paper_types.edit');
    Route::delete('/question-paper-types/{id}',      [QuestionPaperTypeController::class, 'destroy'])->name('admin.question_paper_types.destroy');
    Route::get('/question-paper-types/questions',    [QuestionPaperTypeController::class, 'getQuestions'])->name('admin.question_paper_types.questions');
    Route::post('/question-paper-types/{id}/toggle-status', [QuestionPaperTypeController::class, 'toggleStatus'])->name('admin.question_paper_types.toggle_status');

});

    // Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/fee-categories', [FeeCategoryController::class, 'index'])->name('admin.fee_categories.index')->middleware('permission:View Fee Categories');
    Route::get('/fee-categories/create', [FeeCategoryController::class, 'create'])->name('admin.fee_categories.create')->middleware('permission:Create Fee Categories');
    Route::post('/fee-categories', [FeeCategoryController::class, 'store'])->name('admin.fee_categories.store')->middleware('permission:Create Fee Categories');
    Route::get('/fee-categories/{feeCategory}/edit', [FeeCategoryController::class, 'edit'])->name('admin.fee_categories.edit')->middleware('permission:Edit Fee Categories');
    Route::put('/fee-categories/{feeCategory}', [FeeCategoryController::class, 'update'])->name('admin.fee_categories.update')->middleware('permission:Edit Fee Categories');
    Route::delete('/fee-categories/{feeCategory}', [FeeCategoryController::class, 'destroy'])->name('admin.fee_categories.destroy')->middleware('permission:Delete Fee Categories');
    // });

    // Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/fee-structures', [FeeStructureController::class, 'index'])->name('admin.fee_structures.index')->middleware('permission:View Fee Structure');
    Route::get('/fee-structures/create', [FeeStructureController::class, 'create'])->name('admin.fee_structures.create')->middleware('permission:Create Fee Structure');
    Route::post('/fee-structures', [FeeStructureController::class, 'store'])->name('admin.fee_structures.store')->middleware('permission:Create Fee Structure');

    // Wizard steps
    Route::get('/fee-structures/{feeStructure}/items', function ($id) {
        return app(FeeStructureController::class)->edit($id);
    })->name('admin.fee_structures.wizard.items');

    Route::get('/fee-structures/{feeStructure}/edit', [FeeStructureController::class, 'edit'])->name('admin.fee_structures.edit');
    Route::put('/fee-structures/{feeStructure}', [FeeStructureController::class, 'update'])->name('admin.fee_structures.update');
    Route::delete('/fee-structures/{feeStructure}', [FeeStructureController::class, 'destroy'])->name('admin.fee_structures.destroy');
    Route::get('/fee-structures/{feeStructure}/items', [FeeStructureController::class, 'wizardItems'])->name('admin.fee_structures.wizard.items');

    // });

    Route::get('/fee-installments', [FeeInstallmentController::class, 'index'])->name('admin.fee_installments.index')->middleware('permission:View Fee Installments');
    Route::get('/fee-installments/create', [FeeInstallmentController::class, 'create'])->name('admin.fee_installments.create')->middleware('permission:Create Fee Installments');
    Route::post('/fee-installments', [FeeInstallmentController::class, 'store'])->name('admin.fee_installments.store')->middleware('permission:Create Fee Installments');
    Route::get('/fee-installments/{feeInstallment}/edit', [FeeInstallmentController::class, 'edit'])->name('admin.fee_installments.edit')->middleware('permission:Edit Fee Installments');
    Route::put('/fee-installments/{feeInstallment}', [FeeInstallmentController::class, 'update'])->name('admin.fee_installments.update')->middleware('permission:Edit Fee Installments');
    Route::delete('/fee-installments/{feeInstallment}', [FeeInstallmentController::class, 'destroy'])->name('admin.fee_installments.destroy')->middleware('permission:Delete Fee Installments');

    // routes/web.php

    // Route::prefix('admin')->middleware('auth')->group(function () {
    //     Route::resource('fee-payments', FeePaymentController::class);
    // });

    Route::get('/collect-fees', [FeePaymentController::class, 'collectForm'])->name('admin.collect-fees');
    Route::post('/collect-fees', [FeePaymentController::class, 'collectStore']);

    Route::get('/fee-payments', [FeePaymentController::class, 'index'])->name('admin.fee-payments.index')->middleware('permission:View Fee Payment');

    Route::get('/payments/collect', [FeePaymentController::class, 'collect'])
        ->name('admin.payments.collect')->middleware('permission:Create Fee Payment');

    Route::post('/payments/collect', [FeePaymentController::class, 'storeCollect'])
        ->name('admin.payments.collect.store')->middleware('permission:Create Fee Payment');
});
Route::get('admin/fee-payments/{feePayment}', [FeePaymentController::class, 'show'])
    ->name('admin.fee_payments.show')->middleware('permission:View Fee Payment');

Route::get('admin/fee-payments/{feePayment}/receipt', [FeePaymentController::class, 'receipt'])
    ->name('admin.fee_payments.receipt');

// Route::prefix('admin')->middleware('auth')->group(function () {
// 1. Assign Fee Structure to Student
Route::get('/student-fee-plans/assign', [StudentFeePlanController::class, 'assignForm'])->name('admin.student-fee-plans.assign');
Route::post('/student-fee-plans/assign', [StudentFeePlanController::class, 'assignStore']);

// 2. Generate Fee Schedule
Route::get('/student-fee-plans/generate', [StudentFeePlanController::class, 'generateForm'])->name('admin.student-fee-plans.generate');
Route::post('/student-fee-plans/generate', [StudentFeePlanController::class, 'generateStore']);

Route::get('/fee-structures/{feeStructure}/generate', [StudentFeePlanController::class, 'generate'])->name('admin.fee_structures.generate');
Route::post('/fee-structures/{feeStructure}/generate', [FeeStructureController::class, 'generateStore'])->name('admin.fee_structures.generate.store');

// Student Ledger / Account Summary
Route::get('/student-ledger', [StudentAccountController::class, 'index'])->name('admin.student-ledger.index')->middleware('permission:View Student Ledger');
Route::post('/student-fee-assignments/{id}/generate', [FeeStructureController::class, 'individualGenerate'])->name('admin.student-fee-assignments.generate');

// });



// Index
Route::get('/student-fee-structures', [StudentFeePlanController::class, 'index'])
    ->name('admin.student_fee_structures.index');

// Create / Edit Assignment (same page)
Route::get('/student-fee-structures/create', [StudentFeePlanController::class, 'create'])
    ->name('admin.student_fee_structures.create');

// Edit‑assignment redirect (opens create page in edit mode)
Route::get('/student-fee-structures/plan/{id}/edit', [StudentFeePlanController::class, 'editAssignment'])
    ->name('admin.student_fee_structures.edit_assignment');
Route::delete('/student-fee-plans/{studentFeePlan}', [StudentFeePlanController::class, 'destroy'])->name('admin.student_fee_structures.destroy');

// AJAX for Edit (with fee_structure_id + plan_id)
Route::get('/ajax/enrolled-students', [StudentFeePlanController::class, 'ajaxEnrolledStudents'])
    ->name('admin.ajax.enrolled_students');

// AJAX for Add (only stream + academic_year)
Route::get('/ajax/enrolled-students-add', [StudentFeePlanController::class, 'ajaxEnrolledStudentsAdd'])
    ->name('admin.ajax.enrolled_students_add');

// Store / Update assignment
Route::post('/student-fee-structures/assign', [StudentFeePlanController::class, 'assign'])
    ->name('admin.student_fee_structures.assign.store');


// Extra fee page
Route::get('/extra-fee', [StudentFeePlanController::class, 'createExtraFee'])
    ->name('admin.extra_fee.create');

// Store extra fee
Route::post('/extra-fee', [StudentFeePlanController::class, 'storeExtraFee'])
    ->name('admin.extra_fee.store');
Route::get('/ajax/students-by-branch-year', [StudentFeePlanController::class, 'studentsByBranchYear'])
    ->name('admin.ajax.students_by_branch_year');



Route::prefix('income-heads')->name('income-heads.')->group(function () {
    Route::get('/', [IncomeHeadController::class, 'index'])->name('index')->middleware('permission:View Income Heads');
    Route::get('/create', [IncomeHeadController::class, 'create'])->name('create')->middleware('permission:Create Income Heads');
    Route::get('/{id}/edit', [IncomeHeadController::class, 'edit'])->name('edit')->middleware('permission:Edit Income Heads');
    Route::post('/', [IncomeHeadController::class, 'store'])->name('store')->middleware('permission:Create Income Heads');
    Route::delete('/{id}', [IncomeHeadController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Income Heads');
});

Route::prefix('expense-heads')->name('expense-heads.')->group(function () {
    Route::get('/', [ExpenseHeadController::class, 'index'])->name('index')->middleware('permission:View Expense Heads');
    Route::get('/create', [ExpenseHeadController::class, 'create'])->name('create')->middleware('permission:Create Expense Heads');
    Route::get('/{id}/edit', [ExpenseHeadController::class, 'edit'])->name('edit')->middleware('permission:Edit Expense Heads');
    Route::post('/', [ExpenseHeadController::class, 'store'])->name('store')->middleware('permission:Create Expense Heads');
    Route::delete('/{id}', [ExpenseHeadController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Expense Heads');
});

Route::prefix('income-records')->name('income-records.')->group(function () {
    Route::get('/', [IncomeRecordController::class, 'index'])->name('index')->middleware('permission:View Income Records');
    Route::get('/create', [IncomeRecordController::class, 'create'])->name('create')->middleware('permission:Create Income Records');
    Route::get('/{id}/edit', [IncomeRecordController::class, 'edit'])->name('edit')->middleware('permission:Edit Income Records');
    Route::post('/', [IncomeRecordController::class, 'store'])->name('store')->middleware('permission:Create Income Records');
    Route::put('/{id}', [IncomeRecordController::class, 'update'])->name('update')->middleware('permission:Edit Income Records');
    Route::delete('/{id}', [IncomeRecordController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Income Records');
    Route::get('/{id}/invoice', [IncomeRecordController::class, 'invoice'])->name('invoice');
    Route::get('/export', [IncomeRecordController::class, 'export'])->name('export');
});

Route::prefix('expense-records')->name('expense-records.')->group(function () {
    Route::get('/', [ExpenseRecordController::class, 'index'])->name('index')->middleware('permission:View Expense Records');
    Route::get('/create', [ExpenseRecordController::class, 'create'])->name('create')->middleware('permission:Create Expense Records');
    Route::get('/{id}/edit', [ExpenseRecordController::class, 'edit'])->name('edit')->middleware('permission:Edit Expense Records');
    Route::post('/', [ExpenseRecordController::class, 'store'])->name('store')->middleware('permission:Create Expense Records');
    Route::put('/{id}', [ExpenseRecordController::class, 'update'])->name('update')->middleware('permission:Edit Expense Records');
    Route::delete('/{id}', [ExpenseRecordController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Expense Records');
    Route::get('/{id}/receipt', [ExpenseRecordController::class, 'receipt'])->name('receipt');
    Route::get('/export', [ExpenseRecordController::class, 'export'])->name('export');
});

// Route::prefix('ledgers')->name('ledgers.')->group(function () {
//     Route::get('/', [LedgerController::class, 'index'])->name('index');
//     Route::get('/create', [LedgerController::class, 'create'])->name('create');
//     Route::get('/{ledger}', [LedgerController::class, 'show'])->name('show');
// });

Route::prefix('ledgers')->name('ledgers.')->middleware(['auth'])->group(function () {
    Route::get('/',                    [LedgerController::class, 'index'])->name('index')->middleware('permission:View Ledgers');
    Route::get('/create',              [LedgerController::class, 'create'])->name('create')->middleware('permission:Create Ledgers');
    Route::post('/',                   [LedgerController::class, 'store'])->name('store')->middleware('permission:View Ledgers');
    Route::get('/{id}/edit',           [LedgerController::class, 'edit'])->name('edit')->middleware('permission:Edit Ledgers');
    Route::put('/{id}',                [LedgerController::class, 'update'])->name('update')->middleware('permission:Edit Ledgers');
    Route::delete('/{id}',             [LedgerController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Ledgers');
    Route::get('/{id}',                [LedgerController::class, 'show'])->name('show')->middleware('permission:View Ledgers');
    Route::get('/journal/create',      [LedgerController::class, 'journalCreate'])->name('journal.create');
    Route::post('/journal',            [LedgerController::class, 'journalStore'])->name('journal.store');
    Route::get('/sync',                [LedgerController::class, 'sync'])->name('sync');
    Route::get('/trial-balance',       [LedgerController::class, 'trialBalance'])->name('trial-balance');
});

Route::prefix('scholarships')->name('scholarships.')->middleware(['auth'])->group(function () {
    Route::get('/', [ScholarshipController::class, 'index'])->name('index');
    Route::get('/create', [ScholarshipController::class, 'create'])->name('create');
    Route::get('/{id}/edit', [ScholarshipController::class, 'edit'])->name('edit');
    Route::post('/', [ScholarshipController::class, 'store'])->name('store');
    Route::delete('/{id}', [ScholarshipController::class, 'destroy'])->name('destroy');
});

//student
Route::get('/studentsacademic', function () {
    return view('studentacademic.login');
});
Route::post('/studentsacademic/login', [StudentloginController::class, 'login'])->name('student.studentsacademic.logins');



Route::get('/studentsacademic/dashboard', [StudentloginController::class, 'portalDashboard'])->name('student.studentsacademic.dashboard');

Route::get('/studentsacademic/timetable', [StudentloginController::class, 'timetable'])->name('student.studentsacademic.timetable');
Route::get('/studentsacademic/careerdetails', [StudentloginController::class, 'career'])->name('student.studentsacademic.careerdetails');


Route::get('/studentsacademic/attendancedetails', [StudentloginController::class, 'attendancePortal'])->name('student.studentsacademic.attendancedetails');

Route::get('/studentsacademic/examresults', [StudentloginController::class, 'examResults'])->name('student.studentsacademic.examresults');

Route::get('/studentsacademic/logout', [StudentloginController::class, 'logout'])->name('student.studentsacademic.logout');

Route::get('/studentsacademic/hostel', [StudentloginController::class, 'myTransport'])->name('student.studentsacademic.hostel');
Route::get('/studentsacademic/accountdetails', [StudentloginController::class, 'account'])->name('student.studentsacademic.accountdetails');
Route::post('/account/create-order', [StudentloginController::class, 'createPaymentOrder'])->name('student.create.order');
Route::post('/account/verify-payment', [StudentloginController::class, 'verifyPayment'])->name('student.verify.payment');
Route::get('/studentsacademic/librarydetails', [StudentloginController::class, 'myIssuedBooks'])->name('student.studentsacademic.librarydetails');
Route::get('/studentsacademic/profile', [StudentloginController::class, 'profile'])->name('student.studentsacademic.profile');





Route::get('/teachersacademic', function () {
    return view('teachersacademic.login');
});

Route::post('/teachersacademic/login', [teacherController::class, 'login'])->name('teacher.teachersacademic.logins');


Route::get('/teachersacademic/dashboard', [teacherController::class, 'dashboard'])->name('teacher.teachersacademic.dashboard');

Route::get('/teachersacademic/show', [teacherController::class, 'show'])->name('teacher.teachersacademic.show');

// Attendance Routes
Route::get('/teachersacademic/attendance/mark/{slot}/{date}', [teacherController::class, 'markDirect'])->name('teacher.student_attendance.mark_direct');
Route::post('/teachersacademic/attendance/store', [teacherController::class, 'storeAttendance'])->name('teacher.student_attendance.store');


Route::get('/teachersacademic/logout', [teacherController::class, 'logout'])->name('teacher.teachersacademic.logout');



Route::get('/teachersacademic/exam', [teacherController::class, 'index'])->name('teacher.exams.index');
Route::get('/teachersacademic/exam/create', [teacherController::class, 'create'])->name('teacher.exams.create');
Route::post('/teachersacademic/exam', [teacherController::class, 'store'])->name('teacher.exams.store');
Route::get('/teachersacademic/exam/{exam}/edit', [teacherController::class, 'edit'])->name('teacher.exams.edit');
Route::put('/teachersacademic/exam/{exam}', [teacherController::class, 'update'])->name('teacher.exams.update');
Route::delete('/teachersacademic/exam/{exam}', [teacherController::class, 'destroy'])->name('teacher.exams.destroy');


Route::get('/teachersacademic/exam-marks', [teacherController::class, 'exammarkindex'])->name('teacher.exam_marks.index');
Route::get('/teachersacademic/exam-marks/create', [teacherController::class, 'exammarkcreate'])->name('teacher.exam_marks.create');
Route::post('/teachersacademic/exam-marks', [teacherController::class, 'exammarkstore'])->name('teacher.exam_marks.store');
Route::get('/teachersacademic/exam-marks/{examMark}/edit', [teacherController::class, 'exammarkedit'])->name('teacher.exam_marks.edit');
Route::put('/teachersacademic/exam-marks/{examMark}', [teacherController::class, 'exammarkupdate'])->name('teacher.exam_marks.update');
Route::delete('/teachersacademic/exam-marks/{examMark}', [teacherController::class, 'exammarkdestroy'])->name('teacher.exam_marks.destroy');

// ── Teacher Self-Service: Attendance ───────────────────────────────────
Route::get('/teachersacademic/my-attendance',         [teacherController::class, 'attendanceReport'])->name('teacher.my_attendance.index');
Route::post('/teachersacademic/my-attendance/punch',  [teacherController::class, 'attendancePunch'])->name('teacher.my_attendance.punch');

// ── Teacher Self-Service: Leave Requests ───────────────────────────────
Route::get('/teachersacademic/my-leave',              [teacherController::class, 'leaveIndex'])->name('teacher.my_leave.index');
Route::get('/teachersacademic/my-leave/create',       [teacherController::class, 'leaveCreate'])->name('teacher.my_leave.create');
Route::post('/teachersacademic/my-leave',             [teacherController::class, 'leaveStore'])->name('teacher.my_leave.store');

// ── Teacher Self-Service: Salary / Payslips ────────────────────────────
Route::get('/teachersacademic/my-salary',             [teacherController::class, 'salaryIndex'])->name('teacher.my_salary.index');
Route::get('/teachersacademic/my-salary/{payslip}',   [teacherController::class, 'salaryShow'])->name('teacher.my_salary.show');



Route::get('/bank_accounts', [BankAccountsController::class, 'index'])->name('admin.bank_accounts.index')->middleware('permission:View Bank Accounts');
Route::get('/bank_accounts/create', [BankAccountsController::class, 'create'])->name('admin.bank_accounts.create')->middleware('permission:Create Bank Accounts');
Route::post('/bank_accounts/store', [BankAccountsController::class, 'store'])->name('admin.bank_accounts.store')->middleware('permission:Create Bank Accounts');
Route::get('/bank_accounts/{id}/edit', [BankAccountsController::class, 'edit'])->name('admin.bank_accounts.edit')->middleware('permission:Edit Bank Accounts');
Route::put('/bank_accounts/{id}', [BankAccountsController::class, 'update'])->name('admin.bank_accounts.update')->middleware('permission:Edit Bank Accounts');
Route::delete('/bank_accounts/{id}', [BankAccountsController::class, 'destroy'])->name('admin.bank_accounts.destroy')->middleware('permission:Delete Bank Accounts');

//portal setting
Route::get('/settings',  [PortalSettingController::class, 'index'])
     ->name('settings.index');
Route::put('/settings',  [PortalSettingController::class, 'update'])
     ->name('settings.update');


//notice board
Route::get('/academic/notice_boards', [NoticeBoardController::class, 'index'])->name('admin.notice_boards.index')->middleware('permission:View Notice Board');
Route::get('/academic/notice_boards/create', [NoticeBoardController::class, 'create'])->name('admin.notice_boards.create')->middleware('permission:Create Notice Board');
Route::post('/academic/notice_boards', [NoticeBoardController::class, 'store'])->name('admin.notice_boards.store')->middleware('permission:Create Notice Board');
Route::get('/academic/notice_boards/{noticeBoard}/edit', [NoticeBoardController::class, 'edit'])->name('admin.notice_boards.edit')->middleware('permission:Edit Notice Board');
Route::put('/academic/notice_boards/{noticeBoard}', [NoticeBoardController::class, 'update'])->name('admin.notice_boards.update')->middleware('permission:Edit Notice Board');
Route::delete('/academic/notice_boards/{noticeBoard}', [NoticeBoardController::class, 'destroy'])->name('admin.notice_boards.destroy')->middleware('permission:Delete Notice Board');

// routes/web.php — admin routes group ke andar
Route::prefix('admin')->middleware(['auth'])->group(function () {

    Route::prefix('students/{student}')->group(function () {
        Route::post('qualifications', [StudentAcademicQualificationController::class, 'store'])
            ->name('admin.students.qualifications.store');
        Route::put('qualifications/{qualification}', [StudentAcademicQualificationController::class, 'update'])
            ->name('admin.students.qualifications.update');
        Route::delete('qualifications/{qualification}', [StudentAcademicQualificationController::class, 'destroy'])
            ->name('admin.students.qualifications.destroy');
        Route::get('qualifications/{qualification}/edit', [StudentAcademicQualificationController::class, 'edit'])
            ->name('admin.students.qualifications.edit');
    });


});
// Practical Groups
Route::get('/practical-groups',                        [PracticalGroupController::class, 'index'])       ->name('practical_groups.index');
Route::get('/practical-groups/create',                 [PracticalGroupController::class, 'create'])      ->name('practical_groups.create');
Route::post('/practical-groups',                       [PracticalGroupController::class, 'store'])       ->name('practical_groups.store');
Route::get('/practical-groups/{practicalGroup}/edit',  [PracticalGroupController::class, 'edit'])        ->name('practical_groups.edit');
Route::put('/practical-groups/{practicalGroup}',       [PracticalGroupController::class, 'update'])      ->name('practical_groups.update');
Route::delete('/practical-groups/{practicalGroup}',    [PracticalGroupController::class, 'destroy'])     ->name('practical_groups.destroy');
Route::get('/practical-groups/get-streams/{courseId}', [PracticalGroupController::class, 'getStreams'])  ->name('practical_groups.getStreams');
Route::get('/practical-groups/get-students',           [PracticalGroupController::class, 'getStudents']) ->name('practical_groups.getStudents');

Route::get('/practical-timetables',        [PracticalTimetableController::class, 'index'])->name('practical_timetables.index');
Route::get('/practical-timetables/master', [PracticalTimetableController::class, 'master'])->name('practical_timetables.master');
Route::get('/practical-timetable-slots/{slot}/edit', [PracticalTimetableController::class, 'editSlot'])->name('practical_timetable_slots.edit');
Route::put('/practical-timetable-slots/{slot}', [PracticalTimetableController::class, 'updateSlot'])->name('practical_timetable_slots.update');
Route::delete('/practical-timetable-slots/{slot}', [PracticalTimetableController::class, 'destroySlot'])->name('practical_timetable_slots.destroy');
Route::get('/practical-timetables/create', [PracticalTimetableController::class, 'create'])->name('practical_timetables.create');
Route::post('/practical-timetables',       [PracticalTimetableController::class, 'store'])->name('practical_timetables.store');

// Group Master
Route::get('/group-master',                        [GroupMasterController::class, 'index'])   ->name('group_master.index');
Route::get('/group-master/create',                 [GroupMasterController::class, 'create'])  ->name('group_master.create');
Route::post('/group-master',                       [GroupMasterController::class, 'store'])   ->name('group_master.store');
Route::get('/group-master/{groupMaster}/edit',     [GroupMasterController::class, 'edit'])    ->name('group_master.edit');
Route::put('/group-master/{groupMaster}',          [GroupMasterController::class, 'update'])  ->name('group_master.update');
Route::delete('/group-master/{groupMaster}',       [GroupMasterController::class, 'destroy']) ->name('group_master.destroy');

// ── Exam Centers ──────────────────────────────────────────────────────────
Route::get('/exam-centers',                    [ExamCenterController::class, 'index'])->name('admin.exam_center.index');
Route::get('/exam-centers/create',             [ExamCenterController::class, 'create'])->name('admin.exam_center.create');
Route::post('/exam-centers',                   [ExamCenterController::class, 'store'])->name('admin.exam_center.store');
Route::get('/exam-centers/{id}/edit',          [ExamCenterController::class, 'edit'])->name('admin.exam_center.edit');
Route::delete('/exam-centers/{id}',            [ExamCenterController::class, 'destroy'])->name('admin.exam_center.destroy');
Route::post('/exam-centers/{id}/toggle-status',[ExamCenterController::class, 'toggleStatus'])->name('admin.exam_center.toggle_status');
Route::get('/exam-centers/{id}/devices',       [ExamCenterController::class, 'devices'])->name('admin.exam_center.devices');
 
// ── Center Devices ────────────────────────────────────────────────────────
Route::get('/center-devices',                    [CenterDeviceController::class, 'index'])->name('admin.center_devices.index');
Route::get('/center-devices/create',             [CenterDeviceController::class, 'create'])->name('admin.center_devices.create');
Route::post('/center-devices',                   [CenterDeviceController::class, 'store'])->name('admin.center_devices.store');
Route::get('/center-devices/{id}/edit',          [CenterDeviceController::class, 'edit'])->name('admin.center_devices.edit');
Route::delete('/center-devices/{id}',            [CenterDeviceController::class, 'destroy'])->name('admin.center_devices.destroy');
Route::post('/center-devices/{id}/toggle-status',[CenterDeviceController::class, 'toggleStatus'])->name('admin.center_devices.toggle_status');