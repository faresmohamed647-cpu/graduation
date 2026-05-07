<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Entry - SAFESTEP BUS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --bg: #f4f7fb;
      --card: #ffffff;
      --text: #0f172a;
      --muted: #64748b;
      --border: #dbe4ee;
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --secondary: #0ea5a4;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: "Inter", system-ui, sans-serif;
      background: radial-gradient(circle at 12% 10%, rgba(37, 99, 235, 0.1), transparent 42%), var(--bg);
      color: var(--text);
      min-height: 100vh;
      padding: 24px 14px;
    }

    .container {
      max-width: 760px;
      margin: 0 auto;
    }

    .top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 16px;
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #334155;
      text-decoration: none;
      border: 1px solid var(--border);
      background: #fff;
      border-radius: 10px;
      padding: 10px 12px;
      font-weight: 600;
    }

    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08);
      overflow: hidden;
    }

    .card-header {
      padding: 22px 24px;
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: #fff;
    }

    .card-header h1 {
      font-size: 24px;
      margin-bottom: 6px;
    }

    .card-header p {
      font-size: 13px;
      opacity: 0.95;
    }

    .form {
      padding: 20px 24px 24px;
      display: grid;
      gap: 14px;
    }

    label {
      font-size: 13px;
      color: #334155;
      font-weight: 600;
    }

    .field {
      display: grid;
      gap: 8px;
    }

    input,
    textarea,
    select {
      width: 100%;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 11px 12px;
      font-size: 14px;
      font-family: inherit;
    }

    textarea {
      min-height: 110px;
      resize: vertical;
    }

    input:focus,
    textarea:focus,
    select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
    }

    .actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 4px;
    }

    .btn {
      border: none;
      border-radius: 10px;
      padding: 11px 14px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff;
    }

    .btn-secondary {
      background: #fff;
      color: #334155;
      border: 1px solid var(--border);
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="top">
      <a class="back-link" href="/admin">
        <i class="fas fa-arrow-left"></i>
        Back To Admin
      </a>
    </div>

    <div class="card">
      <div class="card-header">
        <h1 id="pageTitle">Add Entry</h1>
        <p id="pageSubtitle">Create a new entry and submit it to admin records.</p>
      </div>

      <form class="form ajax-form" id="addEntryForm">
        <div class="field">
          <label for="entryName">Name</label>
          <input id="entryName" type="text" required placeholder="Enter name">
        </div>

        <div class="field">
          <label for="entryCode">Reference Code</label>
          <input id="entryCode" type="text" placeholder="Optional code">
        </div>

        <div class="field">
          <label for="entryStatus">Status</label>
          <select id="entryStatus">
            <option value="active">Active</option>
            <option value="pending">Pending</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <div class="field">
          <label for="entryNotes">Notes</label>
          <textarea id="entryNotes" placeholder="Add details here..."></textarea>
        </div>

        <div class="actions">
          <a class="btn btn-secondary" href="/admin">Cancel</a>
          <button class="btn btn-primary" type="submit">
            <i class="fas fa-save"></i>
            Save
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const typeLabels = {
      bus: 'Bus',
      financial: 'Financial Entry',
      maintenance: 'Maintenance Record',
      student: 'Student',
      trip: 'Trip',
      complaint: 'Complaint',
      school: 'School',
      user: 'User'
    };

    const params = new URLSearchParams(window.location.search);
    const type = params.get('type') || 'entry';
    const label = typeLabels[type] || 'Entry';

    const title = document.getElementById('pageTitle');
    const subtitle = document.getElementById('pageSubtitle');
    if (title) title.textContent = `Add ${label}`;
    if (subtitle) subtitle.textContent = `Create a new ${label.toLowerCase()} and return to admin dashboard.`;

    const form = document.getElementById('addEntryForm');
    form?.addEventListener('submit', async function (event) {
      event.preventDefault();
      const token = localStorage.getItem('token') || localStorage.getItem('safestep_token');
      const payload = {
        request_type: `admin-${type}-entry`,
        subject: document.getElementById('entryName').value.trim() || `${label} Entry`,
        description: document.getElementById('entryNotes').value.trim() || 'No extra notes provided.',
        context: {
          code: document.getElementById('entryCode').value.trim(),
          status: document.getElementById('entryStatus').value
        }
      };

      try {
        const res = await fetch('/api/requests', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': token ? `Bearer ${token}` : ''
          },
          body: JSON.stringify(payload)
        });

        if (res.status === 401) {
          localStorage.removeItem('token');
          localStorage.removeItem('safestep_token');
          window.location.href = '/login';
          return;
        }

        if (res.status === 403) {
          alert('Access Denied');
          return;
        }

        if (!res.ok) {
          throw new Error('Failed to save entry');
        }

        alert(`${label} saved successfully.`);
        window.location.href = '/admin';
      } catch (error) {
        alert(error.message || 'Failed to save entry.');
      }
    });
  </script>
  <script src="{{ asset('js/ajax-forms.js') }}"></script>
</body>
</html>
