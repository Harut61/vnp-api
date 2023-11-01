#!/bin/sh

cd /ivnews
composer run-script post-install-cmd
composer run-script deploy

cp -ar /ivnews /app