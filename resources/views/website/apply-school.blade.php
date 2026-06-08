@php
    $pageTitle = 'School Registration - SafeStep';
    $formTitle = 'Register Your School';
    $formSubtitle = 'Submit a school registration request. Our team will review and provision your dashboard.';
    $activeRole = 'school';
    $submitLabel = 'Submit School Registration';
    $extraFields = [
        ['name' => 'school_name', 'label' => 'School Name', 'required' => true],
        ['name' => 'school_email', 'label' => 'School Email', 'type' => 'email', 'required' => true],
        ['name' => 'principal_name', 'label' => 'Principal Name', 'required' => true],
        ['name' => 'school_address', 'label' => 'School Address', 'required' => true, 'full' => true],
        ['name' => 'student_count', 'label' => 'Number of Students', 'type' => 'number', 'required' => true],
        ['name' => 'bus_count', 'label' => 'Number of Buses', 'type' => 'number', 'required' => true],
        ['name' => 'school_logo', 'label' => 'School Logo', 'type' => 'file', 'accept' => 'image/*'],
    ];
@endphp

@include('website.partials.application-form')
