<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Create Submission Form - Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/add-form.css') }}">
  <style>
    .form-builder-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .form-preview {
      background: #f9fafb;
      padding: 24px;
      border-radius: 10px;
      border: 1px solid rgba(148, 163, 184, 0.32);
      max-height: 600px;
      overflow-y: auto;
    }

    .field-item {
      background: white;
      padding: 12px;
      border: 1px solid rgba(148, 163, 184, 0.22);
      border-radius: 8px;
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .field-item strong {
      color: #0f172a;
    }

    .field-item small {
      color: #64748b;
      display: block;
      margin-top: 4px;
    }

    .field-actions {
      display: flex;
      gap: 8px;
    }

    .field-actions button {
      padding: 4px 8px;
      font-size: 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-edit {
      background: #3b82f6;
      color: white;
    }

    .btn-edit:hover {
      background: #2563eb;
    }

    .btn-delete {
      background: #ef4444;
      color: white;
    }

    .btn-delete:hover {
      background: #dc2626;
    }

    .section-divider {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 16px;
      border-radius: 8px;
      margin: 20px 0 0 0;
      text-align: center;
      font-weight: 600;
      font-size: 13px;
    }

    @media (max-width: 1024px) {
      .form-builder-container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="top">
      <a class="back-link" href="/admin/management">
        <i class="fas fa-arrow-left"></i> Back to Management
      </a>
    </div>

    <div class="card">
      <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h1 id="pageTitle" style="color: white; margin: 0;">Create Submission Form</h1>
        <p id="pageSubtitle" style="color: rgba(255,255,255,0.8); margin: 4px 0 0 0;">Build custom forms for your application system</p>
      </div>

      <div class="form-builder-container" style="padding: 24px;">
        <!-- Form Configuration -->
        <div>
          <div class="field" style="margin-bottom: 20px;">
            <label>Form Name *</label>
            <input type="text" id="builderFormName" class="form-control" placeholder="e.g., Driver Application Form" required>
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label>Form Description</label>
            <textarea id="builderFormDesc" class="form-control" placeholder="Describe the purpose and requirements..." style="min-height: 100px;"></textarea>
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label>Form Type *</label>
            <select id="builderFormType" class="form-control" required>
              <option value="">Select Type</option>
              <option value="application">Application</option>
              <option value="complaint">Complaint</option>
              <option value="request">Special Request</option>
              <option value="registration">Registration</option>
              <option value="feedback">Feedback</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="section-divider">
            <i class="fas fa-plus-circle"></i> Add Form Fields
          </div>

          <!-- Field Builder -->
          <div class="field" style="margin-bottom: 20px;">
            <label>Field Label *</label>
            <input type="text" id="fieldLabel" class="form-control" placeholder="e.g., Full Name">
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label>Field Type *</label>
            <select id="fieldType" class="form-control" onchange="updateFieldOptions()">
              <option value="">Select Type</option>
              <option value="text">Text Input</option>
              <option value="email">Email</option>
              <option value="tel">Phone Number</option>
              <option value="number">Number</option>
              <option value="date">Date</option>
              <option value="textarea">Text Area</option>
              <option value="select">Dropdown</option>
              <option value="checkbox">Checkbox</option>
              <option value="radio">Radio Button</option>
              <option value="file">File Upload</option>
            </select>
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label>Field Width</label>
            <select id="fieldWidth" class="form-control">
              <option value="full">Full Width</option>
              <option value="half">Half Width</option>
              <option value="third">One Third</option>
            </select>
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label>
              <input type="checkbox" id="fieldRequired"> Required Field
            </label>
          </div>

          <div class="field" id="fieldOptionsContainer" style="display: none; margin-bottom: 20px;">
            <label>Options (one per line)</label>
            <textarea id="fieldOptions" class="form-control" placeholder="Option 1&#10;Option 2&#10;Option 3" style="min-height: 100px;"></textarea>
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label>Placeholder / Helper Text</label>
            <input type="text" id="fieldPlaceholder" class="form-control" placeholder="Optional helper text">
          </div>

          <div class="actions">
            <button class="btn-secondary" type="button" onclick="resetFieldBuilder()">
              <i class="fas fa-undo"></i> Clear
            </button>
            <button class="btn-primary" type="button" onclick="addFieldToForm()">
              <i class="fas fa-plus"></i> Add Field
            </button>
          </div>
        </div>

        <!-- Form Preview -->
        <div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #1f2937;">
              <i class="fas fa-eye"></i> Form Preview
            </h3>
            <span id="fieldCount" style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">0 fields</span>
          </div>

          <div class="form-preview" id="formPreview">
            <div style="text-align: center; color: #94a3b8; padding: 40px 20px;">
              <i class="fas fa-form" style="font-size: 32px; margin-bottom: 12px; display: block;"></i>
              <p>Form fields will appear here as you add them</p>
            </div>
          </div>

          <div id="fieldsList" style="margin-top: 20px;"></div>
        </div>
      </div>

      <!-- Form Actions -->
      <div style="padding: 20px 24px; border-top: 1px solid rgba(148, 163, 184, 0.22); background: #f9fafb; display: flex; gap: 10px; justify-content: flex-end; border-radius: 0 0 10px 10px;">
        <a href="/admin/management" class="btn-secondary">
          <i class="fas fa-times"></i> Cancel
        </a>
        <button type="button" class="btn-secondary" onclick="previewForm()">
          <i class="fas fa-eye"></i> Preview Live
        </button>
        <button type="button" class="btn-primary" onclick="saveBuiltForm()">
          <i class="fas fa-save"></i> Save & Publish
        </button>
      </div>
    </div>
  </div>

  <!-- Success Message -->
  <div id="successMessage" style="display: none; position: fixed; bottom: 20px; right: 20px; background: #10b981; color: white; padding: 16px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000;">
    <i class="fas fa-check-circle"></i> <span id="successText">Form created successfully!</span>
  </div>

  <script src="{{ asset('js/api-service.js') }}"></script>
  <script>
    let formFields = [];

    // Show/hide options input based on field type
    function updateFieldOptions() {
      const fieldType = document.getElementById('fieldType').value;
      const optionsContainer = document.getElementById('fieldOptionsContainer');
      if (['select', 'radio', 'checkbox'].includes(fieldType)) {
        optionsContainer.style.display = 'block';
      } else {
        optionsContainer.style.display = 'none';
      }
    }

    // Add field to form
    function addFieldToForm() {
      const label = document.getElementById('fieldLabel').value;
      const type = document.getElementById('fieldType').value;
      const width = document.getElementById('fieldWidth').value;
      const required = document.getElementById('fieldRequired').checked;
      const placeholder = document.getElementById('fieldPlaceholder').value;
      const options = document.getElementById('fieldOptions').value.split('\n').filter(o => o.trim());

      if (!label || !type) {
        alert('Please fill in Field Label and Field Type');
        return;
      }

      const field = {
        id: Date.now(),
        label,
        type,
        width,
        required,
        placeholder,
        options: options.length > 0 ? options : []
      };

      formFields.push(field);
      updatePreview();
      resetFieldBuilder();
    }

    // Update form preview
    function updatePreview() {
      const preview = document.getElementById('formPreview');
      const fieldsList = document.getElementById('fieldsList');
      const count = document.getElementById('fieldCount');

      count.textContent = formFields.length + ' field' + (formFields.length !== 1 ? 's' : '');

      if (formFields.length === 0) {
        preview.innerHTML = `
          <div style="text-align: center; color: #94a3b8; padding: 40px 20px;">
            <i class="fas fa-form" style="font-size: 32px; margin-bottom: 12px; display: block;"></i>
            <p>Form fields will appear here as you add them</p>
          </div>
        `;
        fieldsList.innerHTML = '';
        return;
      }

      // Generate preview
      let previewHTML = `<form style="display: grid; gap: 14px;">`;
      formFields.forEach((field, index) => {
        previewHTML += `<div class="field ${field.width}">
          <label>${field.label}${field.required ? ' *' : ''}</label>`;

        if (field.type === 'textarea') {
          previewHTML += `<textarea class="form-control" placeholder="${field.placeholder}" ${field.required ? 'required' : ''}></textarea>`;
        } else if (['select', 'radio', 'checkbox'].includes(field.type)) {
          previewHTML += `<div>`;
          field.options.forEach(opt => {
            previewHTML += `<label style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
              <input type="${field.type}" ${field.required ? 'required' : ''}>
              <span>${opt}</span>
            </label>`;
          });
          previewHTML += `</div>`;
        } else {
          previewHTML += `<input type="${field.type}" class="form-control" placeholder="${field.placeholder}" ${field.required ? 'required' : ''}>`;
        }

        previewHTML += `</div>`;
      });
      previewHTML += `</form>`;

      preview.innerHTML = previewHTML;

      // Generate fields list
      let fieldsHTML = '<div>';
      formFields.forEach((field, index) => {
        fieldsHTML += `
          <div class="field-item">
            <div>
              <strong>${index + 1}. ${field.label}</strong>
              <small>${field.type}${field.required ? ' • Required' : ''}</small>
            </div>
            <div class="field-actions">
              <button class="btn-edit" onclick="editField(${field.id})"><i class="fas fa-edit"></i></button>
              <button class="btn-delete" onclick="removeField(${field.id})"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        `;
      });
      fieldsHTML += '</div>';

      fieldsList.innerHTML = fieldsHTML;
    }

    function removeField(id) {
      if (confirm('Remove this field?')) {
        formFields = formFields.filter(f => f.id !== id);
        updatePreview();
      }
    }

    function resetFieldBuilder() {
      document.getElementById('fieldLabel').value = '';
      document.getElementById('fieldType').value = '';
      document.getElementById('fieldWidth').value = 'full';
      document.getElementById('fieldRequired').checked = false;
      document.getElementById('fieldPlaceholder').value = '';
      document.getElementById('fieldOptions').value = '';
      document.getElementById('fieldOptionsContainer').style.display = 'none';
    }

    function saveBuiltForm() {
      const formName = document.getElementById('builderFormName').value;
      const formType = document.getElementById('builderFormType').value;
      const formDesc = document.getElementById('builderFormDesc').value;

      if (!formName || !formType || formFields.length === 0) {
        alert('Please fill in Form Name, Type, and add at least one field');
        return;
      }

      const formData = {
        name: formName,
        type: formType,
        description: formDesc,
        fields: formFields
      };

      console.log('Saving form:', formData);
      showSuccess('Form created successfully!');
      
      // Here you would send to server
      setTimeout(() => {
        window.location.href = '/admin/management';
      }, 2000);
    }

    function showSuccess(message) {
      const successMsg = document.getElementById('successMessage');
      document.getElementById('successText').textContent = message;
      successMsg.style.display = 'block';
      setTimeout(() => {
        successMsg.style.display = 'none';
      }, 3000);
    }

    function previewForm() {
      // Open in new window
      const formName = document.getElementById('builderFormName').value;
      if (!formName) {
        alert('Please enter a form name first');
        return;
      }
      alert('Form preview would open in a new window');
    }
  </script>
</body>
</html>
