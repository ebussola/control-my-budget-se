<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 11/08/14
 * Time: 13:41
 */


class OAuthTest extends \Slim_Framework_TestCase
{

    public function testInvalidToken()
    {
        $this->get('/my-daily-budget/1', ['access_token' => 'invalid token']);

        $this->assertEquals(500, $this->response->getStatus());
        $this->assertContains('error', $this->response->getBody());
        $this->assertContains('1001', $this->response->getBody());
    }

}