#!/bin/bash

php artisan migrate --force

apache2-foreground

if [[ $workload -eq 'worker' ]]
then
nohup php artisan queue:listen &
fi
