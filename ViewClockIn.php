<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["ClockInID"])) {
        echo "非法调用：没有传入ClockInID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Temp = $Connect->prepare("SELECT * FROM ClockInlist WHERE ClockInID=?");
    $Temp->bind_param("i", $_GET["ClockInID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows != 1) {
        echo "非法调用：不存在相应的打卡";
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
    if ($Member == "学生") {
        echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    if (isset($_POST["Check"]) && isset($_POST["Data"])) {
        $_POST["Data"] = SanitizeString($_POST["Data"]);
        $Temp = $Connect->prepare("INSERT INTO ClockInuploadchecklist(ClockInUploadID, UploadUID, Data, CheckTime) VALUES (?, ?, ?, current_timestamp())");
        $Temp->bind_param("iis", $_POST["ClockInUploadID"], $_SESSION["UID"], $_POST["Data"]);
        $Temp->execute();
    }
    $Temp = $Connect->prepare("SELECT * FROM ClockInuploadlist WHERE ClockInID=? ORDER BY UploadTime DESC"); 
    $Temp->bind_param("i", $_GET["ClockInID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    for ($i = 0; $i < $Result->num_rows; $i++) {
        echo "<div class=\"CheckClockInDiv\">";
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        echo GetUserName($RowData[2]);
        CreateText("提交日期：" . $RowData[5]);
        CreateText("提交时间：" . $RowData[6]);
        CreateNewLine();
        if ($RowData[3] != "") {
            CreateText($RowData[3]);
            CreateNewLine();
        }
        $FileList = array();
        if ($RowData[4] != "") {
            $TempArray = StringToArray($RowData[4]);
            for ($j = 0; $j < count($TempArray); $j++) {
                $FileIndex = $TempArray[$j];
                $FileTemp = $Connect->prepare("SELECT * FROM ClockInuploadfilelist WHERE ClockInUploadFileID=?");
                $FileTemp->bind_param("i", $FileIndex);
                $FileTemp->execute();
                $FileResult = $FileTemp->get_result();
                if ($FileResult->num_rows == 1) {
                    $FileResult->data_seek(0);
                    $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
                    CreateDownload("ClockInUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[4], $FileRowData[4], "文件：" . $FileRowData[4]);
                }
                else {
                    CreateText("系统错误：找不到此文件");
                }
            }
        }
        CreateNewLine();
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"ClockInUploadID\" value=\"" . $RowData[0] . "\" />";
        echo "<input type=\"input\" class=\"Input\" required=\"required\" name=\"Data\" />";
        echo "<input type=\"submit\" class=\"SecondButton\" name=\"Check\" value=\"评论\" />";
        echo "</form>";
        $CheckTemp = $Connect->prepare("SELECT * FROM ClockInuploadchecklist WHERE ClockInUploadID=? ORDER BY CheckTime DESC");
        $CheckTemp->bind_param("i", $RowData[0]);
        $CheckTemp->execute();
        $CheckResult = $CheckTemp->get_result();
        for ($j = 0; $j < $CheckResult->num_rows; $j++) {
            $CheckResult->data_seek($j);
            $CheckRowData = $CheckResult->fetch_array(MYSQLI_NUM);
            echo GetUserName($CheckRowData[2]);
            CreateText($CheckRowData[3]);
            CreateText($CheckRowData[4]);
            CreateNewLine();
        }
        echo "</div>";
    }
    require_once "footer.php";
?>
