#!/bin/bash

A=`date +%Y-%m-%d---%H-%M-%S`
php phpunit.phar --debug tests | tee ..\\unittestRuns\\$A.txt
