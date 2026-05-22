function renderAddForm(config) {
  const titleNode = document.getElementById('pageTitle');
  const subtitleNode = document.getElementById('pageSubtitle');
  const formNode = document.getElementById('addForm');

  if (!formNode || !config) return;

  if (titleNode) titleNode.textContent = config.title || 'Add Entry';
  if (subtitleNode) subtitleNode.textContent = config.subtitle || 'Fill required fields and save.';

  const rows = [];
  let rowBuffer = [];

  (config.fields || []).forEach((field) => {
    const isHalf = field.width === 'half';
    const fieldHtml = buildField(field);
    if (isHalf) {
      rowBuffer.push(fieldHtml);
      if (rowBuffer.length === 2) {
        rows.push(`<div class="row">${rowBuffer.join('')}</div>`);
        rowBuffer = [];
      }
    } else {
      if (rowBuffer.length) {
        rows.push(`<div class="row">${rowBuffer.join('')}</div>`);
        rowBuffer = [];
      }
      rows.push(fieldHtml);
    }
  });

  if (rowBuffer.length) {
    rows.push(`<div class="row">${rowBuffer.join('')}</div>`);
  }

  formNode.innerHTML = `
    ${rows.join('')}
    <div class="actions">
      <a class="btn btn-secondary" href="/admin">Cancel</a>
      <button class="btn btn-primary" type="submit">
        <i class="fas fa-save"></i>
        Save
      </button>
    </div>
  `;

  formNode.addEventListener('submit', async (event) => {
    event.preventDefault();

    // Collect form values (inputs/selects/textareas share `name` in our builder)
    const entry = {};
    formNode.querySelectorAll('input[name], select[name], textarea[name]').forEach((el) => {
      entry[el.name] = el.value;
    });

    if (config?.entityType === 'student') {
      try {
        localStorage.setItem('pending_student', JSON.stringify(entry));
      } catch (e) {
        // If storage is blocked, we still continue with the normal redirect.
        console.warn('Could not store pending_student:', e);
      }
    }

    try {
      const { endpoint, body } = await buildRequestFromConfig(config, entry);
      const response = await safestepApi(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
      });

      if (!response?.success) {
        throw new Error(response?.message || 'Save failed');
      }

      alert(`${config.title || 'Entry'} saved successfully.`);
      window.location.href = '/admin';
    } catch (error) {
      alert(error.message || 'Failed to save entry.');
    }
  });
}

async function buildRequestFromConfig(config, entry) {
  const title = String(config?.title || '').toLowerCase();

  if (title.includes('add parent')) {
    return {
      endpoint: '/api/admin/parents',
      body: {
        name: entry.fullName,
        email: entry.email,
        phone: entry.phone,
        student_count: Number(entry.children?.split(',').filter(Boolean).length || 1),
        active: entry.status !== 'inactive',
        message: entry.notes || ''
      }
    };
  }

  if (title.includes('add school')) {
    const extraNotes = `District: ${entry.district || ''}, Type: ${entry.type || ''}, Status: ${entry.status || ''}, Students: ${entry.students || ''}`.trim();
    return {
      endpoint: '/api/admin/schools',
      body: {
        name: entry.schoolName || entry.name,
        address: entry.address,
        phone: entry.contact || entry.contactPhone || entry.phone,
        email: entry.email,
        notes: extraNotes + (entry.notes ? `. ${entry.notes}` : '')
      }
    };
  }

  if (title.includes('financial')) {
    const resolvedType = (entry.type === 'income' || entry.type === 'profit') ? 'income' : 'expense';
    const resolvedTitle = entry.title || (entry.description && entry.description.length <= 100 ? entry.description : (entry.type ? (entry.type.charAt(0).toUpperCase() + entry.type.slice(1) + ' Entry') : 'Financial Entry'));
    return {
      endpoint: '/api/admin/financial-entries',
      body: {
        title: resolvedTitle,
        type: resolvedType,
        amount: normalizeFloat(entry.amount),
        description: entry.description,
        entry_date: entry.date || entry.entry_date
      }
    };
  }

  if (title.includes('maintenance')) {
    let busId = null;
    if (entry.busNumber) {
      try {
        const busesRes = await safestepApi('/api/admin/buses?per_page=all');
        const matchedBus = (busesRes?.data || []).find((b) => {
          const bNum = norm(b.bus_number);
          const eBus = norm(entry.busNumber);
          if (bNum === eBus) return true;
          const bDigits = b.bus_number.replace(/[^0-9]/g, '');
          const eDigits = entry.busNumber.replace(/[^0-9]/g, '');
          if (bDigits && eDigits && bDigits === eDigits) return true;
          if (bNum.includes(eBus) || eBus.includes(bNum)) return true;
          return false;
        });
        if (matchedBus) {
          busId = matchedBus.id;
        }
      } catch (e) {
        console.warn('Could not fetch buses for maintenance record:', e);
      }
    }

    const resolvedTitle = entry.title || (entry.type ? (entry.type.charAt(0).toUpperCase() + entry.type.slice(1) + ' Record') : 'Maintenance Record');
    const fullDesc = `Technician: ${entry.technician || 'N/A'}. ${entry.description || ''}`.trim();

    return {
      endpoint: '/api/admin/maintenance-records',
      body: {
        bus_id: busId,
        title: resolvedTitle,
        description: fullDesc,
        cost: normalizeFloat(entry.cost),
        status: entry.status || 'pending',
        maintenance_date: entry.date || entry.maintenance_date
      }
    };
  }

  if (title.includes('complaint')) {
    const resolvedTitle = entry.subject || entry.title || (entry.type ? (entry.type.charAt(0).toUpperCase() + entry.type.slice(1) + ' Complaint') : 'New Complaint');
    const resolvedBody = `Submitted By: ${entry.submittedBy || 'N/A'}\nPriority: ${entry.priority || 'N/A'}\nDetails: ${entry.details || entry.description || entry.body || ''}`;
    const resolvedStatus = (entry.status === 'in-progress') ? 'open' : (entry.status || 'open');

    return {
      endpoint: '/api/admin/reports',
      body: {
        type: 'complaint',
        title: resolvedTitle,
        body: resolvedBody,
        status: resolvedStatus
      }
    };
  }

  if (title.includes('add driver')) {
    const messageVal = `Assigned Bus: ${entry.assignedBus || 'None'}. Notes: ${entry.notes || ''}`.trim();
    return {
      endpoint: '/api/admin/drivers',
      body: {
        name: entry.fullName,
        email: entry.email || `${slugify(entry.fullName)}.${Date.now()}@safestep.local`,
        phone: entry.phone,
        license_number: entry.license,
        years_experience: normalizeNumber(entry.experience),
        active: entry.status === 'active',
        message: messageVal
      }
    };
  }

  if (title.includes('add bus')) {
    return {
      endpoint: '/api/admin/buses',
      body: {
        bus_number: entry.busNumber,
        plate_number: entry.plateNumber,
        capacity: Math.max(1, normalizeNumber(entry.capacity)),
        active: entry.status === 'active'
      }
    };
  }

  if (title.includes('add user')) {
    const allowedRole = ['admin', 'driver', 'parent'].includes(entry.role) ? entry.role : 'parent';
    return {
      endpoint: '/api/admin/users',
      body: {
        name: entry.fullName,
        email: entry.email,
        role: allowedRole,
        password: 'password'
      }
    };
  }

  if (title.includes('add student')) {
    const parentRes = await safestepApi('/api/admin/parents?per_page=all');
    const match = (parentRes?.data || []).find((parent) =>
      norm(parent.name) === norm(entry.parent) || 
      norm(parent.user?.name) === norm(entry.parent)
    );
    if (!match) throw new Error('Parent not found. Please add parent first.');

    return {
      endpoint: '/api/admin/students',
      body: {
        full_name: entry.fullName,
        parent_id: match.id,
        grade: entry.grade,
        school_name: entry.school,
        active: entry.status === 'active'
      }
    };
  }

  if (title.includes('add trip')) {
    const [drivers, buses, routes] = await Promise.all([
      safestepApi('/api/admin/drivers?per_page=all'),
      safestepApi('/api/admin/buses?per_page=all'),
      safestepApi('/api/admin/routes')
    ]);
    
    const driver = (drivers?.data || []).find((d) => {
      const dName = norm(d.name);
      const eDriver = norm(entry.driver);
      return dName === eDriver || dName.includes(eDriver) || eDriver.includes(dName);
    });

    const bus = (buses?.data || []).find((b) => {
      const bNum = norm(b.bus_number);
      const eBus = norm(entry.bus);
      if (bNum === eBus) return true;
      const bDigits = b.bus_number.replace(/[^0-9]/g, '');
      const eDigits = entry.bus.replace(/[^0-9]/g, '');
      if (bDigits && eDigits && bDigits === eDigits) return true;
      return bNum.includes(eBus) || eBus.includes(bNum);
    });

    const route = (routes?.data || []).find((r) => {
      const rName = norm(r.name);
      const eRoute = norm(entry.routeName);
      return rName === eRoute || rName.includes(eRoute) || eRoute.includes(rName);
    });

    if (!driver || !bus || !route) {
      throw new Error('Trip needs valid driver, bus, and route names.');
    }

    const hour = parseInt(String(entry.startTime || '').split(':')[0], 10) || 8;
    const inferredShift = (hour < 12) ? 'morning' : 'afternoon';

    return {
      endpoint: '/api/admin/trips',
      body: {
        driver_id: driver.id,
        bus_id: bus.id,
        bus_route_id: route.id,
        trip_date: entry.date,
        shift: inferredShift,
        status: normalizeTripStatus(entry.status)
      }
    };
  }

  return {
    endpoint: '/api/requests',
    body: {
      request_type: (config?.title || 'admin-entry').toLowerCase().replace(/\s+/g, '-'),
      subject: config?.title || 'Admin Entry',
      description: `${config?.subtitle || 'New entry'} submitted from dashboard form.`,
      context: entry
    }
  };
}

const norm = (s) => String(s || '').toLowerCase().replace(/[^a-z0-9]/g, '');

function normalizeNumber(value) {
  const match = String(value || '').match(/\d+/);
  return match ? parseInt(match[0], 10) : 0;
}

function normalizeFloat(value) {
  const n = parseFloat(value);
  return Number.isFinite(n) ? n : 0.0;
}

function slugify(value) {
  return String(value || 'user').toLowerCase().replace(/[^a-z0-9]+/g, '.').replace(/^\.+|\.+$/g, '') || 'user';
}

function normalizeTripStatus(value) {
  if (value === 'in-progress') return 'active';
  if (value === 'scheduled') return 'assigned';
  return value || 'assigned';
}


async function safestepApi(url, options = {}) {
  const token = localStorage.getItem('token') || localStorage.getItem('safestep_token');
  const response = await fetch(url, {
    credentials: 'same-origin',
    ...options,
    headers: {
      Accept: 'application/json',
      Authorization: token ? `Bearer ${token}` : '',
      ...(options.headers || {})
    }
  });

  if (response.status === 401) {
    localStorage.removeItem('token');
    localStorage.removeItem('safestep_token');
    window.location.href = '/login';
    throw new Error('Session expired. Please login again.');
  }

  if (response.status === 403) {
    throw new Error('Access Denied');
  }

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    const firstError = data?.errors ? Object.values(data.errors)?.[0]?.[0] : null;
    throw new Error(firstError || data?.message || `Request failed (${response.status})`);
  }

  return data;
}

function buildField(field) {
  const required = field.required ? 'required' : '';
  const placeholder = field.placeholder ? `placeholder="${field.placeholder}"` : '';
  const value = field.value ? `value="${field.value}"` : '';
  const id = field.name;
  const widthClass = field.width === 'half' ? 'field half' : 'field';

  if (field.type === 'textarea') {
    return `
      <div class="${widthClass}">
        <label for="${id}">${field.label}</label>
        <textarea id="${id}" name="${id}" ${placeholder} ${required}></textarea>
      </div>
    `;
  }

  if (field.type === 'select') {
    const options = (field.options || [])
      .map((option) => `<option value="${option.value}">${option.label}</option>`)
      .join('');
    return `
      <div class="${widthClass}">
        <label for="${id}">${field.label}</label>
        <select id="${id}" name="${id}" ${required}>
          ${options}
        </select>
      </div>
    `;
  }

  return `
    <div class="${widthClass}">
      <label for="${id}">${field.label}</label>
      <input id="${id}" name="${id}" type="${field.type || 'text'}" ${placeholder} ${value} ${required}>
    </div>
  `;
}
