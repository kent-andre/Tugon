<?php

// Resident accounts for the no-database stage. Add one array per resident.
// first_name is what the dashboard greets ("Welcome back, Kent Andre").
// The sidebar shows first_name + last_name.
// Final term: this moves to the residents table and passwords get hashed.
return [
    'accounts' => [
        [
            'email'      => 'kentandreaguilar@gmail.com',
            'password'   => '11111111',
            'first_name' => 'Kent Andre',
            'last_name'  => 'Aguilar',
        ],
        // Second account, fill in and uncomment:
        // [
        //     'email'      => '',
        //     'password'   => '',
        //     'first_name' => '',
        //     'last_name'  => '',
        // ],
    ],
];
