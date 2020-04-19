<?php

require_once __DIR__ . '/../vendor/autoload.php';

header ('Content-Type: application/json');

echo json_encode([
    'response' => [
        'outputSpeech' => [
            'text' => 'hallo'
        ]
    ]
]);

exit(0);