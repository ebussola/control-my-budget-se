<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 12/02/14
 * Time: 16:23
 */

// Allow from any origin
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && (
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'POST' ||
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'DELETE' ||
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'PUT' )) {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Headers: X-Requested-With');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT'); // http://stackoverflow.com/a/7605119/578667
        header('Access-Control-Max-Age: 86400');
    }
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
$config = include __DIR__ . '/../config.php';

$app = new \Slim\Slim(['debug' => ($config['env'] == 'development')]);
$app->container->set('config', $config);

require __DIR__ . '/../app/di-container.php';
require __DIR__ . '/../app/main.php';

$app->run();