<img align="right" src="ocl-logo.png" width="220" style="margin:7px; border-radius:10px" />

# Open Crisis Line

## About

This is an open-source suicide / crisis line that runs on PHP and MySQL.

A paid Twilio account is required (see Installation:Twilio setup notes below).  

### How it works

This software creates a help line in the cloud which can be called by anybody that knows the number.  The current version is intended to be used by medium-to-large sized groups and organizations to support themselves, as opposed to a public help line which may have different needs.

With just a web hosting account, PHP 7.x, MySQL and a paid Twilio account (see Installation:Twilio setup notes below), you can launch a crisis line in an area code (and possibly number) of your choosing. Twilio costs $1 per month (for the phone number) plus 2 cents per minute (counting both legs of the connection). 

Once installation (see below) is finished, the administrator should invite volunteers to sign up.  They sign themselves up with their phone number and a master password, specify their handle and pick which phone pools to opt-in to. (Unchecking all the options essentially puts their number on 'hold', which allows volunteers to mark themselves away for vacations or other reasons.)  

The system supports one, two, or three phone pools.  For example, you could have 1) the general pool 2) a group that may have a certain speciality, training or focus and 3) a graveyard (night shift) pool -- volunteers that specifically opt-in to receive calls at any time of day.  When a caller reaches the line, they will be presented with the appropriate number of options.  Example: "To reach any volunteer, press 1. For code red, press 2.  For graveyard / night shift, press 3.".  These are only examples, however, and you can define whatever phone pools you want.

Once an option is selected by the caller, it will call 6 ([configurable](https://github.com/dustball/opencrisisline/blob/master/config.sample)) random people from the appropriate list.  It will connect the call with whomever answers first and hang up on the rest.  If nobody answers -- or a voicemail system picks up -- the caller should hang up and call back for a different random selection.  (Note: callers almost always get through the first time, but your messaging to potential callers should include note of how to handle such a situation.)

At the end of the call, the system will inform both the caller and the volunteer the phone number of the other party.  This option can be disabled by setting `$anonymous = TRUE`  in `config.php`.  

This software is simple but has been reliably deployed for years.

Current status: This project was recently ported to PHP 7.x - please allow a few days for the dust to settle before installing.  Please contact [dustball](https://github.com/dustball) with any questions -- July 1st, 2020.

## Installation

1. Download or clone the repository to your web host
1. `cp config.sample config.php` and edit
1. `php setup.php` to test setup and create database schema 
1. Go to the web page -- i.e. open `index.php` with your browser -- and sign up yourself with a handle matching `$admin_handle` in the config

Twilio setup:

1. Create a Twilio account. If you use this [referral code](https://www.twilio.com/referral/WU8oSC), when you upgrade your account both of our accounts should get credited $10.
1. Buy a local number for $1/mo or a toll-free number for $2/mo.  You can search by area code or even entering words like "HELP".
1. Go to [Twilo's Active Number] (https://www.twilio.com/console/phone-numbers/incoming) page and click on the number.
    1. Scroll down to the second section titled "Voice & Fax".
    1. Enter the full `https://` URL to `mainmenu.php` and `sms.php` on the resulting page (example: [screenshot](https://i.imgur.com/0jy992M.png)).

How to test:

1. Make sure `setup.php` passes all tests 
1. Have a friend call the line or use a test phone - do not use your own phone 
1. The friend should dial "8" (this is a hidden menu option for testing)
1. It should ring your phone.  
1. Optional: answer and talk for more than 15 seconds to trigger the 'call end' code

In case of error, check Twilio's [error logs](https://www.twilio.com/console/debugger) as well as the web access logs (`tail -f ~/logs/*/https/error.log` on Dreamhost, for example).

### Tested Configurations

* Hosting platforms: Tested OK on Mint, Amazon AMI, and Dreamhost.   
* PHP: Tested OK on 7.5 and 7.7 but expected to work with all 7.x versions
* MySQL: Tested OK on 5.7.28 but expected to work any modern MySQL or MariaDB

## Development Team

* Brian Klug ([@dustball](https://github.com/dustball))
* Tom Moore ([@tmoorebetazi](https://github.com/dtmoorebetazi))
