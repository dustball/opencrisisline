<?

    if (!file_exists("config.php")) {
        die("Please copy config.sample to config.php and edit before running setup.");
    }

    include 'config.php';

	
    $sql = "select 2+2 as test";       
    $result = mysql_query($sql) or die("Failed Query #SE101: ".mysql_error());    
    while ($row = mysql_fetch_assoc($result)) {
		$test = $row['test'];
        if ($test!=4) {
            die("Setup failed MySQL test, please edit config.php");
        }
    }

    $sql = "SELECT 1 as test FROM $table_name LIMIT 1";       
    $result = mysql_query($sql) or {
            # Table does not exist.  Create.
            $sql = "CREATE TABLE `$table_name` (`phone` char(10) NOT NULL, `greendot` int(11) DEFAULT NULL, `online` int(11) DEFAULT NULL, `txts` int(11) DEFAULT NULL, `handle` varchar(50) DEFAULT NULL, `graveyard` int(11) DEFAULT NULL, `verified` varchar(1) DEFAULT ' ', PRIMARY KEY (`phone`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            $result = mysql_query($sql) or die("Failed creating table $table_name: ".mysql_error());    
    }
    
    
    $sql = "SELECT 1 as test FROM $table_name LIMIT 1";       
    $result = mysql_query($sql) or die("Failed Query #SE102: ".mysql_error());    
    while ($row = mysql_fetch_assoc($result)) {
		$test = $row['test']));
        if ($test==1) {
            print "\n\nAll tests OK.\n\n";
        }
    }
    
    

    
?>


