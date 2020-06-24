<?php
    include 'config.php';
    
    # menu-digit.php tells Twilio to call this script when the call ends

    if (isset($_REQUEST['AccountSid']) && $_REQUEST['AccountSid'] != $AccountSid) { # isset "protects" $_REQUEST['AccountSid'] reference
        # Make sure it's Twilio at the other end
        die("Access denied");
    }

    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";    
    echo '<Response>';
    
    # Uncomment to help debug    
    # file_put_contents("called.txt",print_r($_REQUEST,true), FILE_APPEND | LOCK_EX);
    
    $from_number = substr($_REQUEST['From'],-10);                           # get the last 10 characters of the string
    $from_location = ucwords(strtolower($_REQUEST['FromCity'])) . " " . $_REQUEST['FromState'];        # ucowrds := uppercase first character each word
    $status = $_REQUEST['DialCallStatus'];      
    $dialed = $_REQUEST['dialed'];                  
    $seconds = $_REQUEST['DialCallDuration'];  

    
    if ($anonymous === FALSE) {                                     # $anonymous defined in config.php
        if ($status == "completed" && $seconds > 15) {
            # send a text to the phone receiving the inbound call
            # only sent to verified numbers which is not a feature of Twilio trial accounts
            $msg = "[$system_name] FYI, you were just called from $from_number ($from_location). Thank you for helping.";
            sms0($help_line_number,"+1".$dialed,$msg);

            # send a text to the person called into the help line
            # only sent to verified numbers which is not a feature of Twilio trial accounts
            $msg = "[$system_name] To talk the person you just spoke to, contact $dialed - or call back this number to get another random volunteer.";
            sms0($help_line_number,"+1".$from_number,$msg);

            # send a call duration report to the assumed administrator
            if ($report_number) {                                   # $report_number defined in config.php, either NULL or a phone number
                $min = round($seconds / 6) / 10;
                $msg = "[$system_name] $from_number ($from_location) called $dialed for $min min";
                sms0($help_line_number, $report_number, $msg);
            }
        }
    }
    
    echo '</Response>';
    
?>