#!/bin/bash
cd /home/jsu9stnmttu2/public_html
/usr/local/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600