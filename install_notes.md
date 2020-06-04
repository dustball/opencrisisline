1. AWS EC2 t2.micro instance
    1.  Ubuntu 20.4 AMI
1.  `sudo apt-get install php libapache2-mod-php php-mysql php-curl binutils gcc make apache2`
    1.  PHP install: `sudo apt-get install php libapache2-mod-php php-mysql`
    1.  PHP curl extension: `sudo apt-get install php-curl`
    1.  ar install: `sudo apt-get install binutils`
    1.  gcc install: `sudo apt-get install gcc`
    1.  make install: `sudo apt-get install make`
    1.  apache install: `sudo apt-get install apache2`
1.  Mint
    1.  `sudo apt-get install build-essential`
        1.  Addresses `configure: error: C compiler cannot create executables` issue in the  `./configure --prefix=$HOME/curl` step
1.  **OPEN PORT 80**
    1.  AWS Console->Compute EC2->NETWORK & SECURITY Security Groups-> Edit Inbound Rules
1.  From README.md
    1.  `wget "https://curl.haxx.se/download/curl-7.70.0.tar.gz"`
        1. slight delay but then quick
    1.  `tar -zxvf curl-7.70.0.tar.gz && rm curl-7.70.0.tar.gz  && cd curl-7.70.0`
    1.  `./configure --prefix=$HOME/curl` 
        1. README.md says 5 minutes but was less about 30 seconds on AWS t2.micro
    1.  `make`
        1. README.md says 4 minutes but was maybe 2 minutes
        1. possible error/warning generated: `make[1]: Nothing to be done for 'all-am'.
    1.  `make install`
    1.  Not done as wanted new PHP
        1.  ~~wget "https://www.php.net/distributions/php-5.6.40.tar.gz"~~
        1.  ~~tar -zxvf php-5.6.40.tar.gz &&  rm php-5.6.40.tar.gz &&  cd php-5.6.40~~
        1.  ~~./configure --prefix=/home/`whoami`/local --with-zend-vm=GOTO --enable-cgi --enable-fpm --enable-libxml --enable-bcmath --enable-calendar= --enable-ctype --enable-dom --enable-exif --enable-fileinfo --enable-filter --enable-ftp --enable-hash --enable-intl --enable-json --enable-mbstring --enable-mbregex --enable-mbregex-backtrack --enable-opcache --enable-pcntl --enable-pdo --enable-phar --enable-posix --enable-session --enable-shmop --enable-simplexml --enable-soap --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-tokenizer --enable-wddx --enable-xml --enable-xmlreader --enable-xmlwriter --enable-zip --with-pcre-regex --with-sqlite3 --with-zlib --with-bz2 --with-kerberos --with-gd --with-jpeg-dir=/usr --with-png-dir=/usr --with-zlib-dir=/usr --with-freetype-dir=/usr --with-gettext --with-mhash --with-iconv --with-mysql --with-mysql-sock=/No-MySQL-hostname-was-specified --with-mysqli --enable-mysqlnd --with-pdo-mysql --with-pdo-sqlite --with-readline  --with-curl=/home/`whoami`/curl # 2 minutes~~
        1.  ~~make; echo "Done compiling" | mail you@youremail.com # This will take ~25 minutes~~
        1.  ~~make install~~
    1.  `cd ~ && export PATH=$HOME/local/bin:$PATH`
    1.  `echo "export PATH=$HOME/local/bin:\$PATH" >> ~/.bash_profile && . ~/.bash_profile`
    ``


