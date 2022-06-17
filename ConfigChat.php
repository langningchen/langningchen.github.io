<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if ($_SESSION["UserType"] != 2) {
        echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    if (isset($_POST["ID"])) {
        $Temp = $Connect->prepare("DELETE FROM chatlist WHERE ID=?");
        $Temp->bind_param("i", $_POST["ID"]);
        $Temp->execute();
    }
    if (isset($_POST["Send"]) && isset($_POST["From"]) && isset($_POST["To"]) && isset($_POST["Value"])) {
        $Temp = $Connect->prepare("INSERT INTO chatlist(UID, SendUID, ReceiveUID, Data, SendTime) VALUES (?,?,?,?,current_timestamp())");
        $Temp->bind_param("iiis", $_POST["To"], $_POST["From"], $_POST["To"], $_POST["Value"]);
        $Temp->execute();
        $TempIndex = $Temp->insert_id;
        $Temp = $Connect->prepare("INSERT INTO chatlist(UID, SendUID, ReceiveUID, Data, SendTime, TheOther) VALUES (?,?,?,?,current_timestamp(),?)");
        $Temp->bind_param("iiisi", $_POST["From"], $_POST["From"], $_POST["To"], $_POST["Value"], $TempIndex);
        $Temp->execute();
    }
    $Temp = $Connect->prepare("SELECT * FROM chatlist order by SendTime desc");
    $Temp->execute();
    $Result = $Temp->get_result();
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("发送者：");
    echo "<input class=\"Input\" type=\"number\" name=\"From\" />";
    CreateNewLine();
    CreateText("接收者：");
    echo "<input class=\"Input\" type=\"number\" name=\"To\" />";
    CreateNewLine();
    CreateText("内容：");
    CreateNewLine();
    echo "<textarea class=\"Input\" name=\"Value\"></textarea>";
    CreateNewLine();
    echo "<input class=\"MainButton\" type=\"submit\" value=\"发送\" name=\"Send\" />";
    echo "</form>";
    CreateNewLine();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td style=\"width: 10%\">";
    CreateText("编号");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("记录人");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("发送");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("接收");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("发送时间");
    echo "</td>";
    echo "<td style=\"width: 30%\">";
    CreateText("内容");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("另一个");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("操作");
    echo "</td>";
    echo "</thead>";
    echo "<tbody>";
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        echo "<tr>";
        echo "<td>";
        CreateText($RowData[0]);
        echo "</td>";
        echo "<td>";
        echo GetUserName($RowData[1]);
        echo "</td>";
        echo "<td>";
        echo GetUserName($RowData[2]);
        echo "</td>";
        echo "<td>";
        echo GetUserName($RowData[3]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[5]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[4]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[6]);
        echo "</td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"ID\" value=\"$RowData[0]\" />";
        echo "<input class=\"SecondButton\" type=\"submit\" value=\"删除\" />";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "Footer.php";
?>
