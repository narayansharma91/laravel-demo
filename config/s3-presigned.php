<?php

return [
    'aws_bucket_name' => env('AWS_BUCKET', 'test'),

    'aws_access_key' => env('AWS_KEY'),

    'aws_secret_key' => env('AWS_SECRET'),

    'aws_region' => env('AWS_REGION', 'west-1'),

    'aws_default_acl' => 'private',

    'content_disposition' => 'in-line',

    'expiry_time' => env('AWS_EXPIRY_IN_MINUTES', '10'),
];
