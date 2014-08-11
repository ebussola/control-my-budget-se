<?php

/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 22/07/14
 * Time: 16:52
 */
class MonthlyGoalControllerTest extends Slim_Framework_TestCase
{

    public function testGoalsDate_Get()
    {
        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = 8;
        $monthly_goal->year = 2014;
        $monthly_goal->amount_goal = 2000;
        $monthly_goal->events = [];
        $this->app->monthly_goal_service->save($monthly_goal, $this->user);

        $this->get('/goals/8/2014');

        $this->assertEquals(200, $this->response->getStatus());
        $data = json_decode($this->response->getBody());

        $this->assertCount(1, $data);
    }

    public function testGoalsDateNotFound_Get()
    {
        $this->get('/goals/8/2014');

        $this->assertEquals(200, $this->response->getStatus());
        $data = json_decode($this->response->getBody());

        $this->assertCount(0, $data);
    }

    public function testGoalsAll()
    {
        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = 7;
        $monthly_goal->year = 2014;
        $monthly_goal->amount_goal = 1000;
        $monthly_goal->events = [];
        $this->app->monthly_goal_service->save($monthly_goal, $this->user);

        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = 8;
        $monthly_goal->year = 2014;
        $monthly_goal->amount_goal = 2000;
        $monthly_goal->events = [];
        $this->app->monthly_goal_service->save($monthly_goal, $this->user);

        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = 8;
        $monthly_goal->year = 2014;
        $monthly_goal->amount_goal = 3000;
        $monthly_goal->events = [];
        $this->app->monthly_goal_service->save($monthly_goal, $this->user);

        $this->get('/goals');

        $data = json_decode($this->response->getBody());
        $this->assertEquals(200, $this->response->getStatus());
        $this->assertCount(3, $data);
    }

    public function testGoalId()
    {
        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = 8;
        $monthly_goal->year = 2014;
        $monthly_goal->amount_goal = 2000;
        $monthly_goal->events = [];
        $this->app->monthly_goal_service->save($monthly_goal, $this->user);

        $this->get('/goal/' . $monthly_goal->id);

        $data = json_decode($this->response->getBody());
        $this->assertEquals(200, $this->response->getStatus());
        $this->assertEquals($monthly_goal->month, $data->month);
        $this->assertEquals($monthly_goal->year, $data->year);
        $this->assertEquals($monthly_goal->amount_goal, $data->amount_goal);
    }

    public function testEditGoal()
    {
        /** @var \shina\controlmybudget\MonthlyGoalService $monthly_goal_service */
        $monthly_goal_service = $this->app->monthly_goal_service;

        $event = new \ebussola\goalr\event\Event();
        $event->name = 'Test';
        $event->date_start = new \ebussola\common\datatype\datetime\Date('2014-08-18');
        $event->date_end = new \ebussola\common\datatype\datetime\Date('2014-08-20');
        $event->category = 'bithday';
        $event->variation = 200;

        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = 8;
        $monthly_goal->year = 2014;
        $monthly_goal->amount_goal = 2000;
        $monthly_goal->events = [$event];
        $monthly_goal_service->save($monthly_goal, $this->user);

        $this->post(
            '/goal/' . $monthly_goal->id,
            [
                'monthly_goal' => json_encode(
                    [
                        'month' => 9,
                        'year' => 2013,
                        'amount_goal' => 1234
                    ]
                )
            ]
        );

        $monthly_goal = $monthly_goal_service->getMonthlyGoalById($monthly_goal->id);
        $this->assertEquals(9, $monthly_goal->month);
        $this->assertEquals(2013, $monthly_goal->year);
        $this->assertEquals(1234, $monthly_goal->amount_goal);
        $this->assertCount(1, $monthly_goal->events);
    }

    public function testEditGoal_RemoveEvent()
    {
        /** @var \shina\controlmybudget\MonthlyGoalService $monthly_goal_service */
        $monthly_goal_service = $this->app->monthly_goal_service;

        $event1 = new \ebussola\goalr\event\Event();
        $event1->name = 'Test';
        $event1->date_start = new \ebussola\common\datatype\datetime\Date('2014-08-18');
        $event1->date_end = new \ebussola\common\datatype\datetime\Date('2014-08-20');
        $event1->category = 'bithday';
        $event1->variation = 200;

        $event2 = new \ebussola\goalr\event\Event();
        $event2->name = 'Test2';
        $event2->date_start = new \ebussola\common\datatype\datetime\Date('2014-08-10');
        $event2->date_end = new \ebussola\common\datatype\datetime\Date('2014-08-12');
        $event2->category = 'fds';
        $event2->variation = 100;

        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = 8;
        $monthly_goal->year = 2014;
        $monthly_goal->amount_goal = 2000;
        $monthly_goal->events = [$event1, $event2];
        $monthly_goal_service->save($monthly_goal, $this->user);

        $monthly_goal = $monthly_goal_service->getMonthlyGoalById($monthly_goal->id);
        $this->post(
            '/goal/' . $monthly_goal->id,
            [
                'monthly_goal' => json_encode(
                    [
                        'events' => [
                            [
                                'id' => $monthly_goal->events[1]->id,
                                'name' => $monthly_goal->events[1]->name,
                                'date_start' => $monthly_goal->events[1]->date_start->format('Y-m-d'),
                                'date_end' => $monthly_goal->events[1]->date_end->format('Y-m-d'),
                                'category' => $monthly_goal->events[1]->category,
                                'variation' => $monthly_goal->events[1]->variation
                            ]
                        ]
                    ]
                )
            ]
        );

        $monthly_goal = $monthly_goal_service->getMonthlyGoalById($monthly_goal->id);
        $this->assertCount(1, $monthly_goal->events);
    }

    public function testAddGoal()
    {
        $this->post(
            '/goals',
            [
                'monthly_goal' => json_encode([
                    'month' => 8,
                    'year' => 2014,
                    'amount_goal' => 1300
                ])
            ]
        );

        $data = json_decode($this->response->getBody());
        $this->assertEquals(200, $this->response->getStatus());
        $this->assertObjectHasAttribute('id', $data);
    }

    public function testDeleteGoal_Invalid()
    {
        $this->delete('/goal/69');

        $this->assertEquals(400, $this->response->getStatus());
    }

    public function testDeleteGoal()
    {
        /** @var \shina\controlmybudget\MonthlyGoalService $monthly_goal_service */
        $monthly_goal_service = $this->app->monthly_goal_service;

        $monthly_goal = new \shina\controlmybudget\MonthlyGoal\MonthlyGoal();
        $monthly_goal->month = 8;
        $monthly_goal->year = 2014;
        $monthly_goal->amount_goal = 2000;
        $monthly_goal->events = [];
        $monthly_goal_service->save($monthly_goal, $this->user);

        $this->delete('/goal/'.$monthly_goal->id);

        $this->assertEquals(200, $this->response->getStatus());
        $this->assertCount(0, $monthly_goal_service->getAll($this->user));
    }

}