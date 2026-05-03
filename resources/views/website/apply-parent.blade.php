@php
    $pageTitle = 'Join as Parent - SafeStep';
    $formTitle = 'Join as Parent';
    $formSubtitle = 'Register to track your children and ensure their safety.';
    $activeRole = 'parent';
    $submitLabel = 'Submit Parent Application';
    $extraFields = [
        ['name' => 'student_state', 'label' => 'State', 'type' => 'select', 'required' => true, 'options' => ['Arab Republic of Egypt', 'Kingdom of Saudi Arabia']],
        ['name' => 'student_relationship', 'label' => 'Relationship', 'type' => 'select', 'required' => true, 'options' => ['Father', 'Mother']],
        ['name' => 'student_count', 'label' => 'Number of Students', 'type' => 'number', 'required' => true],
        ['name' => 'student_degree', 'label' => 'Degree / Level', 'required' => true],
        ['name' => 'student_education_system', 'label' => 'Education System', 'required' => true],
        ['name' => 'school_name', 'label' => 'School Name', 'required' => true],
        ['name' => 'school_address', 'label' => 'School Address', 'required' => true, 'full' => true],
        ['name' => 'school_starting', 'label' => 'School Starting Date/Time', 'required' => true],
    ];
@endphp

@include('website.partials.application-form')
