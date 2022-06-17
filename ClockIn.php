    <?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["ClockInID"])) {
        echo "非法调用：没有传入ClockInID参数";
		CreateNewLine();
        require_once "Footer.php";
        die();
    }
    $Temp = $Connect->prepare("SELECT * FROM clockinlist WHERE ClockInID=?");
    $Temp->bind_param("i", $_GET["ClockInID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows == 0) {
        echo "非法调用：没有这个打卡";
		CreateNewLine();
        require_once "Footer.php";
        die();
    }
    $Result->data_seek(0);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    $Member = GetClassType($RowData[1]);
    if ($Member == "") {
        echo "非法调用：您不在当前班级中";
		CreateNewLine();
        require_once "Footer.php";
        die();
    }
    CreateText("打卡编号：");
    CreateText($RowData[0]);
    CreateNewLine();
    CreateText("布置人：");
    CreateText(GetUserName($RowData[2]));
    CreateNewLine();
    CreateText("开始日期：");
    CreateText($RowData[5]);
    CreateNewLine();
    CreateText("结束日期：");
    CreateText($RowData[6]);
    CreateNewLine();
    CreateText("允许补打卡：");
    CreateText($RowData[7] ? "允许" : "不允许");
    CreateNewLine();
    CreateText("标题：");
    CreateText($RowData[3]);
    CreateNewLine();
    CreateText("内容：");
    CreateText($RowData[4]);
    CreateNewLine();
    echo "<table border=\"1\">";
    echo "<thead>";
    echo "<td>";
    CreateText("日期");
    echo "</td>";
    echo "<td>";
    CreateText("时间");
    echo "</td>";
    echo "<td>";
    CreateText("状态");
    echo "</td>";
    echo "<td>";
    CreateText("操作");
    echo "</td>";
    echo "<td>";
    CreateText("内容");
    echo "</td>";
    echo "<td>";
    CreateText("评论");
    echo "</td>";
    echo "</thead>";
    echo "<tbody>";
    $Year = date("Y");
    $Month = date("m");
    $Day = date("d");
    $Date = $Year . "-" . $Month . "-" . $Day;
    if (strcmp($Date, $RowData[5]) < 0) {
        $Date = $RowData[5];
    }
    while (strcmp($Date, $RowData[5]) >= 0) {
        echo "<tr>";
        $Temp = $Connect->prepare("SELECT * FROM clockinuploadlist WHERE ClockInID=? and UploadUID=? and UploadDate=?");
        $Temp->bind_param("iis", $RowData[0], $_SESSION["UID"], $Date);
        $Temp->execute();
        $Result = $Temp->get_result();
        echo "<td>";
        CreateText($Date);
        echo "</td>";
        if ($Result->num_rows == 0) {
            echo "<td>";
            echo "</td>";
            echo "<td>";
            CreateText(GetClockInStatusName(0));
            echo "</td>";
            echo "<td>";
            if (strcmp($Date, date("Y-m-d")) != 0) {
                if ($RowData[7]) {
                    echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='UploadClockIn.php?ClockInID=" . $RowData[0] . "&UploadDate=" . $Date . "'\" value=\"补卡\" />";
                }
                else {
                    echo "<input class=\"BadButton\" type=\"button\" disabled value=\"已错过\" />";
                }
            }
            else {
                echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='UploadClockIn.php?ClockInID=" . $RowData[0] . "&UploadDate=" . $Date . "'\" value=\"打卡\" />";
            }
            echo "</td>";
            echo "<td>";
            echo "</td>";
            echo "<td>";
            echo "</td>";
        }
        else {
            $Result->data_seek(0);
            CreateNewLine();
            $StatusRowData = $Result->fetch_array(MYSQLI_NUM);
            echo "<td>";
            CreateText($StatusRowData[6]);
            echo "</td>";
            echo "<td>";
            CreateText(GetClockInStatusName($StatusRowData[7]));
            echo "</td>";
            echo "<td>";
            echo "<input class=\"GoodButton\" type=\"button\" disabled value=\"已打卡\" />";
            echo "</td>";
            echo "<td>";
            if ($StatusRowData[3] != "") {
                CreateText($StatusRowData[3]);
                CreateNewLine();
            }
            if ($StatusRowData[4] != "") {
                $TempArray = StringToArray($StatusRowData[4]);
                for ($j = 0; $j < count($TempArray); $j++) {
                    $FileIndex = $TempArray[$j];
                    $FileTemp = $Connect->prepare("SELECT ClockInUploadFileID, ClockInID, UploadUID, FileName FROM clockinuploadfilelist WHERE ClockInUploadFileID=?");
                    $FileTemp->bind_param("i", $FileIndex);
                    $FileTemp->execute();
                    $FileResult = $FileTemp->get_result();
                    if ($FileResult->num_rows == 1) {
                        $FileResult->data_seek(0);
                        $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
						CreateDownload("ClockInUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[3], $FileRowData[3], "文件：" . $FileRowData[3]);
                        CreateNewLine();
                    }
                    else {
                        echo "<input type=\"button\" class=\"BadButton\" value=\"系统错误：找不到此文件\" />";
                        CreateNewLine();
                    }
                }
            }
            echo "</td>";
            echo "<td>";
            $Temp = $Connect->prepare("SELECT * FROM clockinuploadchecklist WHERE ClockInUploadID=? ORDER BY CheckTime DESC");
            $Temp->bind_param("i", $StatusRowData[0]);
            $Temp->execute();
            $Result = $Temp->get_result();
            for ($i = 0; $i < $Result->num_rows; $i++) {
                $Result->data_seek($i);
                $CheckRowData = $Result->fetch_array(MYSQLI_NUM);
                echo GetUserName($CheckRowData[2]);
                CreateText($CheckRowData[3]);
                CreateText($CheckRowData[4]);
                CreateNewLine();
            }
            echo "</td>";
        }
        echo "</td>";
        echo "</tr>";
        $Day--;
        if ($Day == 0) {
            $Month--;
            if ($Month == 1 || $Month == 3 || $Month == 5 || $Month == 7 || $Month == 8 || $Month == 10 || $Month == 12) $Day = 31;
            else if ($Month != 2) $Day = 30;
            else if ($Year % 4 == 0 && $Year % 100 != 0 && $Year % 1000 == 0) $Day = 29;
            else $Day = 28;
        }
        if ($Month == 0) {
            $Year--;
            $Month = 12;
        }
        $Day *= 1;
        $Month *= 1;
        $Year *= 1;
        $Date = $Year . "-";
        if ($Month < 10) {
            $Date .= "0";
        }
        $Date .= $Month . "-";
        if ($Day < 10) {
            $Date .= "0";
        }
        $Date .= $Day;
    }
    echo "</tbody>";
    echo "</table>";
    CreateNewLine();
    if ($RowData[2] == $_SESSION["UID"] || $_SESSION["UserType"] == 2) {
        echo "<input type=\"button\" class=\"GoodButton\" onclick=\"window.location='ViewClockIn.php?ClockInID=" . $RowData[0] . "'\" value=\"在线查看学生打卡记录\" />";
        CreateNewLine();
        echo "<form action=\"DownloadClockIn.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"ClockInID\" value=\"" . $RowData[0] . "\" />";
        echo "<input class=\"MainButton\" type=\"submit\" value=\"下载学生打卡记录\" />";
        echo "</form>";
        CreateNewLine();
    }
    require_once "footer.php";
?>
