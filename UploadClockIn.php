<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["ClockInID"])) {
        echo "非法调用：没有传入ClockInID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    if (!isset($_GET["UploadDate"])) {
        echo "非法调用：没有传入UploadDate参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Temp = $Connect->prepare("SELECT * FROM ClockInlist WHERE ClockInID=?");
    $Temp->bind_param("i", $_GET["ClockInID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows == 0) {
        echo "非法调用：没有此打卡";
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
    $ErrorMessage = "";
    $InputDetail = "";
    $Temp = $Connect->prepare("SELECT * FROM ClockInuploadlist WHERE ClockInID=? and UploadUID=?");
    $Temp->bind_param("ii", $_GET["ClockInID"], $_SESSION["UID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows == 0) {
        if (strcmp($RowData[6], $_GET["UploadDate"]) < 0 && !$RowData[7]) {
            echo "非法调用：已经超过提交时间且老师没有开启补交";
		    CreateNewLine();
			require_once "Footer.php";
			die();
        }
    }
    else {
        echo "非法调用：您已经打卡";
        CreateNewLine();
        require_once "Footer.php";
        die();
    }
    if (isset($_POST["Data"])) {
        $InputDetail = SanitizeString($_POST["Data"]);
    }
    if (isset($_FILES["UploadFile"])) {
        if ($_FILES["UploadFile"]["error"] != 0)
        {
            $ErrorMessage = "上传失败，错误码：" . $_FILES["UploadFile"]["error"];
        }
        else
        {
            $Temp = $Connect->prepare("INSERT INTO ClockInuploadfilelist(ClockInID, UploadUID, UploadTime, FileName, FileType, FileSize) VALUES (?, ?, current_timestamp(), ?, ?, ?)");
            $Temp->bind_param("iisss", $_GET["ClockInID"], $_SESSION["UID"], $_FILES["UploadFile"]["name"], $_FILES["UploadFile"]["type"], $_FILES["UploadFile"]["size"]);
            $Temp->execute();
            move_uploaded_file($_FILES["UploadFile"]["tmp_name"], "ClockInUploadFile/" . $Temp->insert_id . "_" . $_GET["ClockInID"] . "_" . $_SESSION["UID"] . "_" . $_FILES["UploadFile"]["name"]);
        }
    }
    if (isset($_POST["Delete"]) && isset($_POST["ClockInUploadFileID"])) {
        $Temp = $Connect->prepare("SELECT filename FROM ClockInuploadfilelist WHERE ClockInUploadFileID=?");
        $Temp->bind_param("i", $_POST["ClockInUploadFileID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        $Temp = $Connect->prepare("DELETE FROM ClockInuploadfilelist WHERE ClockInUploadFileID=?");
        $Temp->bind_param("i", $_POST["ClockInUploadFileID"]);
        $Temp->execute();
        if (file_exists("ClockInUploadFile/" . $_POST["ClockInUploadFileID"] . "_" . $_GET["ClockInID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
            if (!unlink("ClockInUploadFile/" . $_POST["ClockInUploadFileID"] . "_" . $_GET["ClockInID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
                $ErrorMessage = "删除失败";
            }
        }
        else {
            $ErrorMessage = "删除失败：没有该文件，已将该文件记录删除";
        }
    }
    if (isset($_POST["Submit"]) && isset($_POST["Data"])) {
        $Temp = $Connect->prepare("SELECT ClockInUploadFileID FROM ClockInuploadfilelist WHERE UploadUID=? AND ClockInID=?");
        $Temp->bind_param("ii", $_SESSION["UID"], $_GET["ClockInID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        $FileList = "";
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            $FileList .= $RowData[0] . ",";
        }
        if ($FileList == "" && $InputDetail == "") {
            $ErrorMessage = "请输入内容或上传文件";
        }
        else {
            $ThisStatus = 0;
            $Temp = $Connect->prepare("SELECT Status FROM ClockInuploadlist WHERE ClockInID=? and UploadUID=?");
            $Temp->bind_param("ii", $_GET["ClockInID"], $_SESSION["UID"]);
            $Temp->execute();
            $Result = $Temp->get_result();
            if ($Result->num_rows == 0) {
                $ThisStatus = 1;
            }
            else {
                $Result->data_seek(0);
                $RowData = $Result->fetch_array(MYSQLI_NUM);
                if ($RowData[0] == 2) {
                    $ThisStatus = 3;
                }
                else {
                    $ThisStatus = 1;
                }
                $Temp = $Connect->prepare("DELETE FROM ClockInuploadlist WHERE ClockInID=? and UploadUID=?");
                $Temp->bind_param("ii", $_GET["ClockInID"], $_SESSION["UID"]);
                $Temp->execute();
            }
            $Temp = $Connect->prepare("INSERT INTO ClockInuploadlist(ClockInID, UploadUID, Data, FileList, UploadDate, UploadTime, Status) VALUES (?, ?, ?, ?, ?, current_timestamp(), ?)");
            $Temp->bind_param("iisssi", $_GET["ClockInID"], $_SESSION["UID"], $InputDetail, $FileList, $_GET["UploadDate"], $ThisStatus);
            $Temp->execute();
            $Temp = $Connect->prepare("SELECT ClassID FROM ClockInlist WHERE ClockInID=?");
            $Temp->bind_param("i", $_GET["ClockInID"]);
            $Temp->execute();
            $Result = $Temp->get_result();
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            $ErrorMessage = "<script>window.location=\"ClockIns.php?ClassID=" . $RowData[0] . "\"</script>";
        }
    }
    if (isset($_POST["Rename"]) && isset($_POST["ClockInUploadFileID"]) && isset($_POST["AfterFileName"])) {
        $Temp = $Connect->prepare("SELECT filename FROM ClockInuploadfilelist WHERE ClockInUploadFileID=?");
        $Temp->bind_param("s", $_POST["ClockInUploadFileID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if (file_exists("ClockInUploadFile/" . $_POST["ClockInUploadFileID"] . "_" . $_GET["ClockInID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
            rename("ClockInUploadFile/" . $_POST["ClockInUploadFileID"] . "_" . $_GET["ClockInID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0], "ClockInUploadFile/" . $_POST["ClockInUploadFileID"] . "_" . $_GET["ClockInID"] . "_" . $_SESSION["UID"] . "_" . $_POST["AfterFileName"]);
            $Temp = $Connect->prepare("UPDATE ClockInuploadfilelist SET filename=? WHERE ClockInUploadFileID=?");
            $Temp->bind_param("ss", $_POST["AfterFileName"], $_POST["ClockInUploadFileID"]);
            $Temp->execute();
        }
        else {
            $ErrorMessage = "重命名失败：没有该文件，已将该文件记录删除";
            $Temp = $Connect->prepare("DELETE FROM ClockInuploadfilelist WHERE ClockInUploadFileID=?");
            $Temp->bind_param("i", $_POST["ClockInUploadFileID"]);
            $Temp->execute();
        }
    }
    $Temp = $Connect->prepare("SELECT * FROM ClockInuploadfilelist WHERE UploadUID=? AND ClockInID=?");
    $Temp->bind_param("ii", $_SESSION["UID"], $_GET["ClockInID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input class=\"MainButton\" type=\"submit\" name=\"Submit\" value=\"提交\" />";
    CreateNewLine();
    CreateNewLine();
    CreateText("请输入提交的内容：");
    CreateNewLine();
    echo "<textarea class=\"Input\" name=\"Data\">" . $InputDetail . "</textarea>";
    echo "</form>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\" enctype=\"multipart/form-data\">";
    CreateText("请上传提交的文件：");
    CreateNewLine();
    echo "<input class=\"Input\" type=\"file\" name=\"UploadFile\" required=\"required\" />";
    echo "<input class=\"SecondButton\" type=\"submit\" name=\"Submit\" value=\"上传\" />";
    echo "</form>";
    if ($ErrorMessage != "") {
        echo $ErrorMessage;
        CreateNewLine();
    }
    CreateNewLine();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td style=\"width: 10%\">";
    CreateText("编号");
    echo "</td>";
    echo "<td style=\"width: 40%\">";
    CreateText("文件名");
    echo "</td>";
    echo "<td style=\"width: 20%\">";
    CreateText("文件大小");
    echo "</td>";
    echo "<td style=\"width: 30%\">";
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
        CreateText($RowData[4]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[6]);
        echo "</td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"ClockInUploadFileID\" value=\"$RowData[0]\" />";
        echo "<input class=\"SecondButton\" name=\"Delete\" type=\"submit\" value=\"删除\" />";
        echo "<input class=\"Input\" type=\"text\" required=\"required\" name=\"AfterFileName\" value=\"$RowData[4]\" />";
        echo "<input class=\"SecondButton\" type=\"submit\" name=\"Rename\" value=\"重命名\" />";
		CreateDownload("ClockInUploadFile/" . $RowData[0] . "_" . $RowData[1] . "_" . $RowData[2] . "_" . $RowData[4], $RowData[4]);
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "footer.php";
?>
