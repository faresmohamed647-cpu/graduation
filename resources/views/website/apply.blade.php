<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Apply Now - SafeStep</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('img/icon.jpg') }}" rel="icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <style>
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            border-color: #0ea5a4;
            box-shadow: 0 0 0 0.25rem rgba(14, 165, 164, 0.25);
        }
        .btn-primary {
            background-color: #0ea5a4;
            border-color: #0ea5a4;
        }
        .btn-primary:hover {
            background-color: #0d8a89;
            border-color: #0d8a89;
        }
    </style>
</head>

<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow border-top border-5 border-primary sticky-top p-0">
        <a href="{{ url('/') }}" class="navbar-brand bg-primary d-flex align-items-center px-4 px-lg-5">
            <h2 class="mb-2 text-white">SAFESTEP BUS</h2>
        </a>
    </nav>
    <!-- Navbar End -->

    <div class="container">
        <div class="form-container wow fadeInUp" data-wow-delay="0.1s">
            <div class="text-center mb-5">
                <h6 class="text-secondary text-uppercase">Join Our Team</h6>
                <h1 class="mb-3">Application Form</h1>
                <p>Fill out the form below and we will get back to you soon.</p>
            </div>

            <div id="responseMessage" style="display: none;" class="alert alert-success"></div>

            <form id="applicationForm" class="ajax-form" action="/apply/submit" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required placeholder="+20 3 123 4567">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Position / Role Applying For</label>
                        <select name="role" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="Driver">Driver</option>
                            <option value="Parent">Parent</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" required placeholder="123 Main St, City">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Professional Experience & Skills</label>
                        <textarea name="experience" class="form-control" rows="4" required placeholder="Tell us about your previous experience, driving history, or administrative skills..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Additional Notes or Qualifications (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional information or certifications..."></textarea>
                    </div>
                    <div class="col-12 text-center mt-4">
                        <button class="btn btn-primary w-100 py-3" type="submit" data-loading-text="Submitting...">Submit Professional Application</button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-4 p-3 bg-light rounded shadow-sm border">
                <p class="mb-2 text-dark">Already have an account?</p>
                <a href="{{ url('/login') }}" class="btn btn-secondary px-4 py-2">
                    <i class="fas fa-arrow-right-to-bracket me-2"></i> Login
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/ajax-forms.js') }}"></script>
</body>
</html>
