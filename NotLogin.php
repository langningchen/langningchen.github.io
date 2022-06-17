<?php
    session_start();
    if (!isset($_SESSION["UserName"]) || !isset($_SESSION["UID"]) || !isset($_SESSION["UserType"])) {
        echo "<script>";
        echo "window.location = \"Login.php\"";
        echo "</script>";
		CreateNewLine();
        die();
    }
    else if ($_SESSION["UserType"] == 3) {
        echo "您已被封号";
		CreateNewLine();
		die();
    }
?>
