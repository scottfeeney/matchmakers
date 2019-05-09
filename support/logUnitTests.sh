#!/bin/bash

A=`date +%Y-%m-%d---%H-%M-%S`
B=`pwd | sed 's|.*/||'`

php support\\phpunit.phar --bootstrap support\\bootstrap.php --debug tests | tee "support\\unittestRuns\\$B-$A.txt"
