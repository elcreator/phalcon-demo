#!/bin/sh -x

PATH=/home/project/bin:$PATH

if [ -z $(which composer) ]; then

  COMPOSER_EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  COMPOSER_ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

  if [ "$COMPOSER_EXPECTED_CHECKSUM" != "$COMPOSER_ACTUAL_CHECKSUM" ]; then
    >&2 echo 'ERROR: Invalid composer installer checksum'
    rm composer-setup.php
    exit 1
  fi

  php composer-setup.php --install-dir=/home/project/bin
  echo $?
  ln -s /home/project/bin/composer.phar /home/project/bin/composer
  rm composer-setup.php

fi

if [ ! -f /home/project/vendor/autoload.php ]; then

  cd /home/project && composer install

fi

XDEBUG_EXISTS=$(php -r 'function_exists("xdebug_info")||die("no");')
if [ ! -z "${XDEBUG_EXISTS}" ]; then

  pecl install xdebug
  docker-php-ext-enable xdebug

fi

exec php-fpm
