<?php

    include 'config.php';

    echo "Run this once a year to confirm they still opt-in and -- haven't changed numbers etc.\n";
    echo "\n";
    echo "Before running this script, run the query\n";
    echo "\tUPDATE opencrisisline SET verified=' ' WHERE verified='Y'\n";
    echo "Perhaps from MySQL Workbench or phyMyAdmin.\n";
    echo "\n";
    echo 'If this query is run from MySQL WorkBench, "Safe Update" must be turned off'."\n";
    echo '(Edit->Preferences->SQL Editor (scroll to bottom)->Clear "Safe Updates" and then reconnect'."\n";
    
    if(!defined('STDIN')) {
        die("Please run this from the command-line only.");
    }

    $from = $help_line_number;
        
    # To run this script, first: update opencrisisline set verified=' ' where verified='Y';
    
    $sql = "SELECT * FROM $table_name WHERE verified=' '";
       
    # Uncomment this line for testing purposes (only message the admin)
    #$sql = "select * from $table_name where handle='$admin_handle'";

    try {
        $rows = $db->query($sql)->fetchAll();                                   # hey memory is cheap, fetch all at once
    }
    catch (PDOException $e) {
        logAndDie("Failed Query #V103: " . $e->getMessage() .'->'. $e->getCode());
    }

    foreach ($rows as $row) {
        $name = trim(ucfirst($row['handle']));
        $to = '+1' . $row['phone'];
        $m1 = "$name, thank you for volunteering with the $system_name system!  This is a yearly check to see if we still have (1/2)";
        $m2 = "the right number AND you are still interested in volunteering.  Please reply YES, NO, or HELP on a line by itself. (2/2)";
        print ($m1);
        print ("\n");
        sms0($from,$to,$m1);
        sms0($from,$to,$m2);
        $sql2 = "UPDATE $table_name SET verified='?' WHERE phone='".$row['phone']."'";
        try {
            $result2 = $db->query($sql2);
        }
        catch (PDOException $e) {
            logAndDie("Failed Query #V104: " . $e->getMessage() .'->'. $e->getCode());
        }
        sleep(1);
    }
