<?php

    include 'config.php';

    # This script is run when the caller presses a menu digit from the main menu

    if ($_REQUEST['AccountSid']!=$AccountSid) {
        # Make sure it's Twilio at the other end
        die("Access denied");
    }

    # Uncomment to help debug    
    # file_put_contents("md.txt",print_r($_REQUEST,true), FILE_APPEND | LOCK_EX);    

    if($_REQUEST['Digits'] != '1' && $_REQUEST['Digits'] != '2' && $_REQUEST['Digits'] != '3' && $_REQUEST['Digits'] != '8') {
        header("Location: mainmenu.php");
        die;
    }
    
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    
    $how_many_to_call = $people_to_call;    
    
    if($_REQUEST['Digits'] == '1') {
        $sql = "select phone from $table_name where online=1 and verified='Y' order by rand() limit $how_many_to_call ";
    } else if($_REQUEST['Digits'] == '2') {
        $sql = "select phone from $table_name where $option2_column=1 and verified='Y' order by rand() limit $how_many_to_call ";
    } else if($_REQUEST['Digits'] == '3') {
        if (strpos($option3_friendly,"Graveyard")!==FALSE) {       
            # Bother 50% less people at night
            $how_many_to_call = $how_many_to_call * 3 / 2;
        }        
        $sql = "select phone from $table_name where $option3_column=1 and verified='Y' order by rand() limit $how_many_to_call ";
    } if($_REQUEST['Digits'] == '8') {
        $sql = "select phone from $table_name where handle='$admin_handle'";
    }
      
    echo '<Response>';
    
    $result = mysql_query($sql) or logAndDie("Failed Query #MD103: ".mysql_error());
    if (mysql_num_rows($result)==0) {
        echo '<Say>Sorry, a '.$volunteer.' could not be located right now.  Please try again later.</Say>';
    } else {        
        while ($row = mysql_fetch_assoc($result)) {
            print "<Dial callerId='$help_line_number' action='called.php?dialed=".$row['phone']."'>".$row['phone']."</Dial>";
        }
        echo '<Say>The call failed or the remote party hung up. Goodbye.</Say>';
    }
    
    echo '</Response>';

?>
