<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 12/02/14
 * Time: 16:23
 */

require __DIR__ . '/../vendor/autoload.php';

$config = include __DIR__ . '/../config.php';

$app = new \Slim\Slim(array('debug' => true));
$app->response->headers->set('Access-Control-Allow-Origin', '*');


$conn = \Doctrine\DBAL\DriverManager::getConnection($config['db']);
$app->container->set('data_provider', new \shina\controlmybudget\dataprovider\DoctrineDBAL($conn));
$app->container->set('monthly_goal_service', function() use ($app) {
    $data_provider = $app->container->get('data_provider');

    return new \shina\controlmybudget\MonthlyGoalService($data_provider);
});
$app->container->set('goalr', function() {
    return new \ebussola\goalr\Goalr();
});
$app->container->set('purchase_service', function() use ($app) {
    $data_provider = $app->container->get('data_provider');

    return new \shina\controlmybudget\PurchaseService($data_provider);
});
$app->container->set('budget_control_service', function() use ($app) {
    $purchase_service = $app->container->get('purchase_service');
    $goalr = $app->container->get('goalr');

    return new \shina\controlmybudget\BudgetControlService($purchase_service, $goalr);
});



$app->get('/goals/:month/:year', function($month, $year) use ($app) {
    $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
    $monthly_goal_controller->goalAction($month, $year);
});

$app->get('/goals', function() use ($app) {
    $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
    $monthly_goal_controller->goalAction();
});

$app->get('/goals/:goal_id', function($monthly_goal_id) use ($app) {
    $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
    $monthly_goal_controller->getGoalAction($monthly_goal_id);
});
$app->post('/goals/:goal_id', function($monthly_goal_id) use ($app) {
    $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
    $monthly_goal_controller->editGoalAction($monthly_goal_id);
});

$app->post('/goals', function() use ($app) {
    $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
    $monthly_goal_controller->addGoalAction();
});

$app->get('/my-daily-budget/:monthly_goal_id', function($monthly_goal_id) use ($app) {
    $daily_budget_controller = new \shina\controlmybudget\controller\DailyBudgetController($app);
    $daily_budget_controller->myDailyBudgetAction($monthly_goal_id);
});

$app->run();