<?php

if (!$_SESSION) {
    session_start();
}

session_destroy();

header("location:login.php");
