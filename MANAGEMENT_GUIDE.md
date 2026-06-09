# Management Section Documentation

## Overview
The Management section is a comprehensive admin control panel integrated into the SafeStep Bus dashboard. It provides administrators with tools to manage users, roles, permissions, system configuration, and form submissions.

## Features

### 1. **Admin Dashboard Stats**
- **Admin Users Count**: Total number of admin users in the system
- **Approved Requests**: Count of approved submissions
- **Pending Reviews**: Number of items awaiting action
- **Rejected Items**: Count of rejected submissions

### 2. **Admin Management**
Located in: `resources/views/admin/admin.blade.php` (lines 2367+)

Features:
- View all admin users in a sortable table
- Add new admin users
- Edit admin user details
- Delete admin accounts
- View admin roles and last login time
- Monitor admin activity status

**Usage:**
```php
// Add new admin button triggers addNewAdmin()
// Admin data populated from database via AJAX
```

### 3. **Role & Permissions Management**
Features:
- Create custom roles
- Assign permissions to roles
- Support for default roles: Admin, Manager, Supervisor, Operator
- Granular permission control
- Save and reset permissions

**Permission Types:**
- View Dashboard
- Manage Users
- Manage Applications
- Manage Reports
- Manage Settings
- Manage Submissions

**Usage:**
```javascript
// Select a role from dropdown
loadRolePermissions(role);

// Toggle permissions as needed
// Click "Save Permissions" to persist changes
```

### 4. **System Configuration**
Manage core system settings:
- **App Name**: Application title (default: SafeStep Bus)
- **Support Email**: Main support contact email
- **Support Phone**: Customer support phone number
- **Max Login Attempts**: Security setting for login failures
- **Session Timeout**: Inactivity timeout in minutes (default: 30)
- **Password Expiration**: Force password change after X days (default: 90)

**Usage:**
```javascript
// Modify any setting
// Click "Save Configuration" to update
// Click "Reset" to restore defaults
```

### 5. **Application Submission Form Builder**
A visual form builder for creating custom submission forms without coding.

**Features:**
- **Form Configuration:**
  - Form name and description
  - Form type selection (Application, Complaint, Request, Registration, Feedback, Other)

- **Field Types Supported:**
  - Text Input
  - Email
  - Phone Number
  - Number
  - Date
  - Text Area
  - Dropdown (Select)
  - Checkbox
  - Radio Button
  - File Upload

- **Field Options:**
  - Required/Optional toggle
  - Full/Half/Third width layout
  - Placeholder text and helper text
  - Custom options for dropdowns, checkboxes, and radio buttons

- **Form Management:**
  - Live preview as fields are added
  - Edit existing fields
  - Delete fields
  - Clear form and start over
  - Save and publish forms

**Dedicated Page:**
Accessible at: `/admin/submission-form` (via `resources/views/admin/submission-form.blade.php`)

**Form Builder UI:**
```
[Left Panel]
├── Form Configuration
│   ├── Form Name
│   ├── Form Type
│   └── Description
└── Field Builder
    ├── Field Label
    ├── Field Type
    ├── Field Width
    ├── Required Checkbox
    ├── Options (for dropdowns)
    └── Placeholder Text

[Right Panel]
├── Field Count Badge
├── Live Preview
│   └── Rendered form display
└── Fields List
    ├── Field entries with edit/delete buttons
    └── Reorderable fields
```

### 6. **Recent Submissions Management**
View and manage all form submissions:
- Filter by type (Applications, Complaints, Requests)
- Filter by status (New, Reviewed, Approved, Rejected)
- View submission details
- Download submission data
- Export all submissions to Excel
- Refresh submissions list

**Actions Available:**
- View submission details
- Download individual submissions
- Bulk export functionality
- Status tracking

## Database Integration

### Tables Required
```sql
-- Admin users
CREATE TABLE admins (
    id PRIMARY KEY,
    name VARCHAR,
    email VARCHAR UNIQUE,
    role_id FOREIGN KEY,
    created_at TIMESTAMP,
    last_login TIMESTAMP,
    status ENUM('active', 'inactive')
);

-- Roles
CREATE TABLE roles (
    id PRIMARY KEY,
    name VARCHAR UNIQUE,
    description TEXT
);

-- Permissions
CREATE TABLE permissions (
    id PRIMARY KEY,
    name VARCHAR UNIQUE,
    description TEXT
);

-- Role-Permission pivot
CREATE TABLE role_permissions (
    role_id FOREIGN KEY,
    permission_id FOREIGN KEY
);

-- Form submissions
CREATE TABLE submissions (
    id PRIMARY KEY,
    form_id FOREIGN KEY,
    submitted_by VARCHAR,
    email VARCHAR,
    data JSON,
    status ENUM('new', 'reviewed', 'approved', 'rejected'),
    submitted_at TIMESTAMP
);

-- System settings
CREATE TABLE settings (
    id PRIMARY KEY,
    key VARCHAR UNIQUE,
    value TEXT
);
```

## JavaScript Implementation

### Files Included
1. **public/js/management.js** - Core management functions
   - Admin management functions
   - Permission loading and saving
   - System configuration handling
   - Submission management
   - Form field handling

2. **public/css/management.css** - Styling
   - Management page layout
   - Stats card styling
   - Form builder UI
   - Table styling
   - Responsive adjustments

### Function Reference

```javascript
// Admin Management
addNewAdmin()                   // Add new admin user
editAdmin(adminId)              // Edit admin details
deleteAdmin(adminId)            // Remove admin

// Role & Permissions
createNewRole()                 // Create custom role
loadRolePermissions(role)       // Load permissions for role
savePermissions()               // Save role permissions
resetPermissions()              // Reset permissions to default

// System Configuration
saveSystemConfig()              // Save system settings
resetSystemConfig()             // Reset to defaults

// Submissions
viewSubmission(submissionId)    // View submission details
downloadSubmission(submissionId)// Download submission data
refreshSubmissions()            // Reload submission list
exportSubmissions()             // Export to Excel

// Form Builder
addFormField()                  // Add field to form
removeField(fieldId)            // Remove field from form
saveBuiltForm()                 // Save form template
```

## CSS Classes

### Utility Classes
- `.stat-card` - Statistics display card
- `.stat-icon.blue/green/orange/purple` - Icon background colors
- `.status-badge` - Status indicator badges
- `.status-badge.active/inactive/pending/new/approved/rejected` - Badge colors
- `.btn-primary` - Primary action button
- `.btn-secondary` - Secondary action button
- `.btn-sm` - Small button variant
- `.btn-danger` - Danger action button
- `.card-header` - Card header styling
- `.filters` - Filter section layout
- `.table-wrapper` - Table container

## Responsive Behavior

### Breakpoints
- **Desktop (1024px+)**: Full 2-column form builder
- **Tablets (768px-1023px)**: Single column layout
- **Mobile (<768px)**: Optimized single column, compact styling

### Mobile Optimizations
- Stacked filter selectors
- Single column table view
- Full-width buttons
- Reduced padding/margins
- Horizontal scroll for tables

## Security Considerations

1. **Permission Checks**
   - All admin actions require proper role/permission verification
   - Server-side validation required for all submissions

2. **CSRF Protection**
   - CSRF token included in all forms
   - Meta tag: `<meta name="csrf-token">`

3. **Rate Limiting**
   - Implement rate limiting for admin operations
   - Prevent brute force attacks on admin login

4. **Audit Trail**
   - Log all admin actions
   - Track permission changes
   - Monitor system configuration modifications

## Integration Steps

### 1. Add Management Link to Navigation
Already done in admin.blade.php:
```html
<a href="#" class="nav-link" data-page="management">
    <i class="fas fa-sliders-h"></i><span>Management</span>
</a>
```

### 2. Include CSS
Already added to admin.blade.php:
```html
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
```

### 3. Include JavaScript
Already added to admin.blade.php:
```html
<script src="{{ asset('js/management.js') }}"></script>
```

### 4. Create Backend Routes
Add to `routes/api.php`:
```php
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admins
    Route::get('/admins', [AdminController::class, 'index']);
    Route::post('/admins', [AdminController::class, 'store']);
    Route::put('/admins/{id}', [AdminController::class, 'update']);
    Route::delete('/admins/{id}', [AdminController::class, 'destroy']);

    // Roles
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    
    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/roles/{id}/permissions', [RoleController::class, 'updatePermissions']);

    // Submissions
    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::get('/submissions/{id}', [SubmissionController::class, 'show']);
    Route::post('/submissions/{id}/approve', [SubmissionController::class, 'approve']);
    Route::post('/submissions/{id}/reject', [SubmissionController::class, 'reject']);
    Route::post('/submissions/export', [SubmissionController::class, 'export']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'update']);
});
```

### 5. Create Backend Controllers
Implement controllers for:
- `AdminController` - Admin management
- `RoleController` - Role management
- `PermissionController` - Permission management
- `SubmissionController` - Submission handling
- `SettingController` - System settings

## Features Roadmap

### Phase 2 (Planned)
- [ ] Advanced form analytics
- [ ] Form version control
- [ ] Conditional field logic
- [ ] Multi-language form support
- [ ] Email notification rules
- [ ] Webhook integrations
- [ ] Custom form styling
- [ ] Form templates library
- [ ] A/B testing for forms
- [ ] Advanced permission matrix

### Phase 3 (Planned)
- [ ] Form scheduling
- [ ] Automated workflows
- [ ] Advanced reporting
- [ ] Machine learning insights
- [ ] Form field validation rules builder
- [ ] Custom JavaScript code support

## Troubleshooting

### Issue: Management page doesn't display
**Solution:** Ensure `data-page="management"` is in navigation and page div exists in admin.blade.php

### Issue: JavaScript functions not working
**Solution:** Check that management.js is loaded and there are no console errors

### Issue: CSS not applying
**Solution:** Verify management.css is linked in head and styles aren't overridden

### Issue: Forms not saving
**Solution:** Check backend API endpoints are implemented and CSRF token is valid

## Support

For issues or feature requests, contact: support@safestep.com

---

**Last Updated:** 2026-06-09
**Version:** 1.0
**Compatibility:** Laravel 11+, PHP 8.1+
