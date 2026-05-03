<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Trip</title>
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
    renderAddForm({ title: 'Add Trip', subtitle: 'Create a new route trip.', fields: [
      { name: 'tripId', label: 'Trip ID', required: true, width: 'half' },
      { name: 'routeName', label: 'Route Name', required: true, width: 'half' },
      { name: 'bus', label: 'Bus', required: true, width: 'half' },
      { name: 'driver', label: 'Driver', required: true, width: 'half' },
      { name: 'startTime', label: 'Start Time', type: 'time', required: true, width: 'half' },
      { name: 'endTime', label: 'End Time', type: 'time', required: true, width: 'half' },
      { name: 'date', label: 'Trip Date', type: 'date', required: true, width: 'half' },
      { name: 'students', label: 'Expected Students', type: 'number', required: true, width: 'half' },
      { name: 'stops', label: 'Route Stops', type: 'textarea', placeholder: 'One stop per line: Stop name | lat,lng | HH:MM' },
      { name: 'status', label: 'Status', type: 'select', required: true, options: [{ value: 'scheduled', label: 'Scheduled' }, { value: 'in-progress', label: 'In Progress' }, { value: 'completed', label: 'Completed' }] }
    ]});
  </script>
</body>
</html>
