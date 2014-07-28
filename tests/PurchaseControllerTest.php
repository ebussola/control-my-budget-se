<?php

/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 28/07/14
 * Time: 10:25
 */
class PurchaseControllerTest extends Slim_Framework_TestCase
{

    public function testListPurchasesByPeriod()
    {
        /** @var \shina\controlmybudget\PurchaseService $purchase_service */
        $purchase_service = $this->app->purchase_service;

        $purchase1 = new \shina\controlmybudget\Purchase\Purchase();
        $purchase1->date = new \ebussola\common\datatype\datetime\Date('2014-07-05');
        $purchase1->amount = 350.80;
        $purchase1->place = 'Somewhere';
        $purchase_service->save($purchase1);

        $purchase2 = new \shina\controlmybudget\Purchase\Purchase();
        $purchase2->date = new \ebussola\common\datatype\datetime\Date('2014-07-21');
        $purchase2->amount = 200;
        $purchase2->place = 'Somewhere 2';
        $purchase_service->save($purchase2);

        $purchase3 = new \shina\controlmybudget\Purchase\Purchase();
        $purchase3->date = new \ebussola\common\datatype\datetime\Date('2014-07-25');
        $purchase3->amount = 55.50;
        $purchase3->place = 'Somewhere 3';
        $purchase_service->save($purchase3);

        $this->get('/purchases/2014-07-01/2014-07-31');

        $this->assertEquals(200, $this->response->getStatus());
        $data = json_decode($this->response->getBody());
        $this->assertCount(3, $data);
        $this->assertEquals('2014-07-05', $data[0]->date);
        foreach ($data as $purchase_data) {
            $this->assertIsPurchase($purchase_data);
        }
    }

    public function testAddPurchase()
    {
        $this->post(
            '/purchases',
            [
                'purchase' => json_encode(
                    [
                        'date' => '2014-07-28',
                        'amount' => 200,
                        'place' => 'foobar'
                    ]
                )
            ]
        );

        $this->assertEquals(200, $this->response->getStatus());
        $data = json_decode($this->response->getBody());
        $this->assertIsPurchase($data);
    }

    public function testDeletePurchase_InvalidId()
    {
        $this->delete('/purchase/999');

        $this->assertEquals(400, $this->app->response->getStatus());
    }

    public function testDeletePurchase()
    {
        /** @var \shina\controlmybudget\PurchaseService $purchase_service */
        $purchase_service = $this->app->purchase_service;

        $purchase = new \shina\controlmybudget\Purchase\Purchase();
        $purchase->date = new \ebussola\common\datatype\datetime\Date('2014-07-05');
        $purchase->amount = 350.80;
        $purchase->place = 'Somewhere';
        $purchase_service->save($purchase);

        $this->delete('/purchase/' . $purchase->id);

        $this->assertEquals(200, $this->app->response->getStatus());
    }

    public function testEditPurchase()
    {
        /** @var \shina\controlmybudget\PurchaseService $purchase_service */
        $purchase_service = $this->app->purchase_service;

        $purchase = new \shina\controlmybudget\Purchase\Purchase();
        $purchase->date = new \ebussola\common\datatype\datetime\Date('2014-07-05');
        $purchase->amount = 350.80;
        $purchase->place = 'Somewhere';
        $purchase_service->save($purchase);

        $this->post(
            '/purchase/' . $purchase->id,
            [
                'purchase' => json_encode(
                    [
                        'amount' => 3000
                    ]
                )
            ]
        );

        $purchase = $purchase_service->getById($purchase->id);
        $this->assertEquals(3000, $purchase->amount);
    }

    /**
     * @param $purchase_data
     */
    protected function assertIsPurchase($purchase_data)
    {
        $this->assertObjectHasAttribute('id', $purchase_data);
        $this->assertNotNull($purchase_data->id);
        $this->assertObjectHasAttribute('date', $purchase_data);
        $this->assertTrue(is_string($purchase_data->date));
        $this->assertObjectHasAttribute('amount', $purchase_data);
        $this->assertObjectHasAttribute('place', $purchase_data);
    }

}