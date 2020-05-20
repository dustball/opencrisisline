![Open Crisis Line](ocl-logo.png){:style="float:right; margin:7px; border-radius:10px"}
# Open Crisis Line

## About

This is an open-source suicide / crisis line that runs on PHP and MySQL.

A paid Twilio account is required.  Use this [referral code](https://www.twilio.com/referral/WU8oSC) to credit both our accounts $10.

### How it works

This software creates a help line that can be called by anybody that knows the number.  It is intended to be used by groups and organizations to support themselves, as opposed to a public help line which would have very different needs.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque sed ullamcorper risus. Nullam quis ultricies metus, a aliquet turpis. Nam non enim eu orci feugiat vehicula nec nec felis. Integer volutpat ac sapien ut commodo. Praesent porta laoreet est vel consectetur. Maecenas iaculis neque quis sem tempus pulvinar. Vestibulum eget erat ipsum. Morbi vulputate quam est, sed imperdiet augue aliquam vel. Pellentesque dui arcu, vulputate lobortis ante sed, rhoncus condimentum elit. Vestibulum fringilla suscipit purus nec finibus. Duis posuere interdum odio, non bibendum mi tristique vel. Ut ac faucibus mauris. Integer sed tincidunt massa.

Duis in dolor eu risus consequat efficitur. Aenean ipsum turpis, porta euismod tincidunt non, pellentesque at lorem. Donec ornare quam sit amet faucibus viverra. Suspendisse rutrum mollis scelerisque. Sed tincidunt lectus sed neque dapibus, eget elementum magna lobortis. Praesent et placerat dolor, at tempor quam. Ut vehicula fringilla enim dignissim maximus. Maecenas lobortis ligula in ante molestie pretium id vel felis. Morbi et fringilla felis. Sed a erat ut nunc vehicula elementum quis vitae libero. Phasellus malesuada, leo ut scelerisque feugiat, odio arcu tristique eros, eget commodo sem nisi eget arcu. In hac habitasse platea dictumst. Vestibulum ut risus ac augue cursus consequat.


## Installation

1. Make sure PHP 5.6 is working on your web host
2. `cp config.sample config.php` and edit
3. `php setup.php` to test setup and create database schema 
4. Go to web page and test /index.php - sign up yourself with a handle matching `$admin_handle` in the config

Twilio setup:

1. Login to Twilio, buy a local number for $1/mo or a Toll-free number for $2/mo.
2. Enter the URL to `mainmenu.php` and `sms.php` on the resulting page ([screenshot](https://i.imgur.com/0jy992M.png)).

How to test:

1. Have a friend or test phone call the number - do not use your own phone 
2. The friend should dial "8" (this is a hidden menu option for testing)
3. It should ring your phone

In case of error, check Twilio's [error logs](https://www.twilio.com/console/debugger) as well as the web access logs (`tail -f ~/logs/*/https/error.log` on DH).

### Dreamhost Installation

Open Crisis Line can be installed anywhere, but custom installation instructions for Dreamhost are provided as a convienence below.

1. Log into Dreamhost control panel
2. Domains -> Manage Domains -> "Add hosting" button.  Fill out the top form called "Fully Hosted".  Create a new user for the site, doesn't matter what PHP mode you choose since we will make our own below.  Make note of the password on the resulting page.
3. Domains -> SSL -> "Add" button -> Let's Encrypt (Free) -> "Select" button
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

    
    