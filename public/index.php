<?php

require_once __DIR__ . '/../vendor/autoload.php';

header ('Content-Type: application/json');

echo json_encode([
    'version' => '1.0',
    'response' => [
        'outputSpeech' => [
            'type' => 'PlainText',
            'text' => 'hallo'
        ]
    ]
]);

exit(0);