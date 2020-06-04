<?php

if (!file_exists("config.php")) {
    die("Please copy config.sample to config.php and edit before running setup.");
}

if(!defined('STDIN')) {
    die("Please run this from the command-line only.");
}

include 'config.php';

$sql = "select 2+2 as test";       
$result = mysql_query($sql) or die("Failed Query #SE101: ".mysql_error());    
while ($row = mysql_fetch_assoc($result)) {
$test = $row['test'];
    if ($test!=4) {
        die("Setup failed MySQL test, please edit config.php");
    }
}

$sql = "SELECT 1 as test FROM $table_name LIMIT 1";       
$result = mysql_query($sql);
if (!$result) {
        # Table does not exist.  Create.
        if (!$option2_column || !$option3_column) {
            die("Please don't comment out the schema config until after you've created the schema.\n");
        }
        $sql = "CREATE TABLE `$table_name` (`phone` char(10) NOT NULL, `handle` varchar(50) DEFAULT NULL, `online` int(11) DEFAULT NULL, `$option2_column` int(11) DEFAULT NULL,`$option3_column` int(11) DEFAULT NULL, `txts` int(11) DEFAULT NULL, `verified` varchar(1) DEFAULT ' ', PRIMARY KEY (`phone`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        $result = mysql_query($sql) or die("Failed creating table $table_name: ".mysql_error());    
}


$sql = "SELECT 1 as test union SELECT 1 as test FROM $table_name LIMIT 1";       
$result = mysql_query($sql) or die("Failed Query #SE102: ".mysql_error());    
while ($row = mysql_fetch_assoc($result)) {
    $test = $row['test'];
    if ($test==1) {
        
        if (!$report_number) {
            die("Skipping Twilio test since report_number is null.  Consider setting it to your phone number for a minute to pass the test?");
        }
        
        $client = new TwilioRestClient($AccountSid, $AuthToken);
        $data = array(
            "From" => $help_line_number,
            "To" => $report_number,
            "Body" => "Hi! This is setup.php letting you know things are working."
        );
        $response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages","POST",$data);
        
        if($response->IsError) {
            die("\nTwilio error: $response->ErrorMessage\n\n");
        }
        
        if ($response->HttpStatus==201) {
            print "\nAll tests OK.\n\n";
        } else {
            print "\Unknown Twilio Error.\n\n";
        }
    } else {
        print "\nUnknown MySQL / PHP error.\n\n";
    }
}

