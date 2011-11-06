#!/bin/bash

export XDEBUG_CONFIG="idekey=ECLIPSE_DBGP"
php app/console $1
