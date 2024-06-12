<?php

return [
    'role_structure' => [
        'superadministrator' => [
            'users' => 'c,r,u,d',
            'doctors' => 'c,r,u,d',
            'patients' => 'c,r,u,d',
            'medical_records' => 'c,r,u,d',
            'appointments' => 'c,r,u,d',
            'profile' => 'r,u',
        ],
        'administrator' => [
            'doctors' => 'c,r,u,d',
            'patients' => 'c,r,u,d',
            'medical_records' => 'c,r,u,d',
            'appointments' => 'c,r,u,d',
            'profile' => 'r,u',
        ],
        'doctor' => [
            'patients' => 'r',
            'medical_records' => 'c,r,u,d',
            'appointments' => 'c,r,u,d',
            'profile' => 'r,u',
        ],
        'patient' => [
            'medical_records' => 'r',
            'appointments' => 'c,r,u',
            'profile' => 'r,u',
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];

