<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 12/02/14
 * Time: 10:51
 */

require __DIR__ . '/../vendor/autoload.php';

$config = include __DIR__ . '/../config.php';

$conn = \Doctrine\DBAL\DriverManager::getConnection($config['db']);

$cli = new \Symfony\Component\Console\Application();
$cli->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet(array(
    'db'     => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($conn),
    'dialog' => new \Symfony\Component\Console\Helper\DialogHelper()
)));
$cli->addCommands(array(
    // Migrations Commands
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
));

$cli->run();