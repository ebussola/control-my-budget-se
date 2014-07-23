<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 12/02/14
 * Time: 16:23
 */

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\Slim();
$app->container->set('config', include __DIR__ . '/../config.php');

require __DIR__ . '/../app/di-container.php';
require __DIR__ . '/../app/main.php';

$app->run();