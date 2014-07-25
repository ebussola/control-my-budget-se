<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 22/07/14
 * Time: 16:28
 */

$app->response->headers->set('Access-Control-Allow-Origin', '*');

$app->get(
    '/goals/:month/:year',
    function ($month, $year) use ($app) {
        $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
        $monthly_goal_controller->goalAction($month, $year);
    }
);

$app->get(
    '/goals',
    function () use ($app) {
        $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
        $monthly_goal_controller->allGoalsAction();
    }
);

$app->get(
    '/goal/:goal_id',
    function ($monthly_goal_id) use ($app) {
        $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
        $monthly_goal_controller->getGoalAction($monthly_goal_id);
    }
);
$app->post(
    '/goal/:goal_id',
    function ($monthly_goal_id) use ($app) {
        $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
        $monthly_goal_controller->editGoalAction($monthly_goal_id);
    }
);

$app->post(
    '/goals',
    function () use ($app) {
        $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
        $monthly_goal_controller->addGoalAction();
    }
);

$app->delete(
    '/goal/:monthly_goal_id',
    function ($monthly_goal_id) use ($app) {
        $monthly_goal_controller = new \shina\controlmybudget\controller\MonthlyGoalController($app);
        $monthly_goal_controller->deleteGoalAction($monthly_goal_id);
    }
);


$app->get(
    '/my-daily-budget/:monthly_goal_id',
    function ($monthly_goal_id) use ($app) {
        $daily_budget_controller = new \shina\controlmybudget\controller\DailyBudgetController($app);
        $daily_budget_controller->myDailyBudgetAction($monthly_goal_id);
    }
);
$app->get(
    '/my-daily-budget/:monthly_goal_id/:spent_simulation',
    function ($monthly_goal_id, $spent_simulation) use ($app) {
        $daily_budget_controller = new \shina\controlmybudget\controller\DailyBudgetController($app);
        $daily_budget_controller->myDailyBudgetAction($monthly_goal_id, $spent_simulation);
    }
);