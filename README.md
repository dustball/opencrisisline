# Open Crisis Line

## About

This is an open-source suicide / crisis line that runs on PHP and MySQL.

A paid Twilio account is required.

## Setup

1. Make sure PHP 5.6 is working on your web host
2. `cp config.sample config.php` and edit
3. `php setup.php` to test setup and create database schema 
4. Go to web page and test


## Dreamhost Installation

Open Crisis Line can be installed anywhere, but custom installation instructions for Dreamhost are provided as a convienence below.

1. Log into Dreamhost control panel
2. Domains -> Manage Domains -> "Add hosting" button.  Fill out the top form called "Fully Hosted".  Create a new user for the site, doesn't matter what PHP mode you choose since we will make our own below.  Make note of the password on the resulting page.
3. FTP & SSH Users -> Manage Users -> choose newly created user -> Show Info -> Edit Access -> SSH ON / Secure connection (FTP disabled)
4. More -> MySQL Databases -> Create new.  Fill out the form, creating a new hostname and a new database user.  Make note of the password.
5. SSH into the account created in step #2 and enter the following commands one line at a time:

```Shell
wget "https://curl.haxx.se/download/curl-7.70.0.tar.gz"
tar -zxvf curl-7.70.0.tar.gz 
rm curl-7.70.0.tar.gz 
cd curl-7.70.0
./configure --prefix=$HOME/curl  
make
make install
cd ~
wget "https://www.php.net/distributions/php-5.6.40.tar.gz"
tar -zxvf php-5.6.40.tar.gz
rm php-5.6.40.tar.gz
cd php-5.6.40
./configure --prefix=/home/`whoami`/local --with-zend-vm=GOTO --enable-cgi --enable-fpm --enable-libxml --enable-bcmath --enable-calendar= --enable-ctype --enable-dom --enable-exif --enable-fileinfo --enable-filter --enable-ftp --enable-hash --enable-intl --enable-json --enable-mbstring --enable-mbregex --enable-mbregex-backtrack --enable-opcache --enable-pcntl --enable-pdo --enable-phar --enable-posix --enable-session --enable-shmop --enable-simplexml --enable-soap --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-tokenizer --enable-wddx --enable-xml --enable-xmlreader --enable-xmlwriter --enable-zip --with-pcre-regex --with-sqlite3 --with-zlib --with-bz2 --with-kerberos --with-gd --with-jpeg-dir=/usr --with-png-dir=/usr --with-zlib-dir=/usr --with-freetype-dir=/usr --with-gettext --with-mhash --with-iconv --with-mysql --with-mysql-sock=/No-MySQL-hostname-was-specified --with-mysqli --enable-mysqlnd --with-pdo-mysql --with-pdo-sqlite --with-readline  --with-curl=/home/`whoami`/curl # 5 minutes
make; echo "Done compiling" | mail you@youremail.com # This will take ~25 minutes
make install
cd ~
export PATH=$HOME/local/bin:$PATH
echo "export PATH=$HOME/local/bin:\$PATH" >> ~/.bash_profile
. ~/.bash_profile
cd *.com  # go into your web directory (this command assumes your domain ends in .com)
git clone https://github.com/dustball/opencrisisline.git
cd opencrisisline
sed "s|PATHTOPHP|${HOME}|" .htaccess.sample > .htaccess
cp config.sample config.php 
pico config.php # edit ...
php setup.php
```
    
6. You should see a "All tests OK" message.

    
    