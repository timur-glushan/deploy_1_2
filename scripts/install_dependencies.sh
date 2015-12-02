#!/bin/bash
debconf-set-selections <<< 'mysql-server mysql-server/root_password password your_password'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password your_password'
apt-get install -y mailutils apache2 php5 mysql-server php5-mysql php5-curl
