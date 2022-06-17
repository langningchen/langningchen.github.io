<?php
    require_once "NotLogin.php";
    require_once "Function.php";
    require_once "Header.php";
    $TempArray = GetFriends();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td>";
    CreateText("用户名");
    echo "</td>";
    echo "<td>";
    CreateText("操作");
    echo "</td>";
    echo "</thead>";
    echo "<tbody>";
    for ($i = 0; $i < count($TempArray); $i++) {
        echo "<tr>";
        echo "<td>";
        echo GetUserName($TempArray[$i]);
        echo "</td>";
        echo "<td>";
        CreateLink("Chat.php?UID=" . $TempArray[$i], "进入");
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "footer.php";
?>
