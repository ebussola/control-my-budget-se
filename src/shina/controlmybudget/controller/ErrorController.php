<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 11/08/14
 * Time: 14:01
 */

namespace shina\controlmybudget\controller;


use Slim\Slim;

class ErrorController
{

    /**
     * @var Slim
     */
    protected $app;

    public function __construct(Slim $app)
    {
        $this->app = $app;
    }

    public function invalidToken()
    {
        $this->app->response->setBody($this->jsonErrorMsg('Invalid Token', 1001));
    }

    protected function jsonErrorMsg($msg, $code=1000)
    {
        return json_encode([
            'error' => [
                'message' => $msg,
                'code' => $code
            ]
        ]);
    }

} 