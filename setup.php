<?php

if (!file_exists("config.php")) {
    die("#SE10: Please copy config.sample to config.php and edit before running setup.");
}

if(!defined('STDIN')) {
    die("#SE20: Please run this from the command-line only.");
}

# open the database among other things
include 'config.php';

try {
    $sql = "select 2+2 as test";
    $result = $db->query($sql);                                                 # $db := db handle set by included config.php
}
catch (PDOException $e) {                                                       # Couldn't connect to DBMS or particular database
    logAndDie("Failed Query #SE101: ".$e->getMessage().'->'.(int)$e->getCode());
}

#   should be safe to fetch as errors should have been caught above -> no try/catch
while ($row = $result->fetch(PDO::FETCH_ASSOC)){
    $test = $row['test'];
    if ($test != 4) {
        logAndDie("#SE110: Setup failed MySQL test, please edit config.php");       # not using try/catch as this is a logic issue not an database structure issue
    }
}

try {
    $sql = "SELECT 1 as test FROM $table_name LIMIT 1";
    $result = $db->query($sql);
}
catch (PDOException $e) {
    if ((int)$e->getCode() != 42) {                                               # if error is something else than table does not exist
        logAndDie("#SE120: Failed to query table $table_name ->" . $e->getMessage() . '->' . (int)$e->getCode());
    }
    else {      # Table does not exist.  Create it.
        if (!$option2_column || !$option3_column) {                             # $option2_column & $option3_column defined in included config.php
            die("#SE130: Please don't comment out the schema config until after you've created the schema.\n");
        }

        try {
            $sql = "CREATE TABLE `$table_name` (
                                `phone` char(10) NOT NULL,
                                `handle` varchar(50) DEFAULT NULL, 
                                `online` int(11) DEFAULT NULL, 
                                `$option2_column` int(11) DEFAULT NULL,
                                `$option3_column` int(11) DEFAULT NULL, 
                                `txts` int(11) DEFAULT NULL, 
                                `verified` varchar(1) DEFAULT ' ', 
                                PRIMARY KEY (`phone`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            $response = $db->query($sql);
        }
        catch (PDOException $e) {
            logAndDie("#SE130: Failed creating table $table_name: ".'->'.$e->getMessage().'->'.(int)$e->getCode());
        }
    }
}

try {
    $sql = "SELECT 1 as test union SELECT 1 as test FROM $table_name LIMIT 1";
    $result = $db->query($sql);
}
catch (PDOException $e){
    logAndDie("#SE130: Failed Query ".'->'.$e->getMessage().'->'.(int)$e->getCode());
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $test = $row['test'];
    if ($test==1) {
        if (!$report_number) {
            die("#SE140: Skipping Twilio test since report_number is null.  Consider setting it to your phone number for a minute to pass the test?");
        }
        
        $client = new TwilioRestClient($AccountSid, $AuthToken);
        $data = array(
            "From" => $help_line_number,
            "To" => $report_number,
            "Body" => "Hi! This is setup.php letting you know things are working."
        );
        $response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages","POST",$data);
        
        if($response->IsError) {
            die("\n#SE140: Twilio error: $response->ErrorMessage\n\n");
        }
        
        if ($response->HttpStatus==201) {
            print "\nAll tests OK.\n\n";
        } else {
            print "\n#SE150: Unknown Twilio Error.\n\n";
        }
    } else {
        print "\n#SE160: Unknown MySQL / PHP error.\n\n";
    }
}