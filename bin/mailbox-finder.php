<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 22/04/14
 * Time: 14:10
 */

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'host' => '',
    'port' => 000,
    'login' => '',
    'password' => ''
];

$imap = new \Fetch\Server($config['host'], $config['port']);
$imap->setAuthentication($config['login'], $config['password']);

var_dump(imap_list($imap->getImapStream(), "{{$config['host']}}", "*"));

//var_dump($imap->hasMailBox('[Gmail]/All Mail'));