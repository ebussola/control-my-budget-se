control-my-budget-se
====================

Control My Budget Standard Edition

* Doctrine DBAL PDO Mysql for persistence layer
* Job Schedule SE for start the workers
* REST API for configuration of goals and events
* Authenticated with Facebook Login

Running
-------

#### Start docker-serve.sh

    ./docker-serve.sh
    
#### Then start JobSchedule or cronjob executing ```control-my-budget:import:email``` hourly

To start JobSchedule:

    php bin/control-my-budget-se.php jobschedule:start <jobschedule-table>