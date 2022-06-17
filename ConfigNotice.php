<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if ($_SESSION["UserType"] != 2) {
        echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    if (isset($_POST["NoticeID"])) {
        $Temp = $Connect->prepare("DELETE FROM notice WHERE NoticeID=?");
        $Temp->bind_param("s", $_POST["NoticeID"]);
        $Temp->execute();
    }
    if (isset($_POST["Title"]) && isset($_POST["Data"])) {
        $Temp = $Connect->prepare("INSERT INTO notice(UploadUID, Title, Data) VALUES (?, ?, ?)");
        $Temp->bind_param("sss", $_SESSION["UID"], $_POST["Title"], $_POST["Data"]);
        $Temp->execute();
    }
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("新增公告：");
    CreateNewLine();
    CreateText("标题：");
    CreateNewLine();
    echo "<input class=\"Input\" type=\"text\" required=\"required\" name=\"Title\" />";
    CreateNewLine();
    CreateText("内容：");
    CreateNewLine();
    echo "<textarea class=\"Input\" type=\"text\" required=\"required\" name=\"Data\"></textarea>";
    CreateNewLine();
    echo "<input class=\"MainButton\" type=\"submit\" name=\"New\" value=\"增加\" />";
    echo "</form>";
    CreateNewLine();
    $Temp = $Connect->prepare("SELECT * FROM notice");
    $Temp->execute();
    $Result = $Temp->get_result();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td style=\"width: 5%\">";
    CreateText("公告编号");
    echo "</td>";
    echo "<td style=\"width: 5%\">";
    CreateText("发布者");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("发布时间");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("标题");
    echo "</td>";
    echo "<td style=\"width: 50%\">";
    CreateText("内容");
    echo "</td>";
    echo "<td style=\"width: 5%\">";
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
        CreateText($RowData[2]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[3]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[4]);
        echo "</td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"NoticeID\" value=\"$RowData[0]\" />";
        echo "<input class=\"DangerousButton\" type=\"submit\" value=\"删除\" />";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "Footer.php";
?>
