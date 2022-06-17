<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["UID"]) || $_GET["UID"] == "") {
		echo "非法调用：没有传入UID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
	}
	if ($_GET["UID"] == $_SESSION["UID"]) {
		echo "非法调用：不能与自己私聊";
		CreateNewLine();
		require_once "Footer.php";
		die();
	}
	if ($_SESSION["UserType"] != 2 && !in_array($_GET["UID"], GetFriends())) {
        echo "非法调用：您没有权限和他私聊";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    if (isset($_POST["ID"])) {
        if (isset($_POST["Delete"])) {
            $Temp = $Connect->prepare("DELETE FROM chatlist WHERE ID=?");
            $Temp->bind_param("i", $_POST["ID"]);
            $Temp->execute();
        }
        else if (isset($_POST["Withdraw"])) {
            $Temp = $Connect->prepare("SELECT TheOther FROM chatlist WHERE ID=?");
            $Temp->bind_param("i", $_POST["ID"]);
            $Temp->execute();
            $Result = $Temp->get_result();
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            $TheOther = $RowData[0];
            $Temp = $Connect->prepare("DELETE FROM chatlist WHERE ID=?");
            $Temp->bind_param("i", $_POST["ID"]);
            $Temp->execute();
            $Temp = $Connect->prepare("DELETE FROM chatlist WHERE ID=?");
            $Temp->bind_param("i", $TheOther);
            $Temp->execute();
        }
    }
    if (isset($_POST["Send"]) && isset($_POST["Value"])) {
        if ($_POST["Value"] != "") {
            $_POST["Value"] = SanitizeString($_POST["Value"]);
            $Temp = $Connect->prepare("INSERT INTO chatlist(UID, SendUID, ReceiveUID, Data, SendTime) VALUES (?, ?, ?, ?, current_timestamp())");
            $Temp->bind_param("iiis", $_GET["UID"], $_SESSION["UID"], $_GET["UID"], $_POST["Value"]);
            $Temp->execute();
            $TempIndex = $Temp->insert_id;
            $Temp = $Connect->prepare("INSERT INTO chatlist(UID, SendUID, ReceiveUID, Data, SendTime, TheOther) VALUES (?, ?, ?, ?, current_timestamp(),?)");
            $Temp->bind_param("iiisi", $_SESSION["UID"], $_SESSION["UID"], $_GET["UID"], $_POST["Value"], $TempIndex);
            $Temp->execute();
        }
    }
    $Temp = $Connect->prepare("SELECT ID, SendUID, ReceiveUID, Data, SendTime, TheOther FROM chatlist WHERE UID=? AND ((SendUID=? AND ReceiveUID=?) OR (SendUID=? AND ReceiveUID=?)) ORDER BY SendTime DESC");
    $Temp->bind_param("iiiii", $_SESSION["UID"], $_GET["UID"], $_SESSION["UID"], $_SESSION["UID"], $_GET["UID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    $NumberOfRows = min($Result->num_rows, 10);
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<textarea class=\"Input\" name=\"Value\"></textarea>";
    CreateNewLine();
    echo "<input class=\"MainButton\" type=\"submit\" value=\"发送\" name=\"Send\" />";
    echo "</form>";
    CreateNewLine();
    echo "<input class=\"SecondButton\" type=\"button\" value=\"刷新\" onclick=\"window.location = window.location\" />";
    CreateNewLine();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td style=\"width: 10%\">"; CreateText("发送"); echo "</td>";
    echo "<td style=\"width: 10%\">"; CreateText("接收"); echo "</td>";
    echo "<td style=\"width: 20%\">"; CreateText("发送时间"); echo "</td>";
    echo "<td style=\"width: 50%\">"; CreateText("内容"); echo "</td>";
    echo "<td style=\"width: 10%\">"; CreateText("操作"); echo "</td>";
    echo "</thead>";
    echo "<tbody>";
    for ($i = 0; $i < $NumberOfRows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        echo "<tr>";
        echo "<td>" . GetUserName($RowData[1]) . "</td>";
        echo "<td>" . GetUserName($RowData[2]) . "</td>";
        echo "<td><span class=\"Text\">" . $RowData[4] . "</span></td>";
        echo "<td><span class=\"Text\">" . $RowData[3] . "</span></td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"ID\" value=\"" . $RowData[0] . "\" />";
        echo "<input class=\"DangerousButton\" type=\"submit\" name=\"Delete\" value=\"删除\" />";
        if ($RowData[1] == $_SESSION["UID"]) {
            echo "<input class=\"WarningButton\" type=\"submit\" name=\"Withdraw\" value=\"撤回\" />";
        }
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "footer.php";
?>
