<?php

include 'config.php';

# This script is run when the caller presses a menu digit from the main menu

if (isset($_REQUEST['AccountSid']) && $_REQUEST['AccountSid'] != $AccountSid) { # isset "protects" $_REQUEST['AccountSid'] reference
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
    $sql = "SELECT phone FROM $table_name WHERE online=1 AND verified='Y' ORDER BY RAND() LIMIT $how_many_to_call ";
} else if($_REQUEST['Digits'] == '2') {
    $sql = "SELECT phone FROM $table_name WHERE $option2_column=1 AND verified='Y' ORDER BY RAND() LIMIT $how_many_to_call ";
} else if($_REQUEST['Digits'] == '3') {
    if (strpos($option3_friendly,"Graveyard") !== FALSE)
        # Bother 50% less people at night
        $how_many_to_call = $how_many_to_call * 2 / 3;
    $sql = "SELECT phone FROM $table_name WHERE $option3_column=1 AND verified='Y' ORDER BY RAND() LIMIT $how_many_to_call ";
} if($_REQUEST['Digits'] == '8') {
    $sql = "SELECT phone FROM $table_name WHERE handle='$admin_handle'";
}

echo '<Response>';

try {
    $result = $db->query($sql);
}
catch (PDOException $e) {
    logAndDie('Failed Query #MD103: ' . $e->getMessage() . '->' . $e->getCode());
}
if ($result->fetchColumn() == 0) {                                              # row count per https://stackoverflow.com/questions/883365/row-count-with-pdo
    echo '<Say>Sorry, a '.$volunteer.' could not be located right now.  Please try again later.</Say>';
}
else {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {                               # fetch a row at a time
        print "<Dial callerId='$help_line_number' action='called.php?dialed=".$row['phone']."'>".$row['phone']."</Dial>";
    }
    echo '<Say>The call failed or the remote party hung up. Goodbye.</Say>';
}

echo '</Response>';
