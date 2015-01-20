#!/bin/bash

docker run -it -v "$(pwd)":/var/www -w /var/www -p 80:80 -d controlmybudget-se \
/usr/sbin/apache2ctl -D FOREGROUND

docker ps