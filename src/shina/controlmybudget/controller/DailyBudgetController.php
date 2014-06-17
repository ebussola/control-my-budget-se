<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 13/02/14
 * Time: 11:04
 */

namespace shina\controlmybudget\controller;


use shina\controlmybudget\BudgetControlService;
use shina\controlmybudget\MonthlyGoalService;
use Slim\Slim;

class DailyBudgetController {

    /**
     * @var Slim
     */
    private $app;

    public function __construct(Slim $app) {
        $this->app = $app;
    }

    public function myDailyBudgetAction($monthly_goal_id, $spent_simulation=null) {
        /** @var BudgetControlService $budget_control_service */
        $budget_control_service = $this->app->container->get('budget_control_service');
        /** @var MonthlyGoalService $monthly_goal_service */
        $monthly_goal_service = $this->app->container->get('monthly_goal_service');

        $monthly_goal = $monthly_goal_service->getMonthlyGoalById($monthly_goal_id);
        $daily_budget = $budget_control_service->getDailyBudget($monthly_goal, $spent_simulation);

        $this->app->response->setBody($daily_budget);
    }

}