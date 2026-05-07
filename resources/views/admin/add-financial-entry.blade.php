<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Financial Entry</title>
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
    renderAddForm({ title: 'Add Financial Entry', subtitle: 'Record a new financial transaction.', fields: [
      { name: 'date', label: 'Date', type: 'date', required: true, width: 'half' },
      { name: 'amount', label: 'Amount', type: 'number', required: true, width: 'half' },
      { name: 'type', label: 'Type', type: 'select', required: true, options: [{ value: 'income', label: 'Income' }, { value: 'expense', label: 'Expense' }, { value: 'profit', label: 'Profit' }, { value: 'loss', label: 'Loss' }] },
      { name: 'enteredBy', label: 'Entered By', required: true, width: 'half' },
      { name: 'description', label: 'Description', type: 'textarea', required: true }
    ]});
  </script>
  <script src="{{ asset('js/ajax-forms.js') }}"></script>
</body>
</html>
