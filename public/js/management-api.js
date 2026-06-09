/**
 * Management API Integration
 * Examples for backend API calls to support management features
 */

// Base API configuration
const MANAGEMENT_API = {
    admins: '/api/admins',
    roles: '/api/roles',
    permissions: '/api/permissions',
    submissions: '/api/submissions',
    settings: '/api/settings',
    forms: '/api/forms',
};

// Helper function for API calls
async function managementApiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `Bearer ${localStorage.getItem('safestep_token') || ''}`,
        },
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(endpoint, options);
        if (!response.ok) {
            throw new Error(`API Error: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error('API Call Error:', error);
        throw error;
    }
}

// ============================================
// ADMIN MANAGEMENT API CALLS
// ============================================

/**
 * Get all admin users
 */
async function getAdmins() {
    return managementApiCall(MANAGEMENT_API.admins);
}

/**
 * Create new admin user
 * @param {Object} adminData - Admin details
 */
async function createAdmin(adminData) {
    return managementApiCall(MANAGEMENT_API.admins, 'POST', {
        name: adminData.name,
        email: adminData.email,
        role_id: adminData.role_id,
        password: adminData.password,
    });
}

/**
 * Update admin user
 * @param {Number} adminId - Admin ID
 * @param {Object} adminData - Updated admin details
 */
async function updateAdmin(adminId, adminData) {
    return managementApiCall(`${MANAGEMENT_API.admins}/${adminId}`, 'PUT', adminData);
}

/**
 * Delete admin user
 * @param {Number} adminId - Admin ID
 */
async function deleteAdmin(adminId) {
    return managementApiCall(`${MANAGEMENT_API.admins}/${adminId}`, 'DELETE');
}

// ============================================
// ROLE & PERMISSION MANAGEMENT API CALLS
// ============================================

/**
 * Get all roles
 */
async function getRoles() {
    return managementApiCall(MANAGEMENT_API.roles);
}

/**
 * Create new role
 * @param {Object} roleData - Role details
 */
async function createRole(roleData) {
    return managementApiCall(MANAGEMENT_API.roles, 'POST', {
        name: roleData.name,
        description: roleData.description,
    });
}

/**
 * Get permissions for a role
 * @param {Number} roleId - Role ID
 */
async function getRolePermissions(roleId) {
    return managementApiCall(`${MANAGEMENT_API.roles}/${roleId}/permissions`);
}

/**
 * Update role permissions
 * @param {Number} roleId - Role ID
 * @param {Array} permissionIds - Array of permission IDs
 */
async function updateRolePermissions(roleId, permissionIds) {
    return managementApiCall(`${MANAGEMENT_API.roles}/${roleId}/permissions`, 'POST', {
        permissions: permissionIds,
    });
}

/**
 * Get all available permissions
 */
async function getAllPermissions() {
    return managementApiCall(MANAGEMENT_API.permissions);
}

// ============================================
// SYSTEM CONFIGURATION API CALLS
// ============================================

/**
 * Get all system settings
 */
async function getSystemSettings() {
    return managementApiCall(MANAGEMENT_API.settings);
}

/**
 * Update system settings
 * @param {Object} settings - Settings object
 */
async function updateSystemSettings(settings) {
    return managementApiCall(MANAGEMENT_API.settings, 'POST', {
        ...settings,
    });
}

/**
 * Get specific setting value
 * @param {String} key - Setting key
 */
async function getSetting(key) {
    return managementApiCall(`${MANAGEMENT_API.settings}?key=${key}`);
}

// ============================================
// FORM SUBMISSION MANAGEMENT API CALLS
// ============================================

/**
 * Get all submissions with filters
 * @param {Object} filters - Filter parameters
 */
async function getSubmissions(filters = {}) {
    const queryParams = new URLSearchParams(filters).toString();
    return managementApiCall(`${MANAGEMENT_API.submissions}?${queryParams}`);
}

/**
 * Get submission details
 * @param {Number} submissionId - Submission ID
 */
async function getSubmissionDetail(submissionId) {
    return managementApiCall(`${MANAGEMENT_API.submissions}/${submissionId}`);
}

/**
 * Create new submission (for admin form builder)
 * @param {Object} submissionData - Submission data
 */
async function createSubmission(submissionData) {
    return managementApiCall(MANAGEMENT_API.submissions, 'POST', submissionData);
}

/**
 * Update submission status
 * @param {Number} submissionId - Submission ID
 * @param {String} status - New status (approved, rejected, etc.)
 */
async function updateSubmissionStatus(submissionId, status) {
    return managementApiCall(`${MANAGEMENT_API.submissions}/${submissionId}`, 'PUT', {
        status,
    });
}

/**
 * Approve submission
 * @param {Number} submissionId - Submission ID
 * @param {Object} approvalData - Additional approval data
 */
async function approveSubmission(submissionId, approvalData = {}) {
    return managementApiCall(`${MANAGEMENT_API.submissions}/${submissionId}/approve`, 'POST', approvalData);
}

/**
 * Reject submission
 * @param {Number} submissionId - Submission ID
 * @param {Object} rejectionData - Rejection reason and details
 */
async function rejectSubmission(submissionId, rejectionData) {
    return managementApiCall(`${MANAGEMENT_API.submissions}/${submissionId}/reject`, 'POST', {
        reason: rejectionData.reason,
        comment: rejectionData.comment,
    });
}

/**
 * Export submissions
 * @param {Object} filters - Export filters
 */
async function exportSubmissions(filters = {}) {
    return managementApiCall(`${MANAGEMENT_API.submissions}/export`, 'POST', filters);
}

// ============================================
// FORM BUILDER API CALLS
// ============================================

/**
 * Get all form templates
 */
async function getFormTemplates() {
    return managementApiCall(MANAGEMENT_API.forms);
}

/**
 * Create new form template
 * @param {Object} formData - Form configuration
 */
async function createFormTemplate(formData) {
    return managementApiCall(MANAGEMENT_API.forms, 'POST', {
        name: formData.name,
        description: formData.description,
        type: formData.type,
        fields: formData.fields,
    });
}

/**
 * Update form template
 * @param {Number} formId - Form ID
 * @param {Object} formData - Updated form data
 */
async function updateFormTemplate(formId, formData) {
    return managementApiCall(`${MANAGEMENT_API.forms}/${formId}`, 'PUT', formData);
}

/**
 * Delete form template
 * @param {Number} formId - Form ID
 */
async function deleteFormTemplate(formId) {
    return managementApiCall(`${MANAGEMENT_API.forms}/${formId}`, 'DELETE');
}

/**
 * Get form template details
 * @param {Number} formId - Form ID
 */
async function getFormTemplate(formId) {
    return managementApiCall(`${MANAGEMENT_API.forms}/${formId}`);
}

// ============================================
// BACKEND CONTROLLER EXAMPLES (Laravel)
// ============================================

/*

// App/Http/Controllers/Api/AdminController.php
class AdminController extends Controller {
    public function index() {
        // Get all admins with roles
        $admins = Admin::with('role')->paginate();
        return response()->json($admins);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($admin, 201);
    }

    public function update(Request $request, Admin $admin) {
        $validated = $request->validate([
            'name' => 'string',
            'email' => 'email|unique:admins,email,'.$admin->id,
            'role_id' => 'exists:roles,id',
        ]);

        $admin->update($validated);
        return response()->json($admin);
    }

    public function destroy(Admin $admin) {
        $admin->delete();
        return response()->json(null, 204);
    }
}

// App/Http/Controllers/Api/RoleController.php
class RoleController extends Controller {
    public function index() {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function updatePermissions(Request $request, Role $role) {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions']);
        return response()->json($role->fresh('permissions'));
    }
}

// App/Http/Controllers/Api/SubmissionController.php
class SubmissionController extends Controller {
    public function index(Request $request) {
        $query = Submission::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate());
    }

    public function approve(Request $request, Submission $submission) {
        $submission->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Send approval notification
        // event(new SubmissionApproved($submission));

        return response()->json($submission);
    }

    public function reject(Request $request, Submission $submission) {
        $validated = $request->validate([
            'reason' => 'required|string',
            'comment' => 'string',
        ]);

        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'],
            'rejection_comment' => $validated['comment'] ?? null,
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        // Send rejection notification
        // event(new SubmissionRejected($submission));

        return response()->json($submission);
    }

    public function export(Request $request) {
        $submissions = Submission::query()
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->get();

        // Generate Excel export
        return Excel::download(new SubmissionsExport($submissions), 'submissions.xlsx');
    }
}

// Database Migrations
class CreateAdminsTable extends Migration {
    public function up() {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('role_id')->constrained('roles');
            $table->string('password');
            $table->timestamp('last_login')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }
}

class CreateRolesTable extends Migration {
    public function up() {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
}

class CreatePermissionsTable extends Migration {
    public function up() {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
}

class CreateRolePermissionTable extends Migration {
    public function up() {
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->unique(['role_id', 'permission_id']);
        });
    }
}

class CreateSubmissionsTable extends Migration {
    public function up() {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms');
            $table->string('submitted_by');
            $table->string('email');
            $table->json('data');
            $table->enum('status', ['new', 'reviewed', 'approved', 'rejected'])->default('new');
            $table->string('rejection_reason')->nullable();
            $table->text('rejection_comment')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admins');
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('admins');
            $table->timestamps();
        });
    }
}

*/

// ============================================
// USAGE EXAMPLES
// ============================================

/*

// Load admins into management page
async function loadManagementData() {
    try {
        const admins = await getAdmins();
        console.log('Loaded admins:', admins);
        // Populate admin table with data
    } catch (error) {
        console.error('Failed to load admins:', error);
    }
}

// Save role permissions
async function saveRolePermissions(roleId, selectedPermissions) {
    try {
        await updateRolePermissions(roleId, selectedPermissions);
        alert('Permissions updated successfully!');
    } catch (error) {
        alert('Failed to update permissions');
    }
}

// Create new form template
async function createNewForm() {
    const formData = {
        name: 'Driver Application Form',
        description: 'Application form for new bus drivers',
        type: 'application',
        fields: [
            { label: 'Full Name', type: 'text', required: true },
            { label: 'Email', type: 'email', required: true },
            { label: 'Phone', type: 'tel', required: true },
        ],
    };

    try {
        const result = await createFormTemplate(formData);
        console.log('Form created:', result);
    } catch (error) {
        console.error('Failed to create form:', error);
    }
}

// Get and export submissions
async function exportAllSubmissions() {
    try {
        const result = await exportSubmissions({
            type: 'application',
            status: 'approved',
        });
        // Trigger download
    } catch (error) {
        console.error('Export failed:', error);
    }
}

*/
