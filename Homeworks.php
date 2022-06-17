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
    if (isset($_POST["HomeworkID"]) && isset($_POST["Delete"])) {
        DeleteHomework($_POST["HomeworkID"]);
    }
    if ($Member != "学生") {
        echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='CreateHomework.php?ClassID=" . $_GET["ClassID"] . "'\" value=\"布置作业\" />";
        CreateNewLine();
        CreateNewLine();
    }
    $Temp = $Connect->prepare("SELECT HomeworkID,UploadUID,Title,CreateTime,EndTime,NeedUpload FROM Homeworklist WHERE ClassID=? ORDER BY Homeworklist.CreateTime DESC");
    $Temp->bind_param("i", $_GET["ClassID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    CreateText("作业数量：");
    CreateText($Result->num_rows);
    CreateNewLine();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td width=\"10%\">";
    CreateText("作业编号");
    echo "</td>";
    echo "<td width=\"10%\">";
    CreateText("布置人");
    echo "</td>";
    echo "<td width=\"20%\">";
    CreateText("布置时间");
    echo "</td>";
    echo "<td width=\"20%\">";
    CreateText("截止时间");
    echo "</td>";
    echo "<td width=\"20%\">";
    CreateText("标题");
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
        echo GetUserName($RowData[1]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[3]);
        echo "</td>";
        echo "<td>";
        if ($RowData[5]) {
            CreateText($RowData[4]);
        }
        else {
            CreateText("无需提交");
        }
        echo "</td>";
        echo "<td>";
        CreateText($RowData[2]);
        echo "</td>";
        $StatusTemp = $Connect->prepare("SELECT Status FROM Homeworkuploadlist WHERE HomeworkID=? AND UploadUID=?");
        $StatusTemp->bind_param("ss", $RowData[0], $_SESSION["UID"]);
        $StatusTemp->execute();
        $StatusResult = $StatusTemp->get_result();
        echo "<td>";
        if ($StatusResult->num_rows == 0) {
            CreateText(GetHomeworkStatusName(0));
        }
        else {
            $StatusResult->data_seek(0);
            $StatusRowData = $StatusResult->fetch_array(MYSQLI_NUM);
            CreateText(GetHomeworkStatusName($StatusRowData[0]));
        }
        echo "</td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"HomeworkID\" value=\"" . $RowData[0] . "\" />";
        echo "<input class=\"SecondButton\" type=\"button\" onclick=\"window.location='Homework.php?HomeworkID=" . $RowData[0] . "'\" value=\"查看\" />";
        if ($Member != "学生" && $RowData[1] == $_SESSION["UID"]) {
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
