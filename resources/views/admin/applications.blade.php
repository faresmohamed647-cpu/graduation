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
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg-primary: #0f172a; --bg-secondary: #1e293b; --bg-card: #1e293b;
            --bg-hover: #334155; --border: #334155; --text-primary: #f1f5f9;
            --text-secondary: #94a3b8; --accent: #6366f1; --accent-hover: #818cf8;
            --success: #22c55e; --warning: #f59e0b; --danger: #ef4444; --info: #3b82f6;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; }
        .layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 260px; background: var(--bg-secondary); border-right: 1px solid var(--border); padding: 24px 0; display: flex; flex-direction: column; position: fixed; height: 100vh; overflow-y: auto; z-index: 100; }
        .sidebar-brand { padding: 0 24px 24px; border-bottom: 1px solid var(--border); margin-bottom: 16px; }
        .sidebar-brand h2 { font-size: 18px; font-weight: 800; background: linear-gradient(135deg, var(--accent), #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-brand span { font-size: 11px; color: var(--text-secondary); }
        .nav-items { list-style: none; padding: 0 12px; flex: 1; }
        .nav-items a { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; color: var(--text-secondary); text-decoration: none; font-size: 13px; font-weight: 500; transition: all .2s; margin-bottom: 2px; }
        .nav-items a:hover { background: var(--bg-hover); color: var(--text-primary); }
        .nav-items a.active { background: linear-gradient(135deg, rgba(99,102,241,.2), rgba(139,92,246,.1)); color: var(--accent-hover); border: 1px solid rgba(99,102,241,.3); }
        .nav-items a i { width: 18px; text-align: center; font-size: 14px; }

        /* Main */
        .main { flex: 1; margin-left: 260px; padding: 24px 32px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
        .page-header h1 { font-size: 24px; font-weight: 700; }
        .page-header p { font-size: 13px; color: var(--text-secondary); margin-top: 4px; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; color: var(--text-secondary); text-decoration: none; font-size: 13px; padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border); transition: all .2s; background: transparent; cursor: pointer; }
        .back-btn:hover { background: var(--bg-hover); color: var(--text-primary); }

        /* Stats */
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .stat-icon.purple { background: rgba(99,102,241,.15); color: var(--accent); }
        .stat-icon.amber { background: rgba(245,158,11,.15); color: var(--warning); }
        .stat-icon.green { background: rgba(34,197,94,.15); color: var(--success); }
        .stat-icon.red { background: rgba(239,68,68,.15); color: var(--danger); }
        .stat-card h3 { font-size: 24px; font-weight: 700; }
        .stat-card span { font-size: 12px; color: var(--text-secondary); }

        /* Toolbar */
        .toolbar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .search-input { flex: 1; min-width: 240px; padding: 10px 14px 10px 38px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); font-size: 13px; outline: none; transition: border-color .2s; }
        .search-input:focus { border-color: var(--accent); }
        .search-wrap { position: relative; flex: 1; min-width: 240px; }
        .search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); font-size: 14px; }
        .filter-select { padding: 10px 14px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); font-size: 13px; outline: none; cursor: pointer; min-width: 140px; }
        .filter-select:focus { border-color: var(--accent); }

        /* Table */
        .table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 14px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary); background: rgba(15,23,42,.5); border-bottom: 1px solid var(--border); }
        tbody td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tbody tr { transition: background .15s; }
        tbody tr:hover { background: var(--bg-hover); }
        tbody tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
        .badge-pending { background: rgba(245,158,11,.15); color: #fbbf24; }
        .badge-reviewed { background: rgba(59,130,246,.15); color: #60a5fa; }
        .badge-accepted { background: rgba(34,197,94,.15); color: #4ade80; }
        .badge-rejected { background: rgba(239,68,68,.15); color: #f87171; }
        .badge-parent { background: rgba(59,130,246,.12); color: #60a5fa; }
        .badge-driver { background: rgba(34,197,94,.12); color: #4ade80; }
        .badge-admin { background: rgba(168,85,247,.12); color: #c084fc; }

        /* Action buttons */
        .action-btn { padding: 6px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; transition: all .2s; background: transparent; }
        .action-btn.view { color: var(--info); }
        .action-btn.view:hover { background: rgba(59,130,246,.15); }
        .action-btn.delete { color: var(--danger); }
        .action-btn.delete:hover { background: rgba(239,68,68,.15); }

        /* Pagination */
        .pagination-wrap { padding: 16px; display: flex; justify-content: center; align-items: center; gap: 4px; }
        .pagination-wrap .page-link { padding: 8px 14px; border-radius: 6px; font-size: 13px; color: var(--text-secondary); text-decoration: none; border: 1px solid var(--border); transition: all .2s; background: transparent; }
        .pagination-wrap .page-link:hover { background: var(--bg-hover); color: var(--text-primary); }
        .pagination-wrap .page-link.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .pagination-wrap .disabled .page-link { opacity: .4; pointer-events: none; }

        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(4px); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; animation: fadeIn .2s; }
        .modal { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 16px; width: 90%; max-width: 600px; max-height: 85vh; overflow-y: auto; }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 18px; font-weight: 700; }
        .modal-close { background: none; border: none; color: var(--text-secondary); font-size: 20px; cursor: pointer; padding: 4px; border-radius: 6px; transition: all .2s; }
        .modal-close:hover { background: var(--bg-hover); color: var(--text-primary); }
        .modal-body { padding: 24px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .detail-item { display: flex; flex-direction: column; gap: 4px; }
        .detail-item.full { grid-column: 1 / -1; }
        .detail-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary); }
        .detail-value { font-size: 14px; color: var(--text-primary); word-break: break-word; }
        .meta-section { margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
        .meta-section h4 { font-size: 14px; font-weight: 600; color: var(--accent-hover); margin-bottom: 12px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 8px; }
        .btn { padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all .2s; }
        .btn-secondary { background: var(--bg-hover); color: var(--text-primary); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #dc2626; }

        /* Toast */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 2000; display: flex; flex-direction: column; gap: 8px; }
        .toast { padding: 12px 20px; border-radius: 8px; font-size: 13px; font-weight: 500; animation: slideIn .3s; color: #fff; }
        .toast.success { background: var(--success); }
        .toast.error { background: var(--danger); }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-secondary); }
        .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: .5; }
        .empty-state h3 { font-size: 18px; margin-bottom: 8px; color: var(--text-primary); }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { transform: translateX(100px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; padding: 16px; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
            .modal { width: 95%; }
        }
    </style>
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h2><i class="fas fa-shield-alt"></i> SAFESTEP BUS</h2>
            <span>Admin Panel</span>
        </div>
        <ul class="nav-items">
            <a href="{{ url('/admin') }}"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="{{ url('/admin/applications') }}" class="active"><i class="fas fa-file-alt"></i> Applications</a>
            <a href="{{ url('/admin') }}" onclick="navigateTo && navigateTo('parents')"><i class="fas fa-users"></i> Parents</a>
            <a href="{{ url('/admin') }}" onclick="navigateTo && navigateTo('drivers')"><i class="fas fa-id-card"></i> Drivers</a>
            <a href="{{ url('/admin') }}" onclick="navigateTo && navigateTo('buses')"><i class="fas fa-bus"></i> Buses</a>
            <a href="{{ url('/admin') }}" onclick="navigateTo && navigateTo('students')"><i class="fas fa-graduation-cap"></i> Students</a>
            <a href="{{ url('/admin') }}" onclick="navigateTo && navigateTo('trips')"><i class="fas fa-route"></i> Trips</a>
            <a href="{{ url('/admin') }}" onclick="navigateTo && navigateTo('reports')"><i class="fas fa-file-alt"></i> Reports</a>
            <a href="{{ url('/logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-file-alt"></i> Applications Management</h1>
                <p>Review and manage all submitted applications</p>
            </div>
            <a href="{{ url('/admin') }}" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-layer-group"></i></div>
                <div><h3>{{ $stats['total'] }}</h3><span>Total Applications</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fas fa-clock"></i></div>
                <div><h3>{{ $stats['pending'] }}</h3><span>Pending Review</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div><h3>{{ $stats['accepted'] }}</h3><span>Accepted</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
                <div><h3>{{ $stats['rejected'] }}</h3><span>Rejected</span></div>
            </div>
        </div>

        <!-- Toolbar -->
        <form method="GET" action="{{ url('/admin/applications') }}" class="toolbar" id="filterForm">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="search-input" placeholder="Search by name, phone, or email..." value="{{ request('search') }}">
            </div>
            <select name="role" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="all" {{ request('role') === 'all' || !request('role') ? 'selected' : '' }}>All Roles</option>
                <option value="parent" {{ request('role') === 'parent' ? 'selected' : '' }}>Parent</option>
                <option value="driver" {{ request('role') === 'driver' ? 'selected' : '' }}>Driver</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <select name="status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>

        <!-- Table -->
        <div class="table-card">
            @if($applications->count())
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Status</th><th>Created At</th><th>Actions</th>
                    </tr>
                </thead>
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
                            <button class="action-btn view" onclick="viewApplication({{ $app->id }})" title="View Details"><i class="fas fa-eye"></i></button>
                            <button class="action-btn delete" onclick="confirmDelete({{ $app->id }}, '{{ addslashes($app->full_name) }}')" title="Delete"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination-wrap">
                @if($applications->hasPages())
                    @if($applications->onFirstPage())
                        <span class="disabled"><span class="page-link">&laquo;</span></span>
                    @else
                        <a class="page-link" href="{{ $applications->previousPageUrl() }}">&laquo;</a>
                    @endif
                    @foreach($applications->getUrlRange(1, $applications->lastPage()) as $page => $url)
                        <a class="page-link {{ $page == $applications->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    @if($applications->hasMorePages())
                        <a class="page-link" href="{{ $applications->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span class="disabled"><span class="page-link">&raquo;</span></span>
                    @endif
                @endif
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Applications Found</h3>
                <p>No applications match your current filters.</p>
            </div>
            @endif
        </div>
    </main>
</div>

<!-- Detail Modal -->
<div class="modal-overlay" id="detailModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-circle"></i> Application Details</h3>
            <button class="modal-close" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="modal-body" id="detailContent">
            <p style="text-align:center;color:var(--text-secondary)">Loading...</p>
        </div>
        <div class="modal-footer">
            <div style="display:flex;align-items:center;gap:8px;flex:1">
                <label style="font-size:12px;color:var(--text-secondary);white-space:nowrap">Update Status:</label>
                <select id="statusSelect" class="filter-select" style="min-width:130px;padding:7px 10px;font-size:13px">
                    <option value="pending">Pending</option>
                    <option value="reviewed">Reviewed</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>
                <button class="btn" id="saveStatusBtn" style="background:var(--accent);color:#fff" onclick="saveStatus()">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
            <button class="btn btn-secondary" onclick="closeModal('detailModal')">Close</button>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle" style="color:var(--danger)"></i> Confirm Delete</h3>
            <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the application from <strong id="deleteName"></strong>?</p>
            <p style="margin-top:8px;font-size:12px;color:var(--text-secondary)">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn" onclick="executeDelete()"><i class="fas fa-trash"></i> Delete</button>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
let deleteId = null;
let currentApplicationId = null;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function viewApplication(id) {
    currentApplicationId = id;
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    content.innerHTML = '<p style="text-align:center;color:var(--text-secondary)"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';
    modal.classList.add('active');

    fetch('/admin/applications/' + id, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const d = res.data;
            // Pre-select current status in dropdown
            document.getElementById('statusSelect').value = d.status;

            let metaHtml = '';
            if (d.metadata && Object.keys(d.metadata).length > 0) {
                metaHtml = '<div class="meta-section"><h4><i class="fas fa-tags"></i> Role-Specific Details</h4><div class="detail-grid">';
                for (const [key, val] of Object.entries(d.metadata)) {
                    if (val) metaHtml += `<div class="detail-item"><div class="detail-label">${key.replace(/_/g,' ')}</div><div class="detail-value">${val}</div></div>`;
                }
                metaHtml += '</div></div>';
            }
            content.innerHTML = `
                <div class="detail-grid">
                    <div class="detail-item"><div class="detail-label">Full Name</div><div class="detail-value">${d.full_name}</div></div>
                    <div class="detail-item"><div class="detail-label">Email</div><div class="detail-value">${d.email}</div></div>
                    <div class="detail-item"><div class="detail-label">Phone</div><div class="detail-value">${d.phone}</div></div>
                    <div class="detail-item"><div class="detail-label">Role</div><div class="detail-value"><span class="badge badge-${d.role}">${d.role}</span></div></div>
                    <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value"><span class="badge badge-${d.status}" id="currentStatusBadge">${d.status}</span></div></div>
                    <div class="detail-item"><div class="detail-label">Submitted</div><div class="detail-value">${d.created_at}</div></div>
                    <div class="detail-item full"><div class="detail-label">Address</div><div class="detail-value">${d.address}</div></div>
                    <div class="detail-item full"><div class="detail-label">Experience</div><div class="detail-value">${d.experience}</div></div>
                    ${d.notes ? `<div class="detail-item full"><div class="detail-label">Notes</div><div class="detail-value">${d.notes}</div></div>` : ''}
                </div>
                ${metaHtml}
            `;
        })
        .catch(() => { content.innerHTML = '<p style="color:var(--danger)">Failed to load details.</p>'; });
}

function saveStatus() {
    if (!currentApplicationId) return;
    const status = document.getElementById('statusSelect').value;
    const btn = document.getElementById('saveStatusBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch('/admin/applications/' + currentApplicationId + '/status', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ status }),
    })
    .then(r => r.json())
    .then(res => {
        showToast('success', res.message || 'Status updated');
        // Update badge in modal
        const badge = document.getElementById('currentStatusBadge');
        if (badge) {
            badge.className = 'badge badge-' + status;
            badge.textContent = status;
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save';
        // Reload table in background after short delay
        setTimeout(() => location.reload(), 1200);
    })
    .catch(() => {
        showToast('error', 'Failed to update status');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save';
    });
}

function confirmDelete(id, name) {
    deleteId = id;
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteModal').classList.add('active');
}

function executeDelete() {
    if (!deleteId) return;
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

    fetch('/admin/applications/' + deleteId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(res => {
        closeModal('deleteModal');
        showToast('success', res.message || 'Deleted successfully');
        setTimeout(() => location.reload(), 800);
    })
    .catch(() => {
        showToast('error', 'Failed to delete');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash"></i> Delete';
    });
}

function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function showToast(type, message) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// Debounced search
let searchTimeout;
document.querySelector('.search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => document.getElementById('filterForm').submit(), 500);
});

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('active'); });
});
</script>
</body>
</html>
