#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

readonly DB_NAME=wall
readonly WEB_VHOST_NAME=wall-api.local
readonly LARAVEL_ROOT=/var/www

# -- Install Dependencies

add-apt-repository ppa:ondrej/php
apt-get update -y
apt-get upgrade -yq

apps=(
  nginx
  curl
  git
  ntp
  zip
  mysql-server
  php7.2
  php7.2-fpm
  php7.2-dev
  php7.2-apcu
  php7.2-cli
  php7.2-curl
  php7.2-gd
  php7.2-mysql
  php7.2-mbstring
  php7.2-xml
  php-pear
  php7.2-zip
)

apt-get install -y ${apps[@]}

# -- Nginx Config

SITE=$(cat <<EOF
server {
    listen 80;
    server_name $WEB_VHOST_NAME;
    root /var/www/public;
	index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php {
        fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root/\$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny  all;
    }
}
EOF
)

# Create VHost for Vault app
if [ ! -f /etc/nginx/sites-available/$WEB_VHOST_NAME ]; then
    echo "$SITE" >> /etc/nginx/sites-available/$WEB_VHOST_NAME
    ln -s /etc/nginx/sites-available/$WEB_VHOST_NAME /etc/nginx/sites-enabled/$WEB_VHOST_NAME
fi

# Nginx Cleanup

if [ -d /var/www/html ]; then
  rm -rf /var/www/html
fi

# -- MySQL Config

MYCNF=$(cat <<EOF
[client]
  user=vagrant
  password="vagrant"
[mysql]
  user=vagrant
  password="vagrant"
[mysqldump]
  user=vagrant
  password="vagrant"
[mysqldiff]
  user=vagrant
  password="vagrant"
EOF
)

if [ ! -f /home/vagrant/.my.cnf ]; then
  echo "$MYCNF" >> /home/vagrant/.my.cnf
  chown vagrant:vagrant /home/vagrant/.my.cnf
  chmod 600 /home/vagrant/.my.cnf
fi

# Create Database

if [ ! -d /var/lib/mysql/$DB_NAME ]; then
  mysql -u root -e "CREATE DATABASE $DB_NAME"
fi

mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'vagrant'@'%' IDENTIFIED BY 'vagrant'"

mysql -u root -e "FLUSH PRIVILEGES"

# -- MySQL Binding
echo "[mysqld]" >> /etc/mysql/conf.d/bind.cnf
echo "bind-address = 0.0.0.0" >> /etc/mysql/conf.d/bind.cnf

# -- Composer

wget --quiet https://getcomposer.org/composer.phar
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
composer self-update

# -- Laravel

if [ -d $LARAVEL_ROOT ]; then
  chmod 777 $LARAVEL_ROOT/storage -R
fi

cd $LARAVEL_ROOT

# -- FrontEnd Tooling

curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
apt-get install -y nodejs
npm i npm@latest -g
npm i yarn -g

# -- Hosts

echo "192.168.50.6 $WEB_VHOST_NAME" | tee -a /etc/hosts

# -- Misc

service nginx restart

# -- Profile

echo "alias phpunit=/var/www/vendor/bin/phpunit --debug" >> /home/vagrant/.profile
echo "cd $LARAVEL_ROOT" >> /home/vagrant/.profile
export PATH="/var/www/scripts:$PATH"
echo "clear" >> /home/vagrant/.profile
