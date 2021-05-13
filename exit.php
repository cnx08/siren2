<?php
    session_start();
    session_destroy();
    setcookie("id_user", '', time()-3600);
    setcookie("code_user", '', time()-3600);
    header("Location: index.php");

?>