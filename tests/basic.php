<?php

print "<h1>Basic Test</h1>";

require_once '../require.php';

$settings = [
    'handle\\php' => [
        'loadingDirectory' => 'B:\projects\development\web\frameworks\handle-php\class'
    ]
];

$handle = new \handle\php();

$handle->database->connection->database = 'test';

print "<pre>" . print_r( $handle, true ) . "</pre>";