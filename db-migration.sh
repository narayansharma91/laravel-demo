#!/bin/bash

php artisan migrate --force

apache2-foreground

if [[ $workload -eq 'worker' ]]
then
php artisan queue:listen &
fi
