<?php
    session_start();
    unset($_SESSION['c45_id']);
    unset($_SESSION['c45_username']);
    unset($_SESSION['c45_level']);
    unset($_SESSION['c45_key']);
    unset($_SESSION['c45_last_login']);
    session_destroy();
    header("location:login.php");
