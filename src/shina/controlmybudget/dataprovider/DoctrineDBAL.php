<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 12/02/14
 * Time: 10:45
 */

namespace shina\controlmybudget\dataprovider;


use Doctrine\DBAL\Connection;
use ebussola\goalr\Event;
use shina\controlmybudget\DataProvider;
use shina\controlmybudget\MonthlyGoal;

class DoctrineDBAL implements DataProvider {

    protected $_purchase_table_name = 'purchase';
    protected $_purchase_fields = array('p.id', 'p.date', 'p.place', 'p.amount');

    protected $_monthly_goal_table_name = 'monthly_goal';
    protected $_monthly_goal_fields = array('mg.id', 'mg.month', 'mg.year', 'mg.amount_goal');

    protected $_event_table_name = 'event';
    protected $_event_fields = array('e.id', 'e.monthly_goal_id', 'e.name', 'e.date_start', 'e.date_end',
        'e.variation', 'e.category');

    /**
     * @var Connection
     */
    private $conn;

    public function __construct(Connection $conn) {
        $this->conn = $conn;
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function insertPurchase(array $data) {
        $this->conn->insert('purchase', $data);

        return $this->conn->lastInsertId();
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function updatePurchase($id, array $data) {
        return $this->conn->update('purchase', $data, array('id' => $id)) === 1;
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function savePurchase(array $data) {
        if (isset($data['id']) && $data['id'] != null) {
            $this->updatePurchase($data['id'], $data);

            return $data['id'];
        } else {
            $id = $this->insertPurchase($data);

            return $id;
        }
    }

    /**
     * @param \DateTime $date_start
     * @param \DateTime $date_end
     *
     * @return array
     */
    public function findPurchasesByPeriod(\DateTime $date_start, \DateTime $date_end) {
        $query = $this->conn->createQueryBuilder()
            ->select($this->_purchase_fields)
            ->from($this->_purchase_table_name, 'p')
            ->where('date >= ?')
            ->andWhere('date <= ?');

        $data = $this->conn->executeQuery($query, array(
            $date_start->format('Y-m-d'),
            $date_end->format('Y-m-d')
        ))->fetchAll();

        return $data;
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    public function findPurchaseByHash($hash)
    {
        $query = $this->conn->createQueryBuilder()
            ->select($this->_purchase_fields)
            ->from($this->_purchase_table_name, 'p')
            ->where('hash = ?');

        $data = $this->conn->executeQuery($query, [$hash])->fetch();

        return $data;
    }

    /**
     * @param int $purchase_id
     * @return array
     */
    public function findPurchaseById($purchase_id)
    {
        $query = $this->conn->createQueryBuilder()
            ->select($this->_purchase_fields)
            ->from($this->_purchase_table_name, 'p')
            ->where('id = ?');

        $data = $this->conn->executeQuery($query, [$purchase_id])->fetch();

        return $data;
    }

    /**
     * @param array $data
     *
     * @return int
     * ID of the added object
     */
    public function insertMonthlyGoal(array $data) {
        $events = $data['events'];
        unset($data['events']);

        $this->conn->insert('monthly_goal', $data);
        $monthly_goal_id = $this->conn->lastInsertId();

        $this->saveEvents($events, $monthly_goal_id);

        return $monthly_goal_id;
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function updateMonthlyGoal($id, array $data) {
        $events = $data['events'];
        unset($data['events']);

        $this->conn->update('monthly_goal', $data, array('id' => $data['id']));

        $this->saveEvents($events, $data['id']);
    }

    /**
     * @param int $month
     * @param int $year
     *
     * @return MonthlyGoal[]
     */
    public function findMonthlyGoalsByMonthAndYear($month, $year) {
        $query = $this->conn->createQueryBuilder()
            ->select($this->_monthly_goal_fields)
            ->from($this->_monthly_goal_table_name, 'mg')
            ->where('mg.month = ?')
            ->andWhere('mg.year = ?')
            ->andWhere('mg.is_deleted = 0');
        $monthly_goals_data = $this->conn->executeQuery($query, array(
            $month, $year
        ))->fetchAll();
        $monthly_goals_data = $this->fillWithEvents($monthly_goals_data);

        return $monthly_goals_data;
    }

    /**
     * @param \DateTime $date_start
     * @param \DateTime $date_end
     * @param boolean   $only_forecast
     *
     * @return float
     */
    public function calcAmountByPeriod(\DateTime $date_start, \DateTime $date_end, $only_forecast=false) {
        $query = $this->conn->createQueryBuilder()
            ->select(array('SUM(p.amount) as count_amount'))
            ->from($this->_purchase_table_name, 'p')
            ->where('p.date >= ?')
            ->andWhere('p.date <= ?');

        if ($only_forecast) {
            $query->andWhere('is_forecast = 1');
        }

        $data = $this->conn->executeQuery($query, array(
            $date_start->format('Y-m-d'),
            $date_end->format('Y-m-d')
        ))->fetchAll();

        return (float) reset($data)['count_amount'];
    }

    /**
     * @param int[] $monthly_goal_ids
     *
     * @return MonthlyGoal[]
     */
    public function findMonthlyGoalByIds($monthly_goal_ids) {
        $query = $this->conn->createQueryBuilder()
            ->select($this->_monthly_goal_fields)
            ->from($this->_monthly_goal_table_name, 'mg')
            ->where('mg.id IN (?)')
            ->andWhere('mg.is_deleted = 0');
        $monthly_goals_data = $this->conn->executeQuery($query, array(
            $monthly_goal_ids
        ), array(
            Connection::PARAM_INT_ARRAY
        ))->fetchAll();
        $monthly_goals_data = $this->fillWithEvents($monthly_goals_data);

        return $monthly_goals_data;
    }

    /**
     * @param array $events
     * @param int     $monthly_goal_id
     */
    private function saveEvents($events_data, $monthly_goal_id) {
        $this->conn->delete($this->_event_table_name, ['monthly_goal_id' => $monthly_goal_id]);

        foreach ($events_data as $event_data) {
            $event_data['monthly_goal_id'] = $monthly_goal_id;
            $this->conn->insert('event', $event_data);
        }
    }

    /**
     * @param $monthly_goals_data
     *
     * @return mixed
     */
    private function fillWithEvents($monthly_goals_data) {
        // reducing ids of monthly_goals to monthly_goal_ids
        $monthly_goal_ids = array();
        foreach ($monthly_goals_data as $monthly_goal_data) {
            $monthly_goal_ids[] = $monthly_goal_data['id'];
        }

        $query = $this->conn->createQueryBuilder()
            ->select($this->_event_fields)
            ->from($this->_event_table_name, 'e')
            ->where('monthly_goal_id IN (?)');
        $events_data = $this->conn->executeQuery($query, array(
            $monthly_goal_ids
        ), array(
            Connection::PARAM_INT_ARRAY
        ))->fetchAll();

        // coupling events on monthly_goals
        foreach ($monthly_goals_data as &$monthly_goal_data) {
            $monthly_goal_data['events'] = array();

            foreach ($events_data as $event_data) {
                if ($event_data['monthly_goal_id'] == $monthly_goal_data['id']) {
                    $monthly_goal_data['events'][] = $event_data;
                }
            }
        }

        return $monthly_goals_data;
    }

    /**
     * @param $purchase_id
     * @return bool
     */
    public function deletePurchase($purchase_id)
    {
        return $this->conn->delete($this->_purchase_table_name, ['id' => $purchase_id]) > 0;
    }

    /**
     * @return array
     */
    public function findAllMonthlyGoals($page = 1, $page_size = null)
    {
        $query = $this->conn->createQueryBuilder()
            ->select($this->_monthly_goal_fields)
            ->from($this->_monthly_goal_table_name, 'mg')
            ->where('mg.is_deleted = 0');

        if ($page_size !== null) {
            $query->setFirstResult(($page-1) * $page_size)
                ->setMaxResults($page_size);
        }

        $data = $this->conn->executeQuery($query)->fetchAll();
        $data = $this->fillWithEvents($data);

        return $data;
    }

    /**
     * @param int $monthly_goal_id
     * @return bool
     */
    public function deleteMonthlyGoal($monthly_goal_id)
    {
        return $this->conn->update($this->_monthly_goal_table_name, ['is_deleted' => true], ['id' => $monthly_goal_id]) > 0;
    }

}