<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Applications - Driver Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0f172a; --bg-card: #1e293b; --border: #334155;
            --text: #f1f5f9; --text-dim: #94a3b8; --accent: #22c55e;
            --success: #22c55e; --warning: #f59e0b; --danger: #ef4444; --info: #6366f1;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

        .dash-header { background: var(--bg-card); border-bottom: 1px solid var(--border); padding: 20px 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .dash-header h1 { font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .dash-header h1 i { color: var(--accent); }
        .header-actions { display: flex; gap: 10px; }
        .header-btn { padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; border: 1px solid var(--border); color: var(--text-dim); background: transparent; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; }
        .header-btn:hover { background: rgba(255,255,255,.05); color: var(--text); }
        .header-btn.primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .header-btn.primary:hover { background: #16a34a; }

        .dash-content { max-width: 1100px; margin: 0 auto; padding: 32px 24px; }

        .app-cards { display: flex; flex-direction: column; gap: 16px; }
        .app-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; transition: all .2s; flex-wrap: wrap; position: relative; overflow: hidden; }
        .app-card:hover { border-color: rgba(34,197,94,.3); transform: translateY(-1px); }
        .app-card.latest::before { content: 'LATEST'; position: absolute; top: 8px; right: -28px; background: var(--accent); color: #fff; font-size: 9px; font-weight: 700; padding: 2px 32px; transform: rotate(45deg); letter-spacing: .05em; }
        .card-main { flex: 1; min-width: 200px; }
        .card-main h3 { font-size: 16px; font-weight: 600; margin-bottom: 6px; }
        .card-meta { display: flex; gap: 16px; flex-wrap: wrap; font-size: 12px; color: var(--text-dim); }
        .card-meta span { display: flex; align-items: center; gap: 4px; }
        .card-actions { display: flex; gap: 8px; align-items: center; }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
        .badge-pending { background: rgba(245,158,11,.15); color: #fbbf24; }
        .badge-reviewed { background: rgba(59,130,246,.15); color: #60a5fa; }
        .badge-accepted { background: rgba(34,197,94,.15); color: #4ade80; }
        .badge-rejected { background: rgba(239,68,68,.15); color: #f87171; }

        .view-btn { padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border); background: transparent; color: var(--text-dim); font-size: 12px; font-weight: 500; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; }
        .view-btn:hover { background: rgba(99,102,241,.1); color: var(--info); border-color: rgba(99,102,241,.3); }

        .empty-state { text-align: center; padding: 80px 20px; }
        .empty-state i { font-size: 56px; color: var(--border); margin-bottom: 16px; }
        .empty-state h3 { font-size: 20px; margin-bottom: 8px; }
        .empty-state p { color: var(--text-dim); font-size: 14px; margin-bottom: 24px; }

        .pagination-wrap { display: flex; justify-content: center; gap: 4px; margin-top: 24px; }
        .page-link { padding: 8px 14px; border-radius: 6px; font-size: 13px; color: var(--text-dim); text-decoration: none; border: 1px solid var(--border); transition: all .2s; }
        .page-link:hover { background: rgba(255,255,255,.05); }
        .page-link.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .disabled .page-link { opacity: .4; pointer-events: none; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(4px); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; width: 90%; max-width: 560px; max-height: 85vh; overflow-y: auto; }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 16px; font-weight: 700; }
        .modal-close { background: none; border: none; color: var(--text-dim); font-size: 20px; cursor: pointer; }
        .modal-body { padding: 24px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .detail-item { display: flex; flex-direction: column; gap: 4px; }
        .detail-item.full { grid-column: 1 / -1; }
        .detail-label { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-dim); }
        .detail-value { font-size: 14px; }
        .modal-footer { padding: 14px 24px; border-top: 1px solid var(--border); text-align: right; }
        .btn-close { padding: 8px 20px; border-radius: 8px; background: var(--border); color: var(--text); border: none; cursor: pointer; font-size: 13px; }

        @media (max-width: 640px) {
            .dash-header { padding: 16px; }
            .dash-content { padding: 20px 16px; }
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<!-- DEBUG: THIS IS THE REAL DRIVER APPLICATIONS PAGE -->
    <script>window.__API_TOKEN = '{{ $apiToken ?? '' }}';</script>
    <div class="dash-header">
        <h1><i class="fas fa-id-card"></i> Driver Dashboard — My Applications</h1>
        <div class="header-actions">
            <button class="header-btn primary" onclick="openAppModal()" type="button"><i class="fas fa-plus"></i> New Application</button>
            <a href="{{ url('/driver') }}" class="header-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="{{ url('/') }}" class="header-btn"><i class="fas fa-home"></i> Home</a>
            <a href="{{ url('/logout') }}" class="header-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="dash-content">
        @if($applications->count())
        <div class="app-cards">
            @foreach($applications as $index => $app)
            <div class="app-card {{ $index === 0 && $applications->currentPage() === 1 ? 'latest' : '' }}">
                <div class="card-main">
                    <h3>{{ $app->full_name }}</h3>
                    <div class="card-meta">
                        <span><i class="fas fa-envelope"></i> {{ $app->email }}</span>
                        <span><i class="fas fa-phone"></i> {{ $app->phone }}</span>
                        <span><i class="fas fa-calendar"></i> {{ $app->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="card-actions">
                    <span class="badge badge-{{ $app->status }}">{{ $app->status }}</span>
                    <button class="view-btn" onclick="viewApp({{ $app->id }})"><i class="fas fa-eye"></i> Details</button>
                </div>
            </div>
            @endforeach
        </div>
        @if($applications->hasPages())
        <div class="pagination-wrap">
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
        </div>
        @endif
        @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>No Applications Yet</h3>
            <p>You haven't submitted any driver applications. Start by applying now!</p>
            <button class="header-btn primary" onclick="openAppModal()" type="button"><i class="fas fa-plus"></i> Submit Application</button>
        </div>
        @endif
    </div>

    <!-- Detail Modal -->
    <div class="modal-overlay" id="detailModal">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-file-alt"></i> Application Details</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="detailContent"><p style="text-align:center;color:var(--text-dim)">Loading...</p></div>
            <div class="modal-footer"><button class="btn-close" onclick="closeModal()">Close</button></div>
        </div>
    </div>

    <!-- Application Form Modal -->
    <div class="modal-overlay" id="appFormModal">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle"></i> New Application</h3>
                <button class="modal-close" onclick="closeAppFormModal()">&times;</button>
            </div>
            <form id="appForm" class="ajax-form" action="/apply/submit" method="POST" style="padding:24px;">
                @csrf
                <input type="hidden" name="role" value="Driver">
                <div class="detail-grid" style="margin-bottom:14px;">
                    <div class="detail-item full">
                        <label class="detail-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="e.g., Ahmed Mohamed Hassan" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;">
                    </div>
                    <div class="detail-item full">
                        <label class="detail-label">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="your.email@example.com" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;">
                    </div>
                    <div class="detail-item full">
                        <label class="detail-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="+20 100 123 4567" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;">
                    </div>
                    <div class="detail-item full">
                        <label class="detail-label">Address</label>
                        <input type="text" name="address" class="form-control" required placeholder="Your address..." style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;">
                    </div>
                    <div class="detail-item full">
                        <label class="detail-label">Experience / Request Type</label>
                        <input type="text" name="experience" class="form-control" required placeholder="e.g., maintenance, leave, schedule..." style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;">
                    </div>
                    <div class="detail-item full">
                        <label class="detail-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Additional details..." style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;resize:vertical;"></textarea>
                    </div>
                </div>
                <div id="appFormError" style="color:var(--danger);font-size:13px;margin-bottom:10px;display:none;"></div>
                <div id="appFormSuccess" style="color:var(--success);font-size:13px;margin-bottom:10px;display:none;"></div>
                <div style="text-align:right;">
                    <button type="button" class="btn-close" onclick="closeAppFormModal()" style="margin-right:8px;">Cancel</button>
                    <button type="submit" class="header-btn primary" id="appFormSubmit" style="border:none;cursor:pointer;"><i class="fas fa-paper-plane"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>

<script>
function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));
}

function renderApplications(applications) {
    const content = document.querySelector('.dash-content');
    if (!content) return;
    if (!applications.length) {
        content.innerHTML = `<div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>No Applications Yet</h3>
            <p>No requests yet</p>
            <button class="header-btn primary" onclick="openAppModal()" type="button"><i class="fas fa-plus"></i> Submit Application</button>
        </div>`;
        return;
    }

    content.innerHTML = `<div class="app-cards">${applications.map((app, index) => `
        <div class="app-card ${index === 0 ? 'latest' : ''}">
            <div class="card-main">
                <h3>${escapeHtml(app.full_name)}</h3>
                <div class="card-meta">
                    <span><i class="fas fa-envelope"></i> ${escapeHtml(app.email)}</span>
                    <span><i class="fas fa-phone"></i> ${escapeHtml(app.phone)}</span>
                    <span><i class="fas fa-calendar"></i> ${escapeHtml((app.created_at || '').slice(0, 10))}</span>
                </div>
            </div>
            <div class="card-actions">
                <span class="badge badge-${escapeHtml(app.status)}">${escapeHtml(app.status)}</span>
                <button class="view-btn" onclick="viewApp(${app.id})"><i class="fas fa-eye"></i> Details</button>
            </div>
        </div>
    `).join('')}</div>`;
}

async function loadApplications() {
    if (!window.__API_TOKEN) return;
    try {
        const res = await fetch('/api/applications?role=driver', {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + window.__API_TOKEN
            }
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.status === 'success') {
            renderApplications(data.data || []);
        }
    } catch (error) {
        console.warn('Failed to refresh driver applications', error);
    }
}

setInterval(loadApplications, 10000);

function viewApp(id) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    content.innerHTML = '<p style="text-align:center;color:var(--text-dim)"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';
    modal.classList.add('active');

    fetch('/dashboard/driver/applications/' + id, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const d = res.data;
            let meta = '';
            if (d.metadata && Object.keys(d.metadata).length) {
                meta = '<div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border)"><div class="detail-label" style="margin-bottom:10px;color:var(--accent)">Vehicle Details</div><div class="detail-grid">';
                for (const [k,v] of Object.entries(d.metadata)) { if(v) meta += `<div class="detail-item"><div class="detail-label">${k.replace(/_/g,' ')}</div><div class="detail-value">${v}</div></div>`; }
                meta += '</div></div>';
            }
            content.innerHTML = `<div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Name</div><div class="detail-value">${d.full_name}</div></div>
                <div class="detail-item"><div class="detail-label">Email</div><div class="detail-value">${d.email}</div></div>
                <div class="detail-item"><div class="detail-label">Phone</div><div class="detail-value">${d.phone}</div></div>
                <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value"><span class="badge badge-${d.status}">${d.status}</span></div></div>
                <div class="detail-item full"><div class="detail-label">Address</div><div class="detail-value">${d.address}</div></div>
                <div class="detail-item full"><div class="detail-label">Experience</div><div class="detail-value">${d.experience}</div></div>
                ${d.notes ? `<div class="detail-item full"><div class="detail-label">Notes</div><div class="detail-value">${d.notes}</div></div>` : ''}
            </div>${meta}`;
        })
        .catch(() => { content.innerHTML = '<p style="color:var(--danger)">Failed to load.</p>'; });
}
function closeModal() { document.getElementById('detailModal').classList.remove('active'); }
document.getElementById('detailModal').addEventListener('click', e => { if (e.target.id === 'detailModal') closeModal(); });

function openAppModal() {
    document.getElementById('appFormError').style.display = 'none';
    document.getElementById('appFormSuccess').style.display = 'none';
    document.getElementById('appForm').reset();
    document.getElementById('appFormModal').classList.add('active');
}
function closeAppFormModal() { document.getElementById('appFormModal').classList.remove('active'); }
document.getElementById('appFormModal').addEventListener('click', e => { if (e.target.id === 'appFormModal') closeAppFormModal(); });

document.getElementById('appForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const errorBox = document.getElementById('appFormError');
    const successBox = document.getElementById('appFormSuccess');
    const submitBtn = document.getElementById('appFormSubmit');
    errorBox.style.display = 'none';
    successBox.style.display = 'none';
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    const formData = new FormData(this);
    try {
        const res = await fetch('/apply/submit', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            errorBox.textContent = data.message || 'Submission failed. Please check your input.';
            errorBox.style.display = 'block';
            return;
        }
        successBox.textContent = 'Application submitted successfully!';
        successBox.style.display = 'block';
        this.reset();
        loadApplications();
        const app = data.data;
        if (app && document.querySelector('.app-cards')) {
            const card = document.createElement('div');
            card.className = 'app-card latest';
            card.style.borderColor = 'rgba(34,197,94,.3)';
            card.innerHTML = `
                <div class="card-main">
                    <h3>${app.full_name}</h3>
                    <div class="card-meta">
                        <span><i class="fas fa-envelope"></i> ${app.email}</span>
                        <span><i class="fas fa-phone"></i> ${app.phone}</span>
                        <span><i class="fas fa-calendar"></i> Just now</span>
                    </div>
                </div>
                <div class="card-actions">
                    <span class="badge badge-${app.status}">${app.status}</span>
                    <button class="view-btn" onclick="viewApp(${app.id})"><i class="fas fa-eye"></i> Details</button>
                </div>`;
            document.querySelector('.app-cards').prepend(card);
            const emptyState = document.querySelector('.empty-state');
            if (emptyState) emptyState.remove();
        }
        setTimeout(() => closeAppFormModal(), 1200);
    } catch (err) {
        errorBox.textContent = 'Network error. Please try again.';
        errorBox.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit';
    }
});
</script>
<script src="{{ asset('js/ajax-forms.js') }}"></script>
</body>
</html>
