<?php
    require_once "NotLogin.php";
    require_once "Function.php";
    if (!isset($_GET["URI"])) {
        echo "非法调用：没有传入URI参数";
		CreateNewLine();
		die();
    }
    $Temp = $Connect->prepare("INSERT INTO pagecount(URI, UID, Time) VALUES (?, ?, current_timestamp())");
    $Temp->bind_param("si", $_GET["URI"], $_SESSION["UID"]);
    $Temp->execute();
    echo "OK";
?>
