<img align="right" src="ocl-logo.png" width="220" style="margin:7px; border-radius:10px" />

# Open Crisis Line

## About

This is an open-source suicide / crisis line that runs on PHP and MySQL.

A paid Twilio account is required (see Installation:Twilio setup notes below).  

### How it works

This software creates a help line in the cloud which can be called by anybody that knows the number.  The current version is intended to be used by medium-to-large sized groups and organizations to support themselves, as opposed to a public help line which may have different needs.

With just a web hosting account supporting PHP (shown to work with PHP 7.x and  and is believed to run but has not been shown to run in PHP PHP 5.6) and MySQL and paid Twilio account (see Installation:Twilio setup notes below), you can launch a crisis line in an area code (and possibly number) of your choosing. Twilio costs $1 per month (for the phone number) plus 2 cents per minute (counting both legs of the connection). 

Once installation (see below) is finished, the administrator should invite volunteers to sign up.  They sign themselves up with their phone number and a master password, specify their handle and pick which phone pools to opt-in to. (Unchecking all the options essentially puts their number on 'hold', which allows volunteers to mark themselves away for vacations or other reasons.)  

The system supports one, two, or three phone pools.  For example, you could have 1) the general pool 2) a group that may have a certain speciality, training or focus and 3) a graveyard (night shift) pool -- volunteers that specifically opt-in to recieve calls at any time of day.  When a caller reaches the line, they will be presented with the appropriate number of options.  Example: "To reach any volunteer, press 1. For code red, press 2.  For graveyward / night shift, press 3.".  

Once an option is selected by the caller, it will call 6 ([configurable](https://github.com/dustball/opencrisisline/blob/master/config.sample)) random people from the appropriate list.  It will connect the call with whomever answers first and hang up on the rest.  If nobody answers -- or a voicemail system picks up -- the caller should hang up and call back for a different random selection.  (Note: callers almost always get through the first time, but your messaging to potential callers should include note of how to handle such a situation.)

At the end of the call, the system will inform both the caller and the volunteer the phone number of the other party.  This option can be disabled by setting `$anonymous = TRUE`  in `config.php`.  

This software is simple but has been reliably deployed for years.

Notice: this project was just uploaded to GitHub - please allow a few days for the dust to settle before installing.  Please contact [dustball](https://github.com/dustball) with any questions -- May 21th, 2020.

## Installation

1. Shown to run on Mint and Amazon AMI systems and assumed to run on other other Linux systems. Not tested on macOS or Windows
1. Assure that PHP either 5.6 or 7.x is working on your web host (see below for Dreamhost specifics)
1. Known to work against MySQL 5.7.28
2. `cp config.sample config.php` and edit
3. `php setup.php` to test setup and create database schema 
4. Go to the web page -- i.e. open `index.php` with your browser -- and sign up yourself with a handle matching `$admin_handle` in the config

Twilio setup:

1. Create a Twilio account. If you use this [referral code](https://www.twilio.com/referral/WU8oSC), when you upgrade your account both of our accounts should get credited $10.
1. Buy a local number for $1/mo or a toll-free number for $2/mo.  You can search by area code or even entering words like "HELP".
1. Go to [Twilo's Active Number] (https://www.twilio.com/console/phone-numbers/incoming) page and click on the number.
    1. Scroll down to the second section titled "Voice & Fax".
    1.  Enter the full `https://` URL to `mainmenu.php` and `sms.php` on the resulting page (example: [screenshot](https://i.imgur.com/0jy992M.png)).

How to test:

1. Make sure `setup.php` passes all tests 
2. Have a friend call the line or use a test phone - do not use your own phone 
3. The friend should dial "8" (this is a hidden menu option for testing)
4. It should ring your phone.  
5. Optional: answer and talk for more than 15 seconds to trigger the 'call end' code

In case of error, check Twilio's [error logs](https://www.twilio.com/console/debugger) as well as the web access logs (`tail -f ~/logs/*/https/error.log` on DH).

### Dreamhost PHP 5.6.40 Installation

Open Crisis Line can be installed anywhere, but custom installation instructions to install **PHP 5.6.40** for Dreamhost are provided as a convienence below.

1. Log into Dreamhost control panel
2. Domains -> Manage Domains -> "Add hosting" button.  Fill out the top form called "Fully Hosted". Create a new user for the site, doesn't matter what PHP mode you choose since we will make our own below.  Make note of the password on the resulting page.
3. Domains -> SSL -> "Add" button next to host/domain you just created -> Let's Encrypt (Free) -> "Select" button
4. FTP & SSH Users -> Manage Users -> choose newly created user -> Show Info -> Edit Access -> change protocol to SSH ON / Secure connection (FTP disabled)
5. More -> MySQL Databases -> Create new.  Fill out the form, creating a new hostname and a new database user.  Make note of the password.
6. SSH into the account created in step #2 and enter the following commands one line at a time:

```Shell
wget "https://curl.haxx.se/download/curl-7.70.0.tar.gz"
tar -zxvf curl-7.70.0.tar.gz && rm curl-7.70.0.tar.gz  && cd curl-7.70.0
./configure --prefix=$HOME/curl # 5 minutes
make                            # 4 minutes
make install
cd ~
wget "https://www.php.net/distributions/php-5.6.40.tar.gz"
tar -zxvf php-5.6.40.tar.gz &&  rm php-5.6.40.tar.gz &&  cd php-5.6.40
./configure --prefix=/home/`whoami`/local --with-zend-vm=GOTO --enable-cgi --enable-fpm --enable-libxml --enable-bcmath --enable-calendar= --enable-ctype --enable-dom --enable-exif --enable-fileinfo --enable-filter --enable-ftp --enable-hash --enable-intl --enable-json --enable-mbstring --enable-mbregex --enable-mbregex-backtrack --enable-opcache --enable-pcntl --enable-pdo --enable-phar --enable-posix --enable-session --enable-shmop --enable-simplexml --enable-soap --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-tokenizer --enable-wddx --enable-xml --enable-xmlreader --enable-xmlwriter --enable-zip --with-pcre-regex --with-sqlite3 --with-zlib --with-bz2 --with-kerberos --with-gd --with-jpeg-dir=/usr --with-png-dir=/usr --with-zlib-dir=/usr --with-freetype-dir=/usr --with-gettext --with-mhash --with-iconv --with-mysql --with-mysql-sock=/No-MySQL-hostname-was-specified --with-mysqli --enable-mysqlnd --with-pdo-mysql --with-pdo-sqlite --with-readline  --with-curl=/home/`whoami`/curl # 2 minutes
make; echo "Done compiling" | mail you@youremail.com # This will take ~25 minutes
make install
cd ~ && export PATH=$HOME/local/bin:$PATH
echo "export PATH=$HOME/local/bin:\$PATH" >> ~/.bash_profile && . ~/.bash_profile
cd *.com  # go into your web directory (this command assumes your domain ends in .com)
git clone https://github.com/dustball/opencrisisline.git  # Change this to YOUR copy of opencrisisline if you forked it on Github
cd opencrisisline
sed "s|PATHTOPHP|${HOME}|" .htaccess.sample > .htaccess
cp config.sample config.php 
pico config.php # edit ...
php setup.php
```
    
7. You should see a "All tests OK" message.

