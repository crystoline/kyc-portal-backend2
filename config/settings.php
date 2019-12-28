<?php

return [
    'basic' => [
        'title' => 'Basic Setting',
        'desc' => 'Basic Setting',
        'icon' => 'glyphicon glyphicon-sunglasses',

        'elements' => [
           /* [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 're_verification_period', // unique name for field
                'label' => 'Re-Verification period (Months)', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 6 // default value if you want
            ],
            [
                'type' => 'radio', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'enable_verification', // unique name for field
                'label' => 'Enable verification', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'options' => [
                    ['value' => 0, 'name' => 'No'],
                    ['value' => 1, 'name' => 'Yes'],
                ],
                'value' => 1
            ],*/
            [
                'type' => 'select', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'field_officer_role', // unique name for field
                'label' => 'Field officer\'s role', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'options' => null,
                'value' => 'field_officer'
            ]
        ],

    ],
    'sms' => [
        'title' => 'SMS Setting',
        'desc' => 'SMS API Settings',
        'icon' => 'glyphicon glyphicon-sunglasses',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'sms_user_id', // unique name for field
                'label' => 'User ID', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '' // default value if you want
            ],
            [
                'type' => 'password', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'sms_secret_key', // unique name for field
                'label' => 'User ID', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '' // default value if you want
            ],
        ]
    ],
    'bvn' => [
        'title' => 'BVN Setting',
        'desc' => 'BVN API Settings',
        'icon' => 'glyphicon glyphicon-sunglasses',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bvn_user_id', // unique name for field
                'label' => 'User ID', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '' // default value if you want
            ],
            [
                'type' => 'password', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bvn_password', // unique name for field
                'label' => 'Password', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '' // default value if you want
            ],
        ]
    ],
    'account' => [
        'title' => 'Account Setting',
        'desc' => 'Account Name Enquiry',
        'icon' => 'glyphicon glyphicon-sunglasses',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'css_name_enquiry_client_id ', // unique name for field
                'label' => 'Client ID', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '' // default value if you want
            ],
            [
                'type' => 'password', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'css_name_enquiry_secret_key', // unique name for field
                'label' => 'Password', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '' // default value if you want
            ],
        ]
    ]
];

//setting('enable_part_payment');