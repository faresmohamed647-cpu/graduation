@php
    $pageTitle = 'Join as Admin - SafeStep';
    $formTitle = 'Join as Admin';
    $formSubtitle = 'Apply to become an administrator and help manage the SafeStep network.';
    $activeRole = 'admin';
    $submitLabel = 'Submit Admin Application';
    $extraFields = [
        ['name' => 'admin_department', 'label' => 'Preferred Department', 'required' => true],
        ['name' => 'years_experience', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
        ['name' => 'highest_qualification', 'label' => 'Highest Qualification', 'required' => true],
        ['name' => 'availability', 'label' => 'Availability', 'type' => 'select', 'required' => true, 'options' => ['Immediate', 'Within 2 Weeks', 'Within 1 Month']],
    ];
@endphp

@include('website.partials.application-form')
