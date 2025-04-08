<?php
    session_start();
    session_destroy();
    header("Location: https://hackforfun.io");
    exit;
?>
