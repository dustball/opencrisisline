<?
include 'config.php';

if ($_REQUEST['AccountSid']!=$AccountSid) {
    # Make sure it's Twilio at the other end
    die("Access denied");
}

# This script responds to text messages sent to the line.  The only purpose is to handle opt-in responses caused by running verify.php

header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

echo '<Response>';

$from = substr($_REQUEST['From'],-10);
$body = trim(strtoupper($_REQUEST['Body']));

if ($body=="YES") {
    try {
        $sql = "update $table_name set verified='Y' where phone='$from'";
        $result = $db->query($sql);
    }
    catch (PDOException $e) {
        logAndDie("Failed Query #SM103: ".$e->getMessage(). '->' .$e->getCode());
    }
    print "<Sms>Thank you for verifying your number!  We're all set now.  Thank you for helping.</Sms>";
    print "<Sms>(BONUS TIP: Add this phone number to your contacts as '$system_name' so you recognize a call for help.)</Sms>";
} elseif ($body=="NO") {
    try {
        $sql = "update $table_name set verified='N' where phone='$from'";
        $result = $db->query($sql);
    }
    catch (PDOException $e) {
        logAndDie("Failed Query #SM104: ".$e->getMessage().'->'.$e->getCode());
    }
    print "<Sms>Your number has been removed from the system.  No further action needed.</Sms>";
} elseif ($body=="HELP") {
    print "<Sms>1/3 HELP: Someone, possibly the person that owned your phone number before you, volunteered to help with a phone based help service with</Sms>";
    print "<Sms>2/3 $system_name. We need to know if you are still the same person AND still interested in helping.  Please reply YES or NO to indicate whether</Sms>";
    print "<Sms>3/3 we should keep your number on file.  For additional help or to change numbers, please e-mail $admin_email or text $admin_phone</Sms>";
} else {
    print "<Sms>ERROR: Command not understood. Please e-mail $admin_email if you need help.</Sms>";
}

echo '</Response>';
    
?>