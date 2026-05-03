<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Bus</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/add-form.css') }}">
</head>
<body>
  <div class="container"><div class="top"><a class="back-link" href="/admin"><i class="fas fa-arrow-left"></i> Back To Admin</a></div><div class="card"><div class="card-header"><h1 id="pageTitle"></h1><p id="pageSubtitle"></p></div><form class="form" id="addForm"></form></div></div>
  <script src="{{ asset('js/add-form.js') }}"></script>
  <script>
    renderAddForm({ title: 'Add Bus', subtitle: 'Create a new bus record.', fields: [
      { name: 'busNumber', label: 'Bus Number', required: true, width: 'half' },
      { name: 'plateNumber', label: 'Plate Number', required: true, width: 'half' },
      { name: 'capacity', label: 'Capacity', type: 'number', required: true, width: 'half' },
      { name: 'route', label: 'Route', placeholder: 'Route A', width: 'half' },
      { name: 'assignedDriver', label: 'Assigned Driver', placeholder: 'Driver name', width: 'half' },
      { name: 'status', label: 'Status', type: 'select', required: true, options: [{ value: 'active', label: 'Active' }, { value: 'maintenance', label: 'Maintenance' }, { value: 'inactive', label: 'Inactive' }] },
      { name: 'notes', label: 'Notes', type: 'textarea' }
    ]});
  </script>
</body>
</html>
