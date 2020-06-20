<?php

//TODO:: uncomment index.php:HTTPS
//if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
//    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//    header('HTTP/1.1 301 Moved Permanently');
//    header('Location: ' . $location);
//    exit;
//}

header('Cache-Control: no-cache, must-revalidate');                             # *this* instance to be cached anew
header('Expires: Mon, 01 Nov 2016 05:00:00 GMT');                               # date in past forces *this* instance to be cached anew

# among other things, open the database and assign to $db
require_once "config.php";

$sql = "SELECT 1 AS test FROM $table_name LIMIT 1";
if ( !$db->query($sql) ) {
    echo "Please run setup.php first.";
    exit;
}

$loggedin = 0;

# Update form
if (isset($_COOKIE['phonelogin']))
    $phone = $_COOKIE['phonelogin'];
else
    $phone = NULL;

if ( ! isset($_REQUEST['rpost']) )
    $_REQUEST['rpost'] = NULL;

function processREQUEST($index) {
    if (isset($_REQUEST[$index]))
        $retval = 1;
    else
        $retval = 0;
    return $retval;
}

if ($_REQUEST['rpost'] && $phone) {
    $handle = $_REQUEST['handle'];
    $option2 = processREQUEST('option2');
    $option3 = processREQUEST('option3');
    $online = processREQUEST('online');
    $txts = processREQUEST('txts');

    try {  # make a prepared statement which also addresses escaping strings issues
        $sql = "REPLACE INTO $table_name".              # delete record with phone primary key and then insert these values
            " (verified, handle, $option2_column, phone, $option3_column, online, txts) ".
            " VALUES (:verified, :handle, :option2, :phone, :option3, :online, :txts)";
        $sth = $db->prepare($sql);
        $values = array(
            ':verified' => "Y",
            ':handle' => $handle,
            ':option2' => $option2,
            ':phone' => $phone,
            ':option3' => $option3,
            ':online' => $online,
            ':txts' => $txts);
        $sth->execute($values);
    }
    catch (PDOException $e) {
        logAndDie("Failed to run query in #D102:".$e->getMessage().'->'.$e->getCode());
    }
    # Assume logged in if they made it this far
    $loggedin = 1;
    $error = "[OK - Information Saved]";
}

# Log in
if ( ! isset($_REQUEST['password']) )
    $_REQUEST['password'] = NULL;
$password = $_REQUEST['password'];
if ($password) {
    $phone = preg_replace('/\D+/', '', $_REQUEST['phone']);     # replace all non-digits with nothing
    if (strlen($phone) != 10) {
        $error = "Phone must be 10 digits exactly";
        $loggedin = 0;
    } elseif (strtolower($password) == $master_pass) {
        try {
            $sql = "SELECT * FROM $table_name WHERE $phone = :phone";
            $sth = $db->prepare($sql);
            $sth->execute(array('phone' => $phone));
        }
        catch (PDOException $e) {
            logAndDie("Failed to run query in #D103:" . $e->getMessage() .'->'. $e->getCode());
        }
        $row = $sth->fetch(PDO::FETCH_ASSOC);                          # fetch one row into associative array (dictionary)
        setcookie ("phonelogin", $phone, time()+60*60*24*365*3, "/",
            $_SERVER['SERVER_NAME']);
        $loggedin = 1;
    } else {
        $error = "Invalid login";
    }
}

#if 10 digit phone number and correct password entered
if ($phone && $loggedin) {
    try {
        $sql = "SELECT * FROM $table_name WHERE phone = :phone";
        $sth = $db->prepare($sql);
        $sth->execute(array('phone' => $phone));
    }
    catch (PDOException $e) {
        logAndDie("Failed to run query in #D104: " . $e->getMessage() .'->'. $e->getCode());
    }
    $row = $sth->fetch(PDO::FETCH_ASSOC);                              # fetch one row
    $loggedin = 1;
}

?>

<!doctype html>
<html lang="en">
<head>
<title><?php echo $system_name; ?></title>
<meta charset="utf-8">
<link href="/jquery/jquery-ui.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet">
<meta name="viewport" content="width=600" />
<meta name="robots" content="noindex, nofollow, noarchive">
<style>
html, body {margin:0; padding:0; color:#ccc; font-family: 'Droid Sans', sans-serif; }
html {background:#CED4F3}
body {font-size:13pt; line-height:180%; }
.container {background:#333; width:100%; margin-top:21%; padding:0 15% 3em 0;
    -webkit-box-shadow: 0px 0px 33px 7px rgba(0,0,0,.8);
    box-shadow: 0px 0px 33px 7px rgba(0,0,0,.8);
    -moz-box-shadow: 0px 0px 33px 7px rgba(82,82,82,0.6);
box-shadow: 0px 0px 33px 7px rgba(82,82,82,0.6);}
.inner {margin: 0 10%}

h1 {padding-top:1.5em}

.box {margin-top:3em; margin-left:1.5em; margin-right:3.5em; padding-left:1.25em; display:inline-block; border-left:1px
    dotted #666; vertical-align:top}
.header {text-decoration:underline;}
ul {padding-left:0}
li {list-style-type: none}
a {color:inherit; text-decoration:none}
.r {margin-bottom:0}
.about {display:none}

@media only screen and (max-device-width: 800px) {
  .container{width:800px;}
  body {font-size:130%}
  .hero {width:890px}
}

@media only screen and (max-device-width: 640px) {
  .container{width:640px;}
  body {font-size:160%}
  .hero {width:730px}
}
    
</style>
</head>
<body>


<div class="container">
<div class="inner">

<h1><img src="<?php echo $logo; ?>" width=128 align=absmiddle> &nbsp;<?php echo $system_name; ?></h1>

<?php
if ($loggedin && $phone) {          # phone := 10 digit phone number
    echo
        "<p><code>".$friendly_phone. "->" .$phone."</code></p>".
        "<br>".
        "<dd>".
        '<p style="color:yellow">';
    if (isset($error))
        echo $error;
echo<<<STOP_ECHO
        </p>
        <form method=post>
            <input type=hidden name=rpost value=rpost>
            <table cellspacing=5>
                <tr><td align="right">Volunteer Handle:</td>
                    <td>&nbsp;</td>
                    <td><nobr><input type=text name="handle" id="handle" onkeyup="" onblur="" maxlength=40 class="ui-corner-all ui-widget" value='
STOP_ECHO;
                    if (isset($row['handle']))
                        echo htmlspecialchars($row['handle'],ENT_QUOTES);
                    echo "'>";                                                       # closes off the value single quote
echo<<<STOP_ECHO
                    <span id="len"> &nbsp; </span></nobr></td></tr>
                <tr><td align="right">General Phone Pool</td>
                    <td>&nbsp;</td>
                    <td><input 
STOP_ECHO;

    if (isset($row['online']) && $row['online'])                                # isset($row['online']) protects $row['online']
        echo "checked=checked ";
    echo 'type="checkbox" name="online" value="online"></td></tr>'."\n";

    if ($option2_friendly) {
        echo
            '<tr><td align="right">' . $option2_friendly . '</td>' .
            '<td>&nbsp;</td>' .
            '<td><input ';
        if (isset($row[$option2_column]) && $row[$option2_column])              # isset($row[$option2_column]) protects $row[$option2_column]
            echo " checked=checked ";
        echo 'type="checkbox" name="option2" value="option2"></td></tr>'."\n";
    }

    if ($option3_friendly) {
        echo 
            '<tr><td align="right">' . $option3_friendly . '</td>' .
            "<td>&nbsp;</td>".
            "<td><input ";
        if (isset($row[$option3_column]) && $row[$option3_column])              # isset($row[$option3_column]) protects $row[$option3_column]
            echo "checked=checked ";
        echo 'type="checkbox" name="option3" value="option3"></td></tr>'."\n";
    }

    echo
        '<tr style="display:none">'.
            '<td align="right">TXT messages</td>'.
            '<td>&nbsp;</td>'.
            '<td><input ';
    if ($row['txts'])
        echo 'checked=checked';
    echo
        ' type="checkbox" name="txts" value="txts"></td></tr>'."\n".
        '<tr>'.
            '<td align=right>&nbsp;</td>'.
            '<td>&nbsp;</td>'.
            '<td><button  style="margin-top:.5em" onclick="this.form.submit();" class="ui-button ui-corner-all ui-widget">Save</button></td></tr>';
    echo
        "</table>\n".
        "</dd>\n".
        "<br>\n".
        "<p>Instructions:</p>\n".
        "<p>Enter your volunteer handle and sign-up for whichever lists are appropriate.  Most people should join the General Pool.</p>\n";

    if (strpos($option3_friendly,"Graveyard") !== FALSE)
        echo "If you don't mind phone calls at night, also click $option3_friendly.";

    echo "<p>E-mail $admin_email for help.</p>";

    echo "<p>To quit, just uncheck the boxes and hit save. To change phone numbers, uncheck the boxes and login with a new phone number or contact the admin.</p>";

}

else {            # loggedin and/or phone is false -> start login screen (phone # and password)
    echo    
        '<br>'.
        '<h3 style="cursor:pointer">Login</h3>'.
        '<dd>'.
        '<p style="...">'.
        "<p>";
    if (isset($error))
        echo "$error";
echo<<<STOP_ECHO
        </p>
        <form method=post>
            <table cellspacing=5>
            <tr><td align="right">Phone #:</td>
            <td>&nbsp;</td>
STOP_ECHO;
        echo
            '<td><input type=text name="phone" id="fm" class=" ui-corner-all ui-widget" value="'.$phone.'"></td></tr>'."\n".
            '<tr><td align="right">Password:</td>'.
                '<td>&nbsp;</td>'.
                '<td><input type=password name="password" class=" ui-corner-all ui-widget" value="'.$password.'"></td></tr>';
echo<<<STOP_ECHO
            <tr><td align="right">&nbsp;</td>
                <td>&nbsp;</td>
                <td><button  style="margin-top:.5em" onclick="this.form.submit();" class="ui-button ui-corner-all ui-widget">Login</button></td></tr>
            </table>
        </dd>
        </form>
STOP_ECHO;
}
?>


<br/>  
  
<p><i><small>Powered by <a href="https://github.com/dustball/opencrisisline$volunteer">Open Crisis Line</a></small></i></p>
</div>
</div>
</body>
