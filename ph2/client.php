<?php
if ($_POST['act']=='Listen'){
    if (isset($_POST['command']))file_put_contents('../ph2/stop.txt',$_POST['command']);
}

?>