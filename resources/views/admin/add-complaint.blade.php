<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Complaint</title>
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
    renderAddForm({ title: 'Add Complaint', subtitle: 'Create a new complaint ticket.', fields: [
      { name: 'submittedBy', label: 'Submitted By', required: true, width: 'half' },
      { name: 'type', label: 'Type', type: 'select', required: true, options: [{ value: 'service', label: 'Service' }, { value: 'driver', label: 'Driver' }, { value: 'bus', label: 'Bus' }, { value: 'safety', label: 'Safety' }, { value: 'other', label: 'Other' }], width: 'half' },
      { name: 'priority', label: 'Priority', type: 'select', required: true, options: [{ value: 'low', label: 'Low' }, { value: 'medium', label: 'Medium' }, { value: 'high', label: 'High' }], width: 'half' },
      { name: 'status', label: 'Status', type: 'select', required: true, options: [{ value: 'open', label: 'Open' }, { value: 'in-progress', label: 'In Progress' }, { value: 'resolved', label: 'Resolved' }, { value: 'closed', label: 'Closed' }], width: 'half' },
      { name: 'subject', label: 'Subject', required: true },
      { name: 'details', label: 'Details', type: 'textarea', required: true }
    ]});
  </script>
</body>
</html>
