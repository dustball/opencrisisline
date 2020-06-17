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

$sql = "SELECT 1 as test FROM $table_name LIMIT 1";
if (!$db->query($sql)) {
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

if ($_REQUEST['rpost'] && $phone) {
    $handle = $_REQUEST['handle'];
    $option2 = $_REQUEST['option2']?1:0;
    $option3 = $_REQUEST['option3']?1:0;
    $online = $_REQUEST['online']?1:0;
    $txts = $_REQUEST['txts']?1:0;

    try {
        $sql = "REPLACE INTO $table_name 
                    VALUES (verified = :verified, handle = :handle, 
                            $option2_column = :option2, phone = :phone, 
                            $option3_column = :option3, online = :online, 
                            txts = :txts);";
        $sth = $db->prepare($sql);
        $sth->execute(array('verified' => "Y",
                            'handle' => $handle,
                            'option2' => $option2,
                            'phone' => $phone,
                            'option3' => $option3,
                            'online' => $online,
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
if ( ! isset($_REQUEST['password']) )
    $_REQUEST['password'] = NULL;
if ($_REQUEST['password']) {
    $password = $_REQUEST['password'];
    $phone = preg_replace('/\D+/', '', $_REQUEST['phone']);
    if (strlen($phone) != 10) {
        $error = "Phone must be 10 digits exactly";
        $loggedin = 0;
    } elseif (strtolower($password) == $master_pass) {
        try {
            $sth = $db->prepare("SELECT * FROM $table_name WHERE $phone = :phone");
            $sth->execute(array('phone' => $phone));
        }
        catch (PDOException $e) {
            logAndDie("Failed to run query in #D103:" . $e->getMessage() . '->' .
                (int)$e->getCode() . array('exception' => $e));
        }
        $row = $sth->fetch(PDO::FETCH_ASSOC);                          # fetch one row into associative array (dictionary)
        setcookie ("phonelogin", $phone, time()+60*60*24*365*3, "/",
            $_SERVER['SERVER_NAME']);
        $loggedin = 1;
    } else {
        $error = "Invalid login";
    }
}

if ($phone && $loggedin) {
    try {
        $sql = "select * from $table_name where $phone = :phone";
        $sth = $db->prepare($sql);
        $sth->execute(array('phone' => $phone));
    }
    catch (PDOException $e) {
        logAndDie("Failed to run query in #D104: " . $e->getMessage() .'->'.
            (int)$e->getCode() . array('exception' => $e));
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

<?php if ($loggedin && $phone) { ?>
<p><code><?php echo $friendly_phone; ?> -> <?php echo $phone; ?></code></p>

<br>
    <dd>
    <p style="color:yellow"><?php if (isset($error)) {echo $error;} ?></p>
    <form method=post>
    <input type=hidden name=rpost value=rpost>
    <table cellspacing=5>
      <tr><td align=right>Volunteer Handle:</td>
          <td>&nbsp;</td>
          <td><nobr><input type=text name="handle" id="handle" name="handle" onkeyup="" onblur="" maxlength=40 id="fm"
                           class=" ui-corner-all ui-widget" value='<?php if (isset($row['handle'])) echo htmlspecialchars($row['handle'],ENT_QUOTES); ?>'>
                  <span id="len">&nbsp;</span></nobr></td></tr>
      <tr><td align=right>General Phone Pool</td>
          <td>&nbsp;</td><td><input <?php if (isset($row['online'])) echo "checked=checked"; ?> type="checkbox" name="online" value="online"></td></tr>
      <?php if ($option2_friendly) { ?>
      <tr><td align=right><?php echo $option2_friendly; ?></td>
          <td>&nbsp;</td>
          <td><input <?php if (isset($row[$option2_column])) echo "checked=checked"; ?> type="checkbox" name="option2" value="option2"></td></tr>
      <?php } ?>
      <?php if ($option3_friendly) { ?>
      <tr><td align=right><?php echo $option3_friendly; ?></td>
          <td>&nbsp;</td>
          <td><input <?php if (isset($row[$option3_column])) echo "checked=checked"; ?> type="checkbox" name="option3" value="option3"></td></tr>
      <?php } ?>
      <tr style="display:none">
          <td align=right>TXT messages</td>
          <td>&nbsp;</td>
          <td><input <?php if (isset($row['txts'])) echo "checked=checked"; ?> type="checkbox" name="txts" value="txts"></td></tr>
      <tr><td align=right>&nbsp;</td>
          <td>&nbsp;</td>
          <td><button  style="margin-top:.5em" onclick="this.form.submit();" class="ui-button ui-corner-all ui-widget">Save</button></td></tr>
    </table>
    </dd>
<br>
<p>Instructions:</p>
<p>Enter your volunteer handle and sign-up for whichever lists are appropriate.  Most people should join the General Pool. 

<?php if (strpos($option3_friendly,"Graveyard") !==FALSE) {
    print "If you don't mind phone calls at night, also click $option3_friendly.";


}?>

E-mail <?php echo $admin_email; ?> for help.</p>

<p>To quit, just uncheck the boxes and hit save.  To change phone numbers, uncheck the boxes and login with a new phone number or contact the admin.</p>

<?php } else { ?>

<br>
  <h3 style="cursor:pointer">Login</h3>
    
    <dd>
    <p style="color:red"><?php if (isset($error)) echo $error; ?></p>
    <form method=post>
    <table cellspacing=5>
    <tr><td align=right>Phone #:</td>
        <td>&nbsp;</td>
        <td><input type=text name="phone" id="fm" class=" ui-corner-all ui-widget" value="<?php echo $phone; ?>"></td></tr>
    <tr><td align=right>Password:</td>
        <td>&nbsp;</td>
        <td><input type=password name="password" class=" ui-corner-all ui-widget" value="<?php echo $password; ?>"></td></tr>
    <tr><td align=right>&nbsp;</td>
        <td>&nbsp;</td>
        <td><button  style="margin-top:.5em" onclick="this.form.submit();" class="ui-button ui-corner-all ui-widget">Login</button></td></tr>
    </table>
    </dd>
    
    </form>  

<br/>  
  
<?php }  ?>

<p><i><small>Powered by <a href="https://github.com/dustball/opencrisisline$volunteer">Open Crisis Line</a></small></i></p>
</div>
</div>
</body>
