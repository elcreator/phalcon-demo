#!/usr/bin/env bash
set -x
DOMAIN=project.local
PHP_VER=8.1
PHP_BUILD=20210902
PHALCON_VER=v5.1.2
IDE_KEY=PHPSTORM
VM_IP=192.168.56.37
VM_GW=192.168.56.1

sudo apt update
sudo apt upgrade

sudo apt install -y gcc
sudo apt install -y make
sudo apt install -y nginx
sudo adduser --system --no-create-home --shell /bin/false --group --disabled-login nginx

sudo update-alternatives --set php /usr/bin/php${PHP_VER}
sudo update-alternatives --set phar /usr/bin/phar${PHP_VER}
sudo update-alternatives --set phar.phar /usr/bin/phar.phar${PHP_VER}
sudo update-alternatives --set phpize /usr/bin/phpize${PHP_VER}
sudo update-alternatives --set php-config /usr/bin/php-config${PHP_VER}

sudo apt install -y php${PHP_VER}-fpm
sudo apt install -y php${PHP_VER}-cli
sudo apt install -y php${PHP_VER}-psr
sudo apt install -y php${PHP_VER}-bcmath
sudo apt install -y php${PHP_VER}-curl
sudo apt install -y php${PHP_VER}-dev
sudo apt install -y php${PHP_VER}-gd
sudo apt install -y php${PHP_VER}-intl
sudo apt install -y php${PHP_VER}-mbstring
sudo apt install -y php${PHP_VER}-mysql
sudo apt install -y php${PHP_VER}-sqlite3
sudo apt install -y php${PHP_VER}-xml
sudo apt install -y php${PHP_VER}-zip
sudo apt install -y php${PHP_VER}-xdebug

sudo apt-get install -y wget
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password admin'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password admin'
sudo debconf-set-selections <<< 'opensshd-server opensshd-server/PasswordAuthentication password admin'
sudo apt install -y mysql-server
sudo apt install -y mysql-client

sudo apt install -y htop
sudo apt install -y unzip
sudo apt install -y net-tools

cd /tmp || exit

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer

wget http://pear.php.net/go-pear.phar
php go-pear.phar

sudo pecl channel-update pecl.php.net
sudo pecl -d php_suffix=${PHP_VER} install apcu
echo '; priority=20' | sudo tee -a /etc/php/${PHP_VER}/mods-available/apcu.ini
echo 'extension = apcu.so' | sudo tee -a /etc/php/${PHP_VER}/mods-available/apcu.ini
sudo phpenmod -v ${PHP_VER} apcu

wget https://github.com/phalcon/cphalcon/releases/download/${PHALCON_VER}/phalcon-pecl.tgz
pear install phalcon-pecl.tgz

echo '; priority=50' | sudo tee -a /etc/php/${PHP_VER}/mods-available/xdebug.ini
echo "zend_extension = /usr/lib/php/${PHP_BUILD}/xdebug.so" | sudo tee -a /etc/php/${PHP_VER}/mods-available/xdebug.ini
echo 'xdebug.mode = debug' | sudo tee -a /etc/php/${PHP_VER}/mods-available/xdebug.ini
echo 'xdebug.start_with_request = trigger' | sudo tee -a /etc/php/${PHP_VER}/mods-available/xdebug.ini
echo "xdebug.remote_host = \"${VM_GW}\"" | sudo tee -a /etc/php/${PHP_VER}/mods-available/xdebug.ini
echo "xdebug.idekey = \"${IDE_KEY}\"" | sudo tee -a /etc/php/${PHP_VER}/mods-available/xdebug.ini

sudo adduser --quiet --disabled-password --shell /bin/bash --home /home/project --gecos "project" project
echo "project:project" | chpasswd

sudo cp -r /vagrant/* /home/project/

sudo ln -s /home/project/config/php-fpm/project.conf /etc/php/${PHP_VER}/fpm/pool.d/project.conf
sudo cp /home/project/config/php-fpm/phalcon.ini /etc/php/${PHP_VER}/mods-available/
sudo phpenmod -v ${PHP_VER} phalcon
sudo mkdir -p /etc/nginx/sites-enabled
sudo rm -f /etc/nginx/nginx.conf
sudo cp /home/project/config/nginx/nginx.conf /etc/nginx/
sudo ln -s /home/project/config/nginx/project.conf /etc/nginx/sites-enabled/000.project.conf
sudo ln -s /home/project/www /home/project/public_html
sudo mkdir -p /home/project/logs
sudo mkdir -p /home/project/tmp/log
sudo mkdir -p /home/project/tmp/sessions
sudo mkdir -p /home/project/tmp/uploads
sudo mkdir -p /home/project/tmp/cache/i18n
sudo mkdir -p /home/project/tmp/cache/views
sudo chown -R project:project /home/project
sudo chmod 0777 /home/project/tmp

echo "${VM_IP} ${DOMAIN}" | sudo tee -a /etc/hosts

sudo sed -i "s/PasswordAuthentication.*/PasswordAuthentication yes/g" /etc/ssh/sshd_config
sudo systemctl restart sshd

sudo sed -i "s/bind-address.*/bind-address = 0.0.0.0/g" /etc/mysql/mysql.conf.d/mysqld.cnf
sudo sed -i "s/mysqlx-bind-address.*/mysqlx-bind-address = 0.0.0.0/g" /etc/mysql/mysql.conf.d/mysqld.cnf
sudo systemctl restart mysql

mysqladmin -uroot -padmin create project
mysql -uroot -padmin -e "create user 'project'@'localhost' identified by 'project';"
mysql -uroot -padmin -e "grant all privileges on *.* to 'project'@'localhost';"
mysql -uroot -padmin -e "create user 'project'@'%' identified by 'project';"
mysql -uroot -padmin -e "grant all privileges on *.* to 'project'@'%';"
mysql -uroot -padmin -e "flush privileges;"
mysql -uproject -pproject -e "use project; select version();"
mysql -uproject -pproject -e "source /home/project/db/install.sql"
mysql -uproject -pproject -e "source /home/project/db/seed.sql"

sudo mkdir /home/project/certs
sudo usermod -a -G project nginx
sudo openssl dhparam -out /etc/nginx/dhparam.pem 2048
sudo openssl req -x509 -newkey rsa:2048 -nodes -keyout /home/project/certs/${DOMAIN}.key -out /home/project/certs/${DOMAIN}.pem -days 365 -subj "/C=US/ST=Oregon/L=Portland/O=${DOMAIN}/OU=Org/CN=${DOMAIN}"

sudo apt autoremove -y

cp /vagrant/composer.json /tmp
composer install
sudo cp -r vendor /home/project
sudo chown -R project:project /home/project/vendor

sudo phpenmod -v ${PHP_VER} xdebug

sudo systemctl daemon-reload
sudo systemctl restart php${PHP_VER}-fpm
sudo systemctl restart nginx
sudo systemctl enable nginx
