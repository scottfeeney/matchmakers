#!/bin/bash

A=`date +%Y-%m-%d---%H-%M-%S`
B=`pwd | sed 's|.*/||'`

php phpunit.phar --debug tests | tee "..\\unittestRuns\\$B-$A.txt"
