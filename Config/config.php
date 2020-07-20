<?php

return [
    'name' => 'CognitoGuard',
    'region' => env('COGNITO_AWS_REGION', 'us-east-1'),
    'user_pool_id' => env('COGNITO_USER_POOL_ID', '')
];
