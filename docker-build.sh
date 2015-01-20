#!/bin/bash

docker build -t controlmybudget-se .
./docker-run.sh composer install