<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Applications Management - SafeStep Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-unified.css') }}">
    <script>window.__API_TOKEN = '{{ $apiToken ?? '' }}';</script>
</head>
<body>
<div class="layout">
    <!-- ═══ Sidebar ═══ -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h2><i class="fas fa-shield-alt"></i> SAFESTEP BUS</h2>
            <span>Admin Panel — {{ $adminName ?? 'Admin' }}</span>
        </div>
        <ul class="nav-items">
            <a href="#overview"      data-section="overview"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="#applications"  data-section="applications"><i class="fas fa-file-alt"></i> Applications <span class="nav-badge">{{ $stats['pending'] }}</span></a>
            <a href="#drivers"       data-section="drivers"><i class="fas fa-id-card"></i> Drivers</a>
            <a href="#parents"       data-section="parents"><i class="fas fa-users"></i> Parents</a>
            <a href="#notifications" data-section="notifications"><i class="fas fa-bell"></i> Notifications</a>
            <a href="{{ url('/logout') }}" onclick="event.preventDefault();localStorage.removeItem('safestep_token');localStorage.removeItem('token');window.location.href='{{ route('logout') }}'"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </ul>
    </aside>

    <!-- ═══ Main ═══ -->
    <main class="main">

        <!-- ════════════ OVERVIEW ════════════ -->
        <div class="section-page" id="section-overview">
            <div class="page-header"><div><h1><i class="fas fa-chart-line"></i> Dashboard Overview</h1><p>System statistics from the database</p></div></div>
            <div id="overviewContent"><div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Loading…</p></div></div>
        </div>

        <!-- ════════════ APPLICATIONS (server-rendered) ════════════ -->
        <div class="section-page" id="section-applications">
            <div class="page-header">
                <div><h1><i class="fas fa-file-alt"></i> Applications Management</h1><p>Review and manage all submitted applications</p></div>
            </div>

            <div class="stats-row">
                <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-layer-group"></i></div><div><h3>{{ $stats['total'] }}</h3><span>Total</span></div></div>
                <div class="stat-card"><div class="stat-icon amber"><i class="fas fa-clock"></i></div><div><h3>{{ $stats['pending'] }}</h3><span>Pending</span></div></div>
                <div class="stat-card"><div class="stat-icon green"><i class="fas fa-check-circle"></i></div><div><h3>{{ $stats['accepted'] }}</h3><span>Accepted</span></div></div>
                <div class="stat-card"><div class="stat-icon red"><i class="fas fa-times-circle"></i></div><div><h3>{{ $stats['rejected'] }}</h3><span>Rejected</span></div></div>
            </div>

            <form method="GET" action="{{ url('/admin/applications') }}" class="toolbar" id="filterForm">
                <div class="search-wrap"><i class="fas fa-search"></i><input type="text" name="search" class="search-input" placeholder="Search by name, phone, or email…" value="{{ request('search') }}"></div>
                <select name="role" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="all" {{ request('role','all')==='all'?'selected':'' }}>All Roles</option>
                    <option value="parent" {{ request('role')==='parent'?'selected':'' }}>Parent</option>
                    <option value="driver" {{ request('role')==='driver'?'selected':'' }}>Driver</option>
                    <option value="admin" {{ request('role')==='admin'?'selected':'' }}>Admin</option>
                </select>
                <select name="status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="all" {{ request('status','all')==='all'?'selected':'' }}>All Statuses</option>
                    <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                    <option value="reviewed" {{ request('status')==='reviewed'?'selected':'' }}>Reviewed</option>
                    <option value="accepted" {{ request('status')==='accepted'?'selected':'' }}>Accepted</option>
                    <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
                </select>
            </form>

            <div class="table-card">
                @if($applications->count())
                <table>
                    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($applications as $app)
                    <tr>
                        <td>{{ $app->id }}</td>
                        <td style="font-weight:600">{{ $app->full_name }}</td>
                        <td>{{ $app->email }}</td>
                        <td><span class="badge badge-{{ $app->role }}">{{ $app->role }}</span></td>
                        <td>{{ $app->phone }}</td>
                        <td><span class="badge badge-{{ $app->status }}">{{ $app->status }}</span></td>
                        <td>{{ $app->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="action-btn view" onclick="viewApplication({{ $app->id }})" title="View"><i class="fas fa-eye"></i></button>
                            <button class="action-btn delete" onclick="confirmDelete({{ $app->id }}, '{{ addslashes($app->full_name) }}')" title="Delete"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagination-wrap">
                    @if($applications->hasPages())
                        @if($applications->onFirstPage())<span class="disabled"><span class="page-link">&laquo;</span></span>@else <a class="page-link" href="{{ $applications->previousPageUrl() }}">&laquo;</a>@endif
                        @foreach($applications->getUrlRange(1, $applications->lastPage()) as $page => $url)<a class="page-link {{ $page == $applications->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>@endforeach
                        @if($applications->hasMorePages())<a class="page-link" href="{{ $applications->nextPageUrl() }}">&raquo;</a>@else <span class="disabled"><span class="page-link">&raquo;</span></span>@endif
                    @endif
                </div>
                @else
                <div class="empty-state"><i class="fas fa-inbox"></i><h3>No Applications Found</h3><p>No applications match your current filters.</p></div>
                @endif
            </div>
        </div>

        <!-- ════════════ DRIVERS ════════════ -->
        <div class="section-page" id="section-drivers">
            <div class="page-header">
                <div><h1><i class="fas fa-id-card"></i> Drivers Management</h1><p>Real-time driver data from the database</p></div>
                <button class="btn btn-primary" onclick="showAddDriverModal()"><i class="fas fa-plus"></i> Add Driver</button>
            </div>
            <div class="toolbar">
                <div class="search-wrap"><i class="fas fa-search"></i><input type="text" class="search-input" placeholder="Search drivers…" oninput="filterDrivers(this.value)"></div>
            </div>
            <div class="table-card">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>License</th><th>Experience</th><th>Active</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="driversTableBody"><tr><td colspan="8" class="loading">Select the Drivers tab to load data</td></tr></tbody>
                </table>
            </div>
        </div>

        <!-- ════════════ PARENTS ════════════ -->
        <div class="section-page" id="section-parents">
            <div class="page-header">
                <div><h1><i class="fas fa-users"></i> Parents Management</h1><p>Real-time parent data from the database</p></div>
                <button class="btn btn-primary" onclick="showAddParentModal()"><i class="fas fa-plus"></i> Add Parent</button>
            </div>
            <div class="toolbar">
                <div class="search-wrap"><i class="fas fa-search"></i><input type="text" class="search-input" placeholder="Search parents…" oninput="filterParents(this.value)"></div>
            </div>
            <div class="table-card">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Children</th><th>Actions</th></tr></thead>
                    <tbody id="parentsTableBody"><tr><td colspan="6" class="loading">Select the Parents tab to load data</td></tr></tbody>
                </table>
            </div>
        </div>

        <!-- ════════════ NOTIFICATIONS ════════════ -->
        <div class="section-page" id="section-notifications">
            <div class="page-header">
                <div><h1><i class="fas fa-bell"></i> Notifications</h1><p>Send and view system notifications</p></div>
                <button class="btn btn-primary" onclick="showSendNotificationModal()"><i class="fas fa-paper-plane"></i> Send Notification</button>
            </div>
            <div class="table-card">
                <table>
                    <thead><tr><th>Title</th><th>Message</th><th>Audience</th><th>Sent At</th></tr></thead>
                    <tbody id="notificationsTableBody"><tr><td colspan="4" class="loading">Select the Notifications tab to load data</td></tr></tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- ═══ APPLICATION DETAIL MODAL ═══ -->
<div class="modal-overlay" id="detailModal">
    <div class="modal">
        <div class="modal-header"><h3><i class="fas fa-user-circle"></i> Application Details</h3><button class="modal-close" onclick="closeModal('detailModal')">&times;</button></div>
        <div class="modal-body" id="detailContent"><p style="text-align:center;color:var(--text-secondary)">Loading…</p></div>
        <div class="modal-footer">
            <div style="display:flex;align-items:center;gap:8px;flex:1">
                <label style="font-size:12px;color:var(--text-secondary);white-space:nowrap">Status:</label>
                <select id="statusSelect" class="filter-select" style="min-width:130px;padding:7px 10px">
                    <option value="pending">Pending</option><option value="reviewed">Reviewed</option><option value="accepted">Accepted</option><option value="rejected">Rejected</option>
                </select>
                <button class="btn btn-primary" id="saveStatusBtn" onclick="saveStatus()"><i class="fas fa-save"></i> Save</button>
            </div>
            <button class="btn btn-secondary" onclick="closeModal('detailModal')">Close</button>
        </div>
    </div>
</div>

<!-- ═══ DELETE CONFIRM MODAL ═══ -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal" style="max-width:420px">
        <div class="modal-header"><h3><i class="fas fa-exclamation-triangle" style="color:var(--danger)"></i> Confirm Delete</h3><button class="modal-close" onclick="closeModal('deleteModal')">&times;</button></div>
        <div class="modal-body"><p>Delete the application from <strong id="deleteName"></strong>?</p><p style="margin-top:8px;font-size:12px;color:var(--text-secondary)">This cannot be undone.</p></div>
        <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button><button class="btn btn-danger" id="confirmDeleteBtn" onclick="executeDelete()"><i class="fas fa-trash"></i> Delete</button></div>
    </div>
</div>

<!-- ═══ DRIVER FORM MODAL ═══ -->
<div class="modal-overlay" id="driverModal">
    <div class="modal" style="max-width:680px">
        <div class="modal-header"><h3 id="driverFormTitle">Add Driver</h3><button class="modal-close" onclick="closeModal('driverModal')">&times;</button></div>
        <form id="driverForm" onsubmit="submitDriverForm(event)">
            <input type="hidden" id="driverFormId">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group"><label>Name *</label><input id="df_name" required></div>
                    <div class="form-group"><label>Email *</label><input id="df_email" type="email" required></div>
                    <div class="form-group"><label>Phone</label><input id="df_phone"></div>
                    <div class="form-group"><label>License Number</label><input id="df_license"></div>
                    <div class="form-group"><label>Years Experience</label><input id="df_experience" type="number" min="0"></div>
                    <div class="form-group"><label>Car Type</label><input id="df_car_type"></div>
                    <div class="form-group"><label>Car Model</label><input id="df_car_model"></div>
                    <div class="form-group"><label>Car Plate</label><input id="df_car_plate"></div>
                    <div class="form-group full"><label>Address</label><textarea id="df_address" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('driverModal')">Cancel</button><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button></div>
        </form>
    </div>
</div>

<!-- ═══ DRIVER DETAIL MODAL ═══ -->
<div class="modal-overlay" id="driverDetailModal">
    <div class="modal">
        <div class="modal-header"><h3><i class="fas fa-id-card"></i> Driver Details</h3><button class="modal-close" onclick="closeModal('driverDetailModal')">&times;</button></div>
        <div class="modal-body" id="driverDetailContent"></div>
        <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('driverDetailModal')">Close</button></div>
    </div>
</div>

<!-- ═══ PARENT FORM MODAL ═══ -->
<div class="modal-overlay" id="parentModal">
    <div class="modal" style="max-width:560px">
        <div class="modal-header"><h3 id="parentFormTitle">Add Parent</h3><button class="modal-close" onclick="closeModal('parentModal')">&times;</button></div>
        <form id="parentForm" onsubmit="submitParentForm(event)">
            <input type="hidden" id="parentFormId">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group"><label>Name *</label><input id="pf_name" required></div>
                    <div class="form-group"><label>Email *</label><input id="pf_email" type="email" required></div>
                    <div class="form-group"><label>Phone</label><input id="pf_phone"></div>
                    <div class="form-group full"><label>Address</label><textarea id="pf_address" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('parentModal')">Cancel</button><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button></div>
        </form>
    </div>
</div>

<!-- ═══ NOTIFICATION FORM MODAL ═══ -->
<div class="modal-overlay" id="notifModal">
    <div class="modal" style="max-width:520px">
        <div class="modal-header"><h3><i class="fas fa-paper-plane"></i> Send Notification</h3><button class="modal-close" onclick="closeModal('notifModal')">&times;</button></div>
        <form id="notifForm" onsubmit="submitNotification(event)">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full"><label>Title *</label><input id="nf_title" required></div>
                    <div class="form-group full"><label>Message *</label><textarea id="nf_message" required rows="3"></textarea></div>
                    <div class="form-group"><label>Audience</label><select id="nf_target"><option value="all">All Users</option><option value="parents">Parents</option><option value="drivers">Drivers</option></select></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('notifModal')">Cancel</button><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send</button></div>
        </form>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>
<script src="{{ asset('js/admin-unified.js') }}"></script>
</body>
</html>
