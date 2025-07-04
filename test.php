<?php
require 'vendor/autoload.php';

use MongoDB\Laravel\MongoDB;

try {
    $client = new MongoDB\Driver\Manager(env('MONGODB_URI'));
    $databases = $client->executeCommand('admin', new MongoDB\Driver\Command(['listDatabases' => 1]))->toArray();
    echo "Connected to MongoDB. Databases: \n";
    print_r($databases);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
exit;