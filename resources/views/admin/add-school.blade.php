<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add School</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/add-form.css') }}">
</head>
<body>
  <div class="container"><div class="top"><a class="back-link" href="/admin"><i class="fas fa-arrow-left"></i> Back To Admin</a></div><div class="card"><div class="card-header"><h1 id="pageTitle"></h1><p id="pageSubtitle"></p></div><form class="form ajax-form" id="addForm"></form></div></div>
  <script src="{{ asset('js/add-form.js') }}"></script>
  <script>
    renderAddForm({ title: 'Add School', subtitle: 'Register a new school.', fields: [
      { name: 'name', label: 'School Name', required: true, width: 'half' },
      { name: 'district', label: 'District', required: true, width: 'half' },
      { name: 'type', label: 'School Type', type: 'select', required: true, options: [{ value: 'public', label: 'Public' }, { value: 'private', label: 'Private' }, { value: 'international', label: 'International' }], width: 'half' },
      { name: 'contact', label: 'Contact Number', type: 'tel', required: true, width: 'half' },
      { name: 'students', label: 'Student Count', type: 'number', width: 'half' },
      { name: 'status', label: 'Status', type: 'select', required: true, options: [{ value: 'active', label: 'Active' }, { value: 'inactive', label: 'Inactive' }], width: 'half' },
      { name: 'address', label: 'Address', required: true }
    ]});
  </script>
  <script src="{{ asset('js/ajax-forms.js') }}"></script>
</body>
</html>
