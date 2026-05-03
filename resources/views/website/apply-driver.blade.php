@php
    $pageTitle = 'Join as Driver - SafeStep';
    $formTitle = 'Join as Driver';
    $formSubtitle = 'Register your car and join our network of professional drivers.';
    $activeRole = 'driver';
    $submitLabel = 'Submit Driver Application';
    $extraFields = [
        ['name' => 'owner_state', 'label' => 'State', 'type' => 'select', 'required' => true, 'options' => ['Arab Republic of Egypt', 'Kingdom of Saudi Arabia']],
        ['name' => 'owner_age', 'label' => 'Age', 'type' => 'number', 'required' => true],
        ['name' => 'owner_gender', 'label' => 'Gender', 'type' => 'select', 'required' => true, 'options' => ['Male', 'Female']],
        ['name' => 'car_type', 'label' => 'Car Type', 'required' => true],
        ['name' => 'car_model', 'label' => 'Car Model', 'required' => true],
        ['name' => 'car_plate', 'label' => 'License Plate Number', 'required' => true],
    ];
@endphp

@include('website.partials.application-form')
