<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Monolog settings
        /*
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        */
         // Database connection settings           
          "db" => [
            "host" => "localhost",
            "dbname" => "gov_bot",
            "user" => "root",
            "pass" => ""
        ],
        "facebook"=>[
            "access_token"=>"EAAFJbxSzpDYBAP7RRtRop6rfwrcAM2VueGEyL2PHgnm3ZAh1ZCDhDkfLhXkgrcZCerKHoZCdsCuPjPe0kj4BRWyX9xBz9JP9txCSeuuI3hEh6IyJBsh4Ig9AMwOMKyZC0ZAZAl466h43WyOmvmgVouqeIkrIpeSFSTFeaJKIDc7tV0BSctaXzTZB"
        ]
    ],
];