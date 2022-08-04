<?php

return [
    'requestRules' => [
        'description' => 'nullable|string|min:2|max:255',
        'descriptionText' => 'nullable|string|min:2',
        'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
        'emailNullable' => 'nullable|string|min:1|max:50',
        'fax' => 'required|regex:/^([0-9_\-\s\-+()]*)$/|min:5',
        'faxNullable' => 'nullable|numeric|digits_between:1,12',
        'firstName' => 'required|min:2|max:40',
        'firstNameNullable' => 'nullable|min:2|max:40',
        'lastName' => 'required|min:2|max:40',
        'lastNameNullable' => 'nullable|min:2|max:40',
        'middleNameNullable' => 'nullable|string|min:2|max:40',
        'name' => 'required|min:2|max:50',
        'nameLong' => 'required|min:2|max:100',
        'nameNullable' => 'nullable|string|max:50',
        'phone' => 'required|regex:/^([0-9_\-\s\-+()]*)$/|min:7|max:15',
        'phoneNullable' => 'nullable|numeric|digits_between:7,15',
        'phoneSearch' => 'nullable|digits_between:2,15',
        'pin' => 'is_valid_pin',
        'pinNullable' => 'nullable|is_valid_pin',
        'postCode' => 'nullable|regex:/^[a-zA-Z0-9_\-\_\;\(\)\*\# ]*$/|max:20',
        'postCodeNullable' => 'nullable|numeric|digits_between:3,8',
        'validDate' => 'required|date|after:now',
        'validDateNullable' => 'nullable|date|after:now',
        'maxFileSize'   => '10240', //10 MB
        'amountRegex' => 'numeric|regex:/^\d+(.\d{1,2})?$/',
    ]
];
