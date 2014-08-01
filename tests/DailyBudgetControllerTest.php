<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 23/07/14
 * Time: 11:24
 */

class DailyBudgetControllerTest extends Slim_Framework_TestCase
{

    public function testDailyBudget()
    {
        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = date('m');
        $monthly_goal->year = date('Y');
        $monthly_goal->amount_goal = 2000;
        $monthly_goal->events = [];
        $this->app->monthly_goal_service->save($monthly_goal);

        $this->get('/my-daily-budget/'.$monthly_goal->id);

        $this->assertEquals(200, $this->response->getStatus());
        $this->assertTrue($this->response->getBody() >= (floor(2000/date('t'))));
    }

    public function testDailyBudgetSpentSimulation()
    {
        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = date('m');
        $monthly_goal->year = date('Y');
        $monthly_goal->amount_goal = 2000;
        $monthly_goal->events = [];
        $this->app->monthly_goal_service->save($monthly_goal);

        $this->get('/my-daily-budget/'.$monthly_goal->id.'/2000');
        $this->assertEquals(200, $this->response->getStatus());
        $this->assertTrue($this->response->getBody() == 0);
    }

}