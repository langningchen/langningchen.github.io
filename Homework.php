<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["HomeworkID"])) {
        echo "非法调用：没有传入HomeworkID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Temp = $Connect->prepare("SELECT * FROM Homeworklist WHERE HomeworkID=?");
    $Temp->bind_param("i", $_GET["HomeworkID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows != 1) {
        echo "非法调用：不存在相应的班级";
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
    CreateText("作业编号：");
    CreateText($RowData[0]);
    CreateNewLine();
    CreateText("布置人：");
    CreateText(GetUserName($RowData[2]));
    CreateNewLine();
    CreateText("布置时间：");
    CreateText($RowData[5]);
    CreateNewLine();
    CreateText("需要上传：");
    CreateText($RowData[7] ? "需要" : "不需要");
    CreateNewLine();
    if ($RowData[7]) {
        CreateText("截止时间：");
        CreateText($RowData[6]);
        CreateNewLine();
        CreateText("允许补交：");
        CreateText($RowData[8] ? "允许" : "不允许");
        CreateNewLine();
    }
    CreateText("标题：");
    CreateText($RowData[3]);
    CreateNewLine();
    CreateText("内容：");
    CreateText($RowData[4]);
    CreateNewLine();
    if ($RowData[7]) {
        $Temp = $Connect->prepare("SELECT HomeworkUploadID, FileList, UploadTime, Status FROM Homeworkuploadlist WHERE HomeworkID=? and UploadUID=?");
        $Temp->bind_param("ii", $RowData[0], $_SESSION["UID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        if ($Result->num_rows == 0) {
            if (strcmp($RowData[6], date("Y-m-d") . " " . date("H:i:s")) >= 0) {
                echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='UploadHomework.php?HomeworkID=" . $RowData[0] . "'\" value=\"提交\" />";
            }
            else if ($RowData[8]) {
                echo "<input class=\"WarningButton\" type=\"button\" onclick=\"window.location='UploadHomework.php?HomeworkID=" . $RowData[0] . "'\" value=\"补交\" />";
            }
            else {
                echo "<input class=\"BadButton\" type=\"button\" disabled value=\"已过截止时间\" />";
            }
            CreateNewLine();
        }
        else {
            $Result->data_seek(0);
            $StatusRowData = $Result->fetch_array(MYSQLI_NUM);
            CreateText("状态：");
            CreateText(GetHomeworkStatusName($StatusRowData[3]));
            CreateNewLine();
            CreateText("提交时间：");
            CreateText($StatusRowData[2]);
            CreateNewLine();
            $FileList = array();
            if ($StatusRowData[1] != "") {
                $TempArray = StringToArray($StatusRowData[1]);
                for ($j = 0; $j < count($TempArray); $j++) {
                    $FileIndex = $TempArray[$j];
                    $FileTemp = $Connect->prepare("SELECT HomeworkUploadFileID, HomeworkID, UploadUID, FileName FROM Homeworkuploadfilelist WHERE HomeworkUploadFileID=?");
                    $FileTemp->bind_param("i", $FileIndex);
                    $FileTemp->execute();
                    $FileResult = $FileTemp->get_result();
                    if ($FileResult->num_rows == 1) {
                        $FileResult->data_seek(0);
                        $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
                        if (gettype(strpos($FileRowData[3], "jpg")) == "boolean" && 
                            gettype(strpos($FileRowData[3], "jpeg")) == "boolean" && 
                            gettype(strpos($FileRowData[3], "png")) == "boolean" && 
                            gettype(strpos($FileRowData[3], "ico")) == "boolean" && 
                            gettype(strpos($FileRowData[3], "bmp")) == "boolean" && 
                            gettype(strpos($FileRowData[3], "gif")) == "boolean" && 
                            gettype(strpos($FileRowData[3], "webp")) == "boolean"
                        ) {
                            array_push($FileList, array("HomeworkUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_", $FileRowData[3]));
                        }
                        else {
                            echo "<img onclick=\"Draw(this, " . $StatusRowData[0] . ")\" class=\"HomeworkImage\" src=\"" . "HomeworkUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[3] . "\" />";
                        }
                    }
                    else {
                        echo "<input type=\"button\" class=\"BadButton\" value=\"系统错误：找不到此文件\" />";
                    }
                }
            }
            CreateNewLine();
            for ($j = 0; $j < sizeof($FileList); $j++) {
				CreateDownload($FileList[$j][0] . $FileList[$j][1], $FileList[$j][1], "文件：" . $FileList[$j][1]);
            }
            CreateNewLine();
            CreateNewLine();
            if ($StatusRowData[3] == 1) {
                echo "<input class=\"GoodButton\" type=\"button\" disabled value=\"提交未批改\" />";
            }
            if ($StatusRowData[3] == 2) {
                echo "<input class=\"WarningButton\" type=\"button\" onclick=\"window.location='UploadHomework.php?HomeworkID=" . $RowData[0] . "'\" value=\"订正\" />";
            }
            if ($StatusRowData[3] == 3) {
                echo "<input class=\"GoodButton\" type=\"button\" disabled value=\"订正未批改\" />";
            }
            if ($StatusRowData[3] == 4 || $StatusRowData[3] == 5) {
                echo "<input class=\"GoodButton\" type=\"button\" disabled value=\"已通过\" />";
            }
        }
        CreateNewLine();
        CreateNewLine();
        CreateText("作业评论：");
        CreateNewLine();
        $Temp = $Connect->prepare("SELECT * FROM Homeworkuploadchecklist WHERE HomeworkUploadID=? ORDER BY CheckTime DESC");
        $Temp->bind_param("i", $StatusRowData[0]);
        $Temp->execute();
        $Result = $Temp->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $CheckRowData = $Result->fetch_array(MYSQLI_NUM);
            echo GetUserName($CheckRowData[2]);
            if ($CheckRowData[3] != "") {
                CreateText($CheckRowData[3]);
            }
            if ($CheckRowData[4] != "") {
                CreateNewLine();
                echo "<img class=\"HomeworkImage\" onclick=\"window.open(this.src)\" src=\"HomeworkUploadCheckFile/" . $CheckRowData[0] . "_" . $CheckRowData[4] . "\"></img>";
            }
            CreateText($CheckRowData[5]);
            CreateNewLine();
        }
        CreateNewLine();
        if ($RowData[2] == $_SESSION["UID"] || $_SESSION["UserType"] == 2) {
            echo "<input type=\"button\" class=\"GoodButton\" onclick=\"window.location='ViewHomework.php?HomeworkID=" . $RowData[0] . "'\" value=\"在线查看学生作业\" />";
            CreateNewLine();
            echo "<form action=\"DownloadHomework.php\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"HomeworkID\" value=\"" . $RowData[0] . "\" />";
            echo "<input class=\"MainButton\" type=\"submit\" value=\"下载学生作业记录\" />";
            echo "</form>";
            CreateNewLine();
        }
    }
    require_once "footer.php";
?>
