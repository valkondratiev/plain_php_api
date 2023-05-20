<?php

require_once 'libraries/Core.php';
require_once 'libraries/Controller.php';
require_once 'libraries/Database.php';
require_once 'config.php';

foreach (glob(__DIR__."/exceptions/*.php") as $filename)
{
    require_once $filename;
}


try {

    $init = new Core();

} catch (ValidationException $e) {
    $code = $e->getCode() ?: 400;
    switch ($code) {
        case 422:
            header($_SERVER['SERVER_PROTOCOL'] . ' 422 Unprocessable Entity', true, 422);
            break;
        default:
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            break;
    }
    $output = [
        'error' => [
            'message' => $e->getMessage(),
            'details' => []
        ]
    ];
    foreach ($e->getErrors() as $error) {
        $output['error']['details'][] = [
            'field' => $error['field'],
            'message' => $error['message'],
        ];

    }
    echo json_encode($output);
}
catch (CustomException $e) {
    $code = $e->getCode();
    switch ($code) {
        case 404:
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
            break;
        case 422:
            header($_SERVER['SERVER_PROTOCOL'] . ' 422 Unprocessable Entity', true, 422);
            break;
        case 403:
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
            break;
        default:
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            break;
    }

    echo json_encode([
        'error' => [
            'message' => $e->getMessage(),
            'details' => [],
        ]
    ]);
}
catch (Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo json_encode([
        'error' => [
            'message' => 'Server error',
            'details' => [],
        ]
    ]);
}