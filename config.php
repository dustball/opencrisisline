<?php

# Help line config
$anonymous = FALSE;                  # Setting to FALSE will inform both caller and volunteer who they talked to
#xxx???
$help_line_number = "+12056497551";  # Must include prefix of +1 followed by phone number, no spaces or other characters
#xxx???
$friendly_phone = "(205) 649-7551";  # Human friendly version of the above number
$system_name = "Our Help Line";      # The name for your help line
$logo = "ocl-logo.png";              # URL to logo (relative or absolute OK)
$volunteer = "volunteer";            # What do you call volunteers?
$master_pass = "hunter2";            # See documentation (this password may show up as *'s depending on your text editor)
$people_to_call = 6;                 # How many people to call at once

# Admin config
#xxx???
$admin_email = "twm3@alpinix.com";
#xxx???
$admin_phone = "530.448.9672";
#xxx???
#$admin_handle = "brcaiddevadmin";
#xxx???
#$report_number = "530.448.9672";     # Optional - report all successful calls to this number, set to NULL to disable

# Twilio config
$ApiVersion = "2010-04-01";                          # Do not change
$AccountSid = "+12056497551";  # Get these from https://www.twilio.com/console
$AuthToken = "382e6a104f4860d6b6c13c5de982faa2";

# Database config
$db_host = "brcaiddev.cspzofkgh5ed.us-west-1.rds.amazonaws.com";
$db_name = "brcaiddevtest";
$db_user = "brcaiddevadmin";
$db_pass = "brcaiddevadmin69!";
$db_charset = 'utf8mb4';
#xxx???
$table_name = "brcaiddevtest";

# Additional menu items, comment out or set to NULL to disable them 
$option2_column = "opt2";                      
$option2_friendly = "Code Red";
$option3_column = "opt3";
$option3_friendly = "Graveyard / Night Shift";

require "twilio.php";

function logError($err) {
    global $system_name, $admin_email;
#xxx???
#mail($admin_email,"[$system_name] Error","http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."\n\n".print_r($_REQUEST,true)."\n\n\n".print_r($err,true));
echo($admin_email." [$system_name] Error http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."\n\n".print_r($_REQUEST,true)."\n\n\n".print_r($err,true));
}

function logAndDie($err) {
    logError($err);
    die($err);
}

function get_db() {
    global $db_host, $db_name, $db_charset;
    global $db_user, $db_pass;
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";                                                   # dsn := data source name
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    # try accessing the DBMS and then the database
    try {
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);                                                             # dsn contains db_name. Replaces mysql_connect & mysql_select_db
        }
    catch (PDOException $e) {                                                                                           # Couldn't connect to DBMS or particular database
        logAndDie('Could not connect #C301: '.$e->getMessage().'->'.(int)$e->getCode());
        }

    # make PhpStorm happy that $pdo is always defined.
    if (!isset($pdo))
        $pdo = NULL;        # will never execute as if undefined, it is caught immediately above

    return $pdo;
}

$db = get_db();

function sms0($from,$to,$sms) {
    global $ApiVersion, $AccountSid, $AuthToken;
    
    $client = new TwilioRestClient($AccountSid, $AuthToken);
    $data = array(
        "From" => $from,
        "To" => $to,
        "Body" => $sms
    );

    try {
        $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages","POST",$data);
    }
    catch (TwilioException $e) {
        logAndDie('Twilio Exception #C302: '.$e->getMessage().'->'.(int)$e->getCode());
    }

    return;
}
