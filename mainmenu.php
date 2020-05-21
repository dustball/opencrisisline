<?php

    include 'config.php';
    
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    
?>
<Response>
    <Say>Welcome to <? echo $system_name; ?></Say>
    <Gather numDigits="1" action="menu-digit.php" method="POST">
        <?
    
        for ($i=0;$i<3;$i++) {
            echo "<Say>To speak with any $volunteer, press 1.</Say>";
            if ($option2_friendly) {
                echo "<Say>$option2_friendly - press 2.</Say>";    
            }
            if ($option3_friendly) {
                echo "<Say>$option3_friendly - press 3.</Say>";    
            }
            echo '<Pause length="2"/>';
        }
        
        ?>
        
        <Pause length="5"/>
        
    </Gather>
</Response>