#!/bin/bash

validate_status=`php -f /var/www/html/test.php |grep FAIL |wc -l`

validate_text=`php -f /var/www/html/test.php`
if [ $validate_status -gt 0 ]
then

echo -e "Everything went no so good, guys...HELP ME!!! \n\r Here is the output of tests:\n $validate_text" | mail -aFrom:it-giants-deploy@it-giants.com -s "IT-Giants Test Deploy Status" skyismyworld@gmail.com timur.glushan@gmail.com  && exit 1;
else
echo -e "Everything went great, guys! We are ON AIR!!! \n\r Here is the output of tests:\n $validate_text" | mail -aFrom:it-giants-deploy@it-giants.com -s "IT-Giants Test Deploy Status" skyismyworld@gmail.com timur.glushan@gmail.com && exit 0;
fi
