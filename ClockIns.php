<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["ClassID"])) {
        echo "非法调用：没有传入ClassID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Member = GetClassType($_GET["ClassID"]);
    if ($Member == "") {
        echo "非法调用：您不在当前班级中";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    if (isset($_POST["ClockInID"]) && isset($_POST["Delete"])) {
        DeleteClockIn($_POST["ClockInID"]);
    }
    if ($Member != "学生") {
        echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='CreateClockIn.php?ClassID=" . $_GET["ClassID"] . "'\" value=\"新建打卡\" />";
        CreateNewLine();
    }
    $Temp = $Connect->prepare("SELECT * FROM clockinlist WHERE ClassID=? ORDER BY CreateTime DESC");
    $Temp->bind_param("i", $_GET["ClassID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    CreateNewLine();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td width=\"10%\">";
    CreateText("打卡编号");
    echo "</td>";
    echo "<td width=\"10%\">";
    CreateText("布置人");
    echo "</td>";
    echo "<td width=\"20%\">";
    CreateText("标题");
    echo "</td>";
    echo "<td width=\"20%\">";
    CreateText("开始日期");
    echo "</td>";
    echo "<td width=\"20%\">";
    CreateText("结束日期");
    echo "</td>";
    echo "<td width=\"10%\">";
    CreateText("状态");
    echo "</td>";
    echo "<td width=\"10%\">";
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
        echo GetUserName($RowData[2]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[3]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[5]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[6]);
        echo "</td>";
        echo "<td>";
        if (strcmp($RowData[6], date("Y-m-d")) < 0) {
            CreateText("已结束");
        }
        else {
            CreateText("正在进行");
        }
        echo "</td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"ClockInID\" value=\"" . $RowData[0] . "\" />";
        echo "<input type=\"button\" onclick=\"window.location='ClockIn.php?ClockInID=" . $RowData[0] . "'\" class=\"SecondButton\" value=\"查看\" />";
        if ($RowData[2] == $_SESSION["UID"]) {
            echo "<input class=\"SecondButton\" type=\"submit\" name=\"Delete\" value=\"删除\"/>";
        }
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "footer.php";
?>
