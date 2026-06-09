/**
 * Management Dashboard Functions
 * Handles Admin, Roles, Permissions, and Form Submissions
 */

(function() {
    'use strict';

    // Initialize Management Page
    function initManagementPage() {
        console.log('[Management] Initializing...');
        loadAdmins();
        loadRecentSubmissions();
    }

    // Load Admin Users
    function loadAdmins() {
        const table = document.getElementById('adminsTable');
        if (!table) return;

        // Sample data - replace with API call
        const admins = [
            { id: 1, name: 'Ahmed Admin', email: 'ahmed@safestep.com', role: 'Super Admin', created: '2024-01-15', lastLogin: '2026-06-09', status: 'active' },
            { id: 2, name: 'Sara Manager', email: 'sara@safestep.com', role: 'Manager', created: '2024-02-20', lastLogin: '2026-06-08', status: 'active' },
            { id: 3, name: 'Omar Supervisor', email: 'omar@safestep.com', role: 'Supervisor', created: '2024-03-10', lastLogin: '2026-06-07', status: 'inactive' },
        ];

        const tbody = table.querySelector('tbody');
        tbody.innerHTML = admins.map(admin => `
            <tr>
                <td>${admin.name}</td>
                <td>${admin.email}</td>
                <td><span class="status-badge">${admin.role}</span></td>
                <td>${admin.created}</td>
                <td>${admin.lastLogin}</td>
                <td><span class="status-badge ${admin.status}">${admin.status}</span></td>
                <td>
                    <button class="btn-sm" onclick="editAdmin(${admin.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-sm btn-danger" onclick="deleteAdmin(${admin.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');
    }

    // Load Recent Submissions
    function loadRecentSubmissions() {
        const table = document.getElementById('submissionsTable');
        if (!table) return;

        const submissions = [
            { id: 'SUB-001', type: 'Application', by: 'Mohammed Ahmed', email: 'mohammed@example.com', status: 'new', date: '2026-06-09' },
            { id: 'SUB-002', type: 'Complaint', by: 'Fatima Hassan', email: 'fatima@example.com', status: 'reviewed', date: '2026-06-08' },
            { id: 'SUB-003', type: 'Request', by: 'Ahmed Karim', email: 'ahmed.k@example.com', status: 'approved', date: '2026-06-07' },
        ];

        const tbody = table.querySelector('tbody');
        tbody.innerHTML = submissions.map(sub => `
            <tr>
                <td><strong>${sub.id}</strong></td>
                <td>${sub.type}</td>
                <td>${sub.by}</td>
                <td>${sub.email}</td>
                <td><span class="status-badge ${sub.status}">${sub.status}</span></td>
                <td>${sub.date}</td>
                <td>
                    <button class="btn-sm" onclick="viewSubmission('${sub.id}')"><i class="fas fa-eye"></i> View</button>
                    <button class="btn-sm" onclick="downloadSubmission('${sub.id}')"><i class="fas fa-download"></i></button>
                </td>
            </tr>
        `).join('');
    }

    // Add New Admin
    window.addNewAdmin = function() {
        const name = prompt('Enter Admin Name:');
        if (!name) return;
        
        const email = prompt('Enter Admin Email:');
        if (!email) return;

        const role = confirm('Is this a Super Admin? (OK = Super Admin, Cancel = Manager)') ? 'Super Admin' : 'Manager';

        console.log(`Adding admin: ${name} (${email}) as ${role}`);
        alert(`Admin "${name}" has been created successfully!`);
        loadAdmins();
    };

    // Create New Role
    window.createNewRole = function() {
        const roleName = prompt('Enter Role Name:');
        if (!roleName) return;

        console.log(`Creating role: ${roleName}`);
        alert(`Role "${roleName}" has been created successfully!`);
    };

    // Load Role Permissions
    window.loadRolePermissions = function(role) {
        if (!role) return;

        const permissions = {
            admin: ['View Dashboard', 'Manage Users', 'Manage Applications', 'Manage Reports', 'Manage Settings', 'Manage Submissions'],
            manager: ['View Dashboard', 'Manage Applications', 'Manage Reports', 'Manage Submissions'],
            supervisor: ['View Dashboard', 'View Reports', 'Review Submissions'],
            operator: ['View Dashboard']
        };

        const container = document.getElementById('permissionsContainer');
        const perms = permissions[role] || [];

        container.innerHTML = perms.map((perm, index) => `
            <div style="display: flex; align-items: center; gap: 8px; padding: 10px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb;">
                <input type="checkbox" id="perm-${index}" checked>
                <label for="perm-${index}" style="margin: 0; cursor: pointer; flex: 1;">${perm}</label>
            </div>
        `).join('');
    };

    // Save Permissions
    window.savePermissions = function() {
        const role = document.getElementById('roleFilter').value;
        if (!role) {
            alert('Please select a role first');
            return;
        }

        const checkboxes = document.querySelectorAll('#permissionsContainer input[type="checkbox"]:checked');
        const selectedPermissions = Array.from(checkboxes).map(cb => cb.nextElementSibling.textContent);

        console.log(`Saving permissions for ${role}:`, selectedPermissions);
        alert(`Permissions for "${role}" have been updated!`);
    };

    // Reset Permissions
    window.resetPermissions = function() {
        document.getElementById('roleFilter').value = '';
        document.getElementById('permissionsContainer').innerHTML = '';
    };

    // Save System Configuration
    window.saveSystemConfig = function() {
        const config = {
            appName: document.getElementById('appName').value,
            supportEmail: document.getElementById('supportEmail').value,
            supportPhone: document.getElementById('supportPhone').value,
            maxAttempts: document.getElementById('maxAttempts').value,
            sessionTimeout: document.getElementById('sessionTimeout').value,
            passwordExpire: document.getElementById('passwordExpire').value,
        };

        console.log('Saving system config:', config);
        alert('System configuration has been updated successfully!');
    };

    // Reset System Config
    window.resetSystemConfig = function() {
        if (confirm('Reset all configurations to default?')) {
            document.getElementById('appName').value = 'SafeStep Bus';
            document.getElementById('supportEmail').value = 'support@safestep.com';
            document.getElementById('supportPhone').value = '+20 100 1234567';
            document.getElementById('maxAttempts').value = '5';
            document.getElementById('sessionTimeout').value = '30';
            document.getElementById('passwordExpire').value = '90';
        }
    };

    // Add Form Field
    window.addFormField = function() {
        const label = prompt('Enter field label (e.g., Full Name):');
        if (!label) return;

        const types = ['Text', 'Email', 'Phone', 'Number', 'Date', 'Textarea', 'Select', 'Checkbox'];
        const typeList = types.join('\n');
        const typeIndex = prompt(`Select field type:\n${typeList}`);

        if (typeIndex === null) return;

        const fieldType = types[parseInt(typeIndex) - 1];
        if (!fieldType) {
            alert('Invalid selection');
            return;
        }

        console.log(`Added field: ${label} (${fieldType})`);
    };

    // Submission Management
    window.viewSubmission = function(submissionId) {
        console.log(`Viewing submission: ${submissionId}`);
        alert(`Viewing submission details for ${submissionId}`);
    };

    window.downloadSubmission = function(submissionId) {
        console.log(`Downloading submission: ${submissionId}`);
        alert(`Downloading submission ${submissionId}...`);
    };

    window.refreshSubmissions = function() {
        loadRecentSubmissions();
        alert('Submissions refreshed!');
    };

    window.exportSubmissions = function() {
        console.log('Exporting submissions...');
        alert('Submissions will be exported as Excel file');
    };

    // Admin Management
    window.editAdmin = function(adminId) {
        console.log(`Editing admin: ${adminId}`);
        alert(`Editing admin #${adminId}`);
    };

    window.deleteAdmin = function(adminId) {
        if (confirm('Are you sure you want to delete this admin?')) {
            console.log(`Deleting admin: ${adminId}`);
            alert(`Admin #${adminId} has been deleted`);
            loadAdmins();
        }
    };

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('adminsTable')) {
            initManagementPage();
        }
    });

})();
