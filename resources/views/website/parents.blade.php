<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/pearents.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Join SafeStep</title>
    <style>
        .alert-success {
            background: linear-gradient(135deg, rgba(5,150,105,0.12), rgba(5,150,105,0.06));
            color: #065f46;
            border: 1px solid rgba(5,150,105,0.3);
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.4s ease;
        }
        .alert-error {
            background: linear-gradient(135deg, rgba(220,38,38,0.12), rgba(220,38,38,0.06));
            color: #991b1b;
            border: 1px solid rgba(220,38,38,0.3);
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
            width: 100%;
            animation: fadeIn 0.4s ease;
        }
        .alert-error ul { list-style: none; padding: 0; margin: 0; }
        .alert-error li { margin: 2px 0; }
    </style>
</head>

<body>
    <div class="container" id="container">

        <!-- ===== Parent (Student) Form ===== -->
        <div class="form-container join-student-container">
            <form action="{{ url('/register/parent') }}" method="POST" data-request-role="parent">
                @csrf
                <h1>Join as a Student</h1>

                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any() && old('_form') !== 'driver')
                    <div class="alert-error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input type="hidden" name="_form" value="parent">

                <label>Address</label>
                <div class="input-group">
                    <i class='bx bxs-home'></i>
                    <input type="text" name="student_address" value="{{ old('student_address') }}" autocomplete="street-address" required>
                </div>

                <label>State</label>
                <div class="input-group">
                    <i class='bx bxs-map'></i>
                    <select name="student_state" autocomplete="address-level1" required>
                        <option value="">select</option>
                        <option @if(old('student_state')=='Arab Republic of Egypt') selected @endif>Arab Republic of Egypt</option>
                        <option @if(old('student_state')=='Kingdom of Saudi Arabia') selected @endif>Kingdom of Saudi Arabia</option>
                    </select>
                </div>

                <label>Phone</label>
                <div class="input-group">
                    <i class='bx bxs-phone'></i>
                    <input type="tel" name="student_phone" value="{{ old('student_phone') }}" autocomplete="tel" inputmode="tel" required>
                </div>

                <label>Relationship (with the school)</label>
                <div class="input-group">
                    <i class='bx bxs-user-circle'></i>
                    <select name="student_relationship" autocomplete="off" required>
                        <option value="">select</option>
                        <option @if(old('student_relationship')=='Father') selected @endif>Father</option>
                        <option @if(old('student_relationship')=='Mother') selected @endif>Mother</option>
                    </select>
                </div>

                <label>Number of students</label>
                <div class="input-group">
                    <i class='bx bxs-group'></i>
                    <input type="number" name="student_count" value="{{ old('student_count') }}" inputmode="numeric" min="1" required>
                </div>

                <label>Degree</label>
                <div class="input-group">
                    <i class='bx bxs-graduation'></i>
                    <input type="text" name="student_degree" value="{{ old('student_degree') }}" autocomplete="off" required>
                </div>

                <label>Education system</label>
                <div class="input-group">
                    <i class='bx bxs-book'></i>
                    <input type="text" name="student_education_system" value="{{ old('student_education_system') }}" autocomplete="off" required>
                </div>

                <label>School name</label>
                <div class="input-group">
                    <i class='bx bxs-building'></i>
                    <input type="text" name="school_name" value="{{ old('school_name') }}" autocomplete="organization" required>
                </div>

                <label>School address</label>
                <div class="input-group">
                    <i class='bx bxs-map'></i>
                    <input type="text" name="school_address" value="{{ old('school_address') }}" autocomplete="street-address" required>
                </div>

                <label>School starting</label>
                <div class="input-group">
                    <i class='bx bxs-calendar'></i>
                    <input type="text" name="school_starting" value="{{ old('school_starting') }}" autocomplete="off" required>
                </div>

                <label>Email</label>
                <div class="input-group">
                    <i class='bx bxs-envelope'></i>
                    <input type="email" name="student_email" value="{{ old('student_email') }}" data-email="student" autocomplete="email" required>
                </div>

                <div class="password-block" data-block="student">
                    <label>Password</label>
                    <div class="input-group">
                        <i class='bx bxs-lock-alt'></i>
                        <input type="password" name="student_password" data-password="student" autocomplete="new-password" required>
                    </div>

                    <label>Confirm password</label>
                    <div class="input-group">
                        <i class='bx bxs-lock-alt'></i>
                        <input type="password" name="student_password_confirm" data-confirm="student" autocomplete="new-password" required>
                    </div>

                    <div class="password-checklist" data-checklist="student">
                        <div class="strength-meter">
                            <div class="strength-bar" data-bar="student"></div>
                            <span class="strength-text" data-text="student">0%</span>
                        </div>
                        <ul>
                            <li data-rule="length8">At least 8 characters</li>
                            <li data-rule="length12">12+ characters (stronger)</li>
                            <li data-rule="uppercase">At least 1 uppercase letter</li>
                            <li data-rule="lowercase">At least 1 lowercase letter</li>
                            <li data-rule="number">At least 1 number</li>
                            <li data-rule="special">At least 1 special character</li>
                            <li data-rule="no-spaces">No spaces</li>
                            <li data-rule="match">Passwords match</li>
                        </ul>
                    </div>
                </div>

                <label>Message</label>
                <div class="input-group textarea">
                    <i class='bx bxs-message'></i>
                    <textarea name="student_message" autocomplete="off">{{ old('student_message') }}</textarea>
                </div>

                <button type="submit">Join Now</button>
                <p class="existing-account"><a href="{{ url('/verify?type=student') }}">Do you already have an account? Login here</a></p>
            </form>
        </div>

        <!-- ===== Car Owner Form ===== -->
        <div class="form-container join-us">
            <form action="{{ url('/register/driver') }}" method="POST" data-request-role="driver">
                @csrf
                <h1>Join as a Car Owner</h1>

                @if($errors->any() && old('_form') === 'driver')
                    <div class="alert-error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input type="hidden" name="_form" value="driver">

                <label>State</label>
                <div class="input-group">
                    <i class='bx bxs-map'></i>
                    <select name="owner_state" autocomplete="address-level1" required>
                        <option value="">select</option>
                        <option @if(old('owner_state')=='Arab Republic of Egypt') selected @endif>Arab Republic of Egypt</option>
                        <option @if(old('owner_state')=='Kingdom of Saudi Arabia') selected @endif>Kingdom of Saudi Arabia</option>
                    </select>
                </div>

                <label>Your full name</label>
                <div class="input-group">
                    <i class='bx bxs-user'></i>
                    <input type="text" name="owner_full_name" value="{{ old('owner_full_name') }}" autocomplete="name" required>
                </div>

                <label>Your age</label>
                <div class="input-group">
                    <i class='bx bxs-cake'></i>
                    <input type="number" name="owner_age" value="{{ old('owner_age') }}" inputmode="numeric" min="18" required>
                </div>

                <label>Gender</label>
                <div class="input-group">
                    <i class='bx bxs-gender-female'></i>
                    <select name="owner_gender" autocomplete="sex" required>
                        <option value="">select</option>
                        <option @if(old('owner_gender')=='Male') selected @endif>Male</option>
                        <option @if(old('owner_gender')=='Female') selected @endif>Female</option>
                    </select>
                </div>

                <label>Mobile number</label>
                <div class="input-group">
                    <i class='bx bxs-phone'></i>
                    <input type="tel" name="owner_phone" value="{{ old('owner_phone') }}" autocomplete="tel" inputmode="tel" required>
                </div>

                <label>Car type</label>
                <div class="input-group">
                    <i class='bx bxs-car'></i>
                    <input type="text" name="car_type" value="{{ old('car_type') }}" autocomplete="off" required>
                </div>

                <label>Car model</label>
                <div class="input-group">
                    <i class='bx bxs-car'></i>
                    <input type="text" name="car_model" value="{{ old('car_model') }}" autocomplete="off" required>
                </div>

                <label>Home address</label>
                <div class="input-group">
                    <i class='bx bxs-home'></i>
                    <input type="text" name="owner_address" value="{{ old('owner_address') }}" autocomplete="street-address" required>
                </div>

                <label>Car license plate number</label>
                <div class="input-group">
                    <i class='bx bxs-id-card'></i>
                    <input type="text" name="car_plate" value="{{ old('car_plate') }}" autocomplete="off" required>
                </div>

                <label>Email</label>
                <div class="input-group">
                    <i class='bx bxs-envelope'></i>
                    <input type="email" name="owner_email" value="{{ old('owner_email') }}" data-email="car-owner" autocomplete="email" required>
                </div>

                <div class="password-block" data-block="car-owner">
                    <label>Password</label>
                    <div class="input-group">
                        <i class='bx bxs-lock-alt'></i>
                        <input type="password" name="owner_password" data-password="car-owner" autocomplete="new-password" required>
                    </div>

                    <label>Confirm password</label>
                    <div class="input-group">
                        <i class='bx bxs-lock-alt'></i>
                        <input type="password" name="owner_password_confirm" data-confirm="car-owner" autocomplete="new-password" required>
                    </div>

                    <div class="password-checklist" data-checklist="car-owner">
                        <div class="strength-meter">
                            <div class="strength-bar" data-bar="car-owner"></div>
                            <span class="strength-text" data-text="car-owner">0%</span>
                        </div>
                        <ul>
                            <li data-rule="length8">At least 8 characters</li>
                            <li data-rule="length12">12+ characters (stronger)</li>
                            <li data-rule="uppercase">At least 1 uppercase letter</li>
                            <li data-rule="lowercase">At least 1 lowercase letter</li>
                            <li data-rule="number">At least 1 number</li>
                            <li data-rule="special">At least 1 special character</li>
                            <li data-rule="no-spaces">No spaces</li>
                            <li data-rule="match">Passwords match</li>
                        </ul>
                    </div>
                </div>

                <label>Message</label>
                <div class="input-group textarea">
                    <i class='bx bxs-message'></i>
                    <textarea name="owner_message" autocomplete="off">{{ old('owner_message') }}</textarea>
                </div>

                <button type="submit">Subscribe Now</button>
                <p class="existing-account"><a href="{{ url('/verify?type=car-owner') }}">Do you already have an account? Login here</a></p>
            </form>
        </div>

        <!-- ===== Toggle Panels ===== -->
        <div class="toggle-container">
            <div class="toggle">

                <div class="toggle-panel toggle-left">
                    <h1>Join as a Student</h1>
                    <p>Parents can register here to track their children on the bus</p>
                    <button type="button" id="login">Parents</button>
                </div>

                <div class="toggle-panel toggle-right">
                    <h1>Join as a Car Owner</h1>
                    <p>Driver and car owners can register to start earning</p>
                    <button type="button" id="register">Car Owner</button>
                </div>

            </div>
        </div>

    </div>

    <script src="{{ asset('js/pearents.js') }}"></script>
    <script src="{{ asset('js/public-theme.js') }}"></script>

<!-- Language Support -->
<script src='{{ asset('js/language.js') }}'></script>
