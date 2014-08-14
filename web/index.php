<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 12/02/14
 * Time: 16:23
 */

require __DIR__ . '/../vendor/autoload.php';
$config = include __DIR__ . '/../config.php';

$app = new \Slim\Slim([
    'debug' => ($config['env'] == 'development'),
    'templates.path' => __DIR__ . '/../templates'
]);
$app->container->set('config', $config);

require __DIR__ . '/../app/di-container.php';
require __DIR__ . '/../app/site.php';

$app->run();