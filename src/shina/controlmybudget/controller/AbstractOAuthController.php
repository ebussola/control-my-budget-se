<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 08/08/14
 * Time: 11:16
 */

namespace shina\controlmybudget\controller;


use shina\controlmybudget\exceptions\InvalidAccessToken;
use shina\controlmybudget\User;
use shina\controlmybudget\UserService;
use Slim\Slim;

abstract class AbstractOAuthController
{

    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var User
     */
    protected $user;

    public function __construct(Slim $app)
    {
        $this->app = $app;
        $this->user = $this->getAuthenticatedUser();
    }

    /**
     * @return \shina\controlmybudget\User
     * @throws \shina\controlmybudget\exceptions\InvalidAccessToken
     */
    protected function getAuthenticatedUser()
    {
        /** @var UserService $user_service */
        $user_service = $this->app->user_service;
        $access_token = $this->app->request->get('access_token');

        $user = $user_service->getByAccessToken($access_token);
        if (!$user instanceof User) {
            throw new InvalidAccessToken();
        }

        return $user;
    }

}