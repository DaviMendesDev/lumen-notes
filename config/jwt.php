<?php

return [
    'secret' => env('JWT_SECRET'),

    'access' => [
        'ttl' => 30 * 60,
        'claims_fields' => [
            'iss',
            'aud',
            'exp'
        ],
    ],

    'refresh' => [
        'ttl' => 30 * (60 * 60 * 24),
        'claims_fields' => [
            // ...
        ],
    ]
];
