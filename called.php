<?
    include 'config.php';
    
    # menu-digit.php tells Twilio to call this script when the call ends
    
    if ($_REQUEST['AccountSid']!=$AccountSid) {
        # Make sure it's Twilio at the other end
        die("Access denied");
    }

    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";    
    echo '<Response>';
    
    # Uncomment to help debug    
    # file_put_contents("called.txt",print_r($_REQUEST,true), FILE_APPEND | LOCK_EX);
    
    $from = substr($_REQUEST['From'],-10);
    $status = $_REQUEST['DialCallStatus'];      
    $dialed = $_REQUEST['dialed'];                  
    $seconds = $_REQUEST['DialCallDuration'];  
    $from_location = ucwords(strtolower($_REQUEST['FromCity'])) . " " . $_REQUEST['FromState'];
    
    if ($anonymous===FALSE) {        
        if ($status=="completed" && $seconds>15) {
            $msg = "[$system_name] FYI, you were just called from $from ($from_location). Thank you for helping.";
            sms0($help_line_number,"+1".$dialed,$msg);
            $msg = "[$system_name] To talk the person you just spoke to, contact $dialed - or call back this number to get another random volunteer.";
            sms0($help_line_number,"+1".$from,$msg);
            if ($report_number) {
                $min = round($seconds / 6) / 10;
                $msg = "[$system_name] $from ($from_location) called $dialed for $min min";
                sms0($help_line_number,$report_number,$msg);
            }
        }
    }
    
    echo '</Response>';
    
?>