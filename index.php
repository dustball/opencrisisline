<?php

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Nov 2016 05:00:00 GMT');

# open the database
include "config.php";

if (!$db->query("SELECT 1 as test FROM $table_name LIMIT 1")) {
    print ("Please run setup.php first.");
    exit;
}

$loggedin = 0;

$phone = $_COOKIE['phonelogin'];

# Update form
if ($_REQUEST['rpost'] && $phone) {
    $handle = $_REQUEST['handle'];
    $option2 = $_REQUEST['option2']?1:0;
    $option3 = $_REQUEST['option3']?1:0;
    $online = $_REQUEST['online']?1:0;
    $txts = $_REQUEST['txts']?1:0;

    try {
        $sql = $db->prepare("REPLACE INTO $table_name (verified = :verified, handle = :handle,
                            $option2_column, phone = :phone, $option3_column, online = :online, txts = :txts)");
        $sql->execute(array('verified' => 'Y',  'handle' => $handle,    'phone' => $phone,  'online' => $online,
            'txts' => $txts));
    }
    catch (PDOException $e) {
        logAndDie("Failed to run query in #D102:".$e->getMessage().'->'.(int)$e->getCode().array('exception' => $e));
    }
    # Assume logged in if they made it this far
    $loggedin = 1;
    $error = "[OK - Information Saved]";
}

# Log in
if ($_REQUEST['password']) {
    $password = $_REQUEST['password'];
    $phone = preg_replace('/\D+/', '', $_REQUEST['phone']);
    if (strlen($phone) != 10) {
        $error = "Phone must be 10 digits exactly";
        $loggedin = 0;
    } elseif (strtolower($password) == $master_pass) {
        try {
            $sql = $db->prepare("SELECT * FROM $table_name WHERE $phone = :phone");
            $sql->execute(array('phone' => $phone));
        }
        catch (PDOException $e) {
            logAndDie("Failed to run query in #D103:" . $e->getMessage() . '->' .
                (int)$e->getCode() . array('exception' => $e));
        }
        $row = $sql->fetch(PDO::FETCH_ASSOC);                                   # fetch one row
        setcookie ("phonelogin", $phone, time()+60*60*24*365*3, "/",
            $_SERVER['SERVER_NAME']);
        $loggedin = 1;
    } else {
        $error = "Invalid login";
    }
}

if ($phone && $loggedin) {
    try {
        $sql = $db->prepare("select * from $table_name where $phone = :phone");
        $sql->execute(array('phone' => $phone));
    }
    catch (PDOException $e) {
        logAndDie("Failed to run query in #D104:" . $e->getMessage() . '->' .
            (int)$e->getCode() . array('exception' => $e));
    }
    $row = $sql->fetch(PDO::FETCH_ASSOC);                                   # fetch one row
    $loggedin = 1;
}

?><!doctype html>
<html lang="en">
<head>
<title><? echo $system_name; ?></title>
<meta charset="utf-8">
<link href="/jquery/jquery-ui.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet">
<meta name="viewport" content="width=600" />
<meta name="robots" content="noindex, nofollow, noarchive">
<style>
html, body {margin:0; padding:0; color:#ccc; font-family: 'Droid Sans', sans-serif; }
html {background:#CED4F3}
body {font-size:13pt; line-height:180%; }
.container {background:#333; width:100%; margin-top:21%; padding:0 15% 3em 0; -webkit-box-shadow: 0px 0px 33px 7px rgba(0,0,0,.8); box-shadow: 0px 0px 33px 7px rgba(0,0,0,.8);
-moz-box-shadow: 0px 0px 33px 7px rgba(82,82,82,0.6);
box-shadow: 0px 0px 33px 7px rgba(82,82,82,0.6);}
.inner {margin: 0 10%}

h1 {padding-top:1.5em}

.box {margin-top:3em; margin-left:1.5em; margin-right:3.5em; padding-left:1.25em; display:inline-block; border-left:1px dotted #666; vertical-align:top}
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

<h1><img src="<? echo $logo; ?>" width=128 align=absmiddle> &nbsp;<? echo $system_name; ?></h1>

<? if ($loggedin && $phone) { ?>
<p><code><? echo $friendly_phone; ?> -> <? echo $phone; ?></code></p>

<br>
    <dd>
    <p style="color:yellow"><? echo $error; ?></p>
    <form method=post>
    <input type=hidden name=rpost value=rpost>
    <table cellspacing=5>
      <tr><td align=right>Volunteer Handle:</td><td>&nbsp;</td><td><nobr><input type=text name="handle" id="handle" name="handle" onkeyup="" onblur="" maxlength=40 id="fm" class=" ui-corner-all ui-widget" value='<? echo htmlspecialchars($row['handle'],ENT_QUOTES); ?>'> <span id="len">&nbsp;</span></nobr></td></tr>
      <tr><td align=right>General Phone Pool</td><td>&nbsp;</td><td><input <? if ($row['online']) {echo "checked=checked";} ?> type="checkbox" name="online" value="online"></td></tr>      
      <? if ($option2_friendly) { ?>
      <tr><td align=right><? echo $option2_friendly; ?></td><td>&nbsp;</td><td><input <? if ($row[$option2_column]) {echo "checked=checked";} ?> type="checkbox" name="option2" value="option2"></td></tr>
      <? } ?>
      <? if ($option3_friendly) { ?>
      <tr><td align=right><? echo $option3_friendly; ?></td><td>&nbsp;</td><td><input <? if ($row[$option3_column]) {echo "checked=checked";} ?> type="checkbox" name="option3" value="option3"></td></tr>
      <? } ?>
      <tr style="display:none"><td align=right>TXT messages</td><td>&nbsp;</td><td><input <? if ($row['txts']) {echo "checked=checked";} ?> type="checkbox" name="txts" value="txts"></td></tr> 
      <tr><td align=right>&nbsp;</td><td>&nbsp;</td><td><button  style="margin-top:.5em" onclick="this.form.submit();" class="ui-button ui-corner-all ui-widget">Save</button></td></tr>
    </table>
    </dd>
<br>
<p>Instructions:</p>
<p>Enter your volunteer handle and sign-up for whichever lists are appropriate.  Most people should join the General Pool. 

<? if (strpos($option3_friendly,"Graveyard")!==FALSE) { 
    print "If you don't mind phone calls at night, also click $option3_friendly.";
}?>

E-mail <? echo $admin_email; ?> for help.</p>

<p>To quit, just uncheck the boxes and hit save.  To change phone numbers, uncheck the boxes and login with a new phone number or contact the admin.</p>

<? } else { ?>

<br>
  <h3 style="cursor:pointer">Login</h3>
    
    <dd>
    <p style="color:red"><? echo $error; ?></p>
    <form method=post>
    <table cellspacing=5>
    <tr><td align=right>Phone #:</td><td>&nbsp;</td><td><input type=text name="phone" id="fm" class=" ui-corner-all ui-widget" value="<? echo $phone; ?>"></td></tr>
    <tr><td align=right>Password:</td><td>&nbsp;</td><td><input type=password name="password" class=" ui-corner-all ui-widget" value="<? echo $password; ?>"></td></tr>
    <tr><td align=right>&nbsp;</td><td>&nbsp;</td><td><button  style="margin-top:.5em" onclick="this.form.submit();" class="ui-button ui-corner-all ui-widget">Login</button></td></tr>
    </table>
    </dd>
    
    </form>  

<br/>  
  
<? }  ?>

<p><i><small>Powered by <a href="https://github.com/dustball/opencrisisline$volunteer">Open Crisis Line</a></small></i></p>
</div>
</div>
</body>
