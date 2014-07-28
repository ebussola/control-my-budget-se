<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 28/07/14
 * Time: 11:09
 */

namespace shina\controlmybudget\controller;


use ebussola\common\datatype\datetime\Date;
use shina\controlmybudget\Purchase;
use Slim\Slim;

class PurchaseController
{

    /**
     * @var Slim
     */
    protected $app;

    public function __construct(Slim $app)
    {
        $this->app = $app;
    }

    public function listByPeriod(Date $date_start, Date $date_end)
    {
        /** @var \shina\controlmybudget\PurchaseService $purchase_service */
        $purchase_service = $this->app->purchase_service;

        $purchases = $purchase_service->getPurchasesByPeriod($date_start, $date_end);

        $this->app->response->setBody(json_encode($this->allToArray($purchases)));
    }

    public function addPurchase()
    {
        /** @var \shina\controlmybudget\PurchaseService $purchase_service */
        $purchase_service = $this->app->purchase_service;

        if ($this->app->request->post()) {
            $data = json_decode($this->app->request->post('purchase'), true);
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }
        $purchase = new \shina\controlmybudget\Purchase\Purchase();
        $this->fillPurchase($purchase, $data);

        $purchase_service->save($purchase);

        $this->app->response->setBody(json_encode($this->toArray($purchase)));
    }

    public function deletePurchase($purchase_id)
    {
        /** @var \shina\controlmybudget\PurchaseService $purchase_service */
        $purchase_service = $this->app->purchase_service;

        if (!$purchase_service->delete($purchase_id)) {
            $this->app->response->setStatus(400);
        }
    }

    public function editPurchase($purchase_id)
    {
        /** @var \shina\controlmybudget\PurchaseService $purchase_service */
        $purchase_service = $this->app->purchase_service;
        $purchase = $purchase_service->getById($purchase_id);
        if ($this->app->request->post()) {
            $data = json_decode($this->app->request->post('purchase'), true);
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $this->fillPurchase($purchase, $data);

        $purchase_service->save($purchase);
    }

    protected function allToArray($purchases)
    {
        $data = [];
        foreach ($purchases as $purchase) {
            $data[] = $this->toArray($purchase);
        }

        return $data;
    }

    protected function toArray(Purchase $purchase)
    {
        $arr = (array) $purchase;
        $arr['date'] = $purchase->date->format('Y-m-d');

        return $arr;
    }

    private function fillPurchase(Purchase &$purchase, $data)
    {
        if (isset($data['date'])) {
            $purchase->date = new Date($data['date']);
        }

        if (isset($data['place'])) {
            $purchase->place = $data['place'];
        }

        if (isset($data['amount'])) {
            $purchase->amount = $data['amount'];
        }
    }

} 