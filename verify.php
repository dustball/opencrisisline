<?

    include 'config.php';

    # Run this once a year to confirm they still opt-in and -- haven't changed numbers etc. 
    
    if(!defined('STDIN')) {
        die("Please run this from the command-line only.");
    }

    $from = $help_line_number;
        
    # To run this script, first: update opencrisisline set verified=' ' where verified='Y';
    
    $sql = "select * from $table_name where verified=' '";
       
    # Uncomment this line for testing purposes (only message the admin)
    #$sql = "select * from $table_name where handle='$admin_handle'";

    try {
        $result = $db->query($sql);
    }
    catch (PDOException $e) {
        logAndDie("Failed Query #V103: " . $e->getMessage() .'->'.
            (int)$e->getCode() . array('exception' => $e));
    }

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $name = trim(ucfirst($row['handle']));
        $to = '+1' . $row['phone'];
        $m1 = "$name, thank you for volunteering with the $system_name system!  This is a yearly check to see if we still have (1/2)";
        $m2 = "the right number AND you are still interested in volunteering.  Please reply YES, NO, or HELP on a line by itself. (2/2)";
        print ($m1);
        print ("\n");
        sms0($from,$to,$m1);
        sms0($from,$to,$m2);
        $sql2 = "update $table_name set verified='?' where phone='".$row['phone']."'";
        try {
            $result2 = $db->query($sql2);
        }
        catch (PDOException $e) {
            logAndDie("Failed Query #V104: " . $e->getMessage() .'->'.
                (int)$e->getCode() . array('exception' => $e));
        }
        sleep(1);
    }
    
    
?>