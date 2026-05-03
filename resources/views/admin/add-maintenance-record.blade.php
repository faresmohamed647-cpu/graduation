<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Maintenance Record</title>
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
    renderAddForm({ title: 'Add Maintenance Record', subtitle: 'Create a maintenance log for a bus.', fields: [
      { name: 'busNumber', label: 'Bus Number', required: true, width: 'half' },
      { name: 'date', label: 'Date', type: 'date', required: true, width: 'half' },
      { name: 'type', label: 'Maintenance Type', type: 'select', required: true, options: [{ value: 'repair', label: 'Repair' }, { value: 'maintenance', label: 'Maintenance' }, { value: 'inspection', label: 'Inspection' }] },
      { name: 'cost', label: 'Cost', type: 'number', width: 'half' },
      { name: 'technician', label: 'Technician', width: 'half' },
      { name: 'description', label: 'Description', type: 'textarea', required: true }
    ]});
  </script>
</body>
</html>
