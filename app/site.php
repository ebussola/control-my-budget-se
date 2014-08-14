<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 14/08/14
 * Time: 18:02
 */

$app->get(
    '/register',
    function () use ($app) {
        $app->render('register.php');
    }
);

$app->post(
    '/register',
    function () use ($app) {
        /** @var \shina\controlmybudget\UserService $user_service */
        $user_service = $app->user_service;
        $data = $app->request->post();

        $user = new \shina\controlmybudget\User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->facebook_user_id = $data['facebook_user_id'];

        try {
            $user_service->save($user);

            $app->response->setBody('Danke!');

        } catch (Exception $e) {
            $app->response->setStatus(500);
            $app->response->setBody($e->getMessage());
        }
    }
);