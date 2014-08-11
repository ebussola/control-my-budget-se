<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 22/07/14
 * Time: 16:28
 */

$app->container->singleton(
    'conn',
    function () use ($app) {
        return $conn = \Doctrine\DBAL\DriverManager::getConnection($app->config['db']);
    }
);

$app->container->singleton(
    'data_provider',
    function () use ($app) {
        return new \shina\controlmybudget\dataprovider\DoctrineDBAL($app->conn);
    }
);
$app->container->singleton(
    'monthly_goal_service',
    function () use ($app) {
        $data_provider = $app->container->get('data_provider');

        return new \shina\controlmybudget\MonthlyGoalService($data_provider);
    }
);

$app->container->singleton(
    'goalr',
    function () {
        return new \ebussola\goalr\Goalr();
    }
);

$app->container->singleton(
    'purchase_service',
    function () use ($app) {
        $data_provider = $app->container->get('data_provider');

        return new \shina\controlmybudget\PurchaseService($data_provider);
    }
);

$app->container->singleton(
    'budget_control_service',
    function () use ($app) {
        $purchase_service = $app->container->get('purchase_service');
        $goalr = $app->container->get('goalr');

        return new \shina\controlmybudget\BudgetControlService($purchase_service, $goalr);
    }
);

$app->container->singleton(
    'http',
    function () {
        return new \Guzzle\Http\Client();
    }
);

$app->container->singleton(
    'user_service',
    function () use ($app) {
        $user_service = new \shina\controlmybudget\UserService($app->data_provider, $app->http);

        return $user_service;
    }
);