<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, post, get,put');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
header('Content-Type: application/json');

require __DIR__ . '/libs/AltoRouter/AltoRouter.php';
require __DIR__ . '/api/advert.php';

// Setup routes
$router = new AltoRouter();
$router->setBasePath('');
$router->map('GET','/advert/get-list','Advert#getAll','getAll');
$router->map('GET','/advert/[i:id]','Advert#getById','getById');
$router->map('POST','/advert/create','Advert#create','create');

// match current request
$match = $router->match();

if ($match === false) {
    echo json_encode([
        "code" => 404,
        "message" => "NOT_FOUND"
    ]);

} else {
    list($controller, $action) = explode('#', $match['target']);
    $controller = new $controller;
    if (is_callable(array($controller, $action))) {
        call_user_func_array(array($controller, $action), array($match['params']));

    } else {
        echo json_encode([
            "code" => 400,
            "message" => 'Error: can not call ' . get_class($controller) . '#' . $action
        ]);
    }
}
?>