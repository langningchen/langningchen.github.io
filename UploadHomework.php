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
    if ($Result->num_rows == 0) {
        echo "非法调用：没有此作业";
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
    $Temp = $Connect->prepare("SELECT * FROM Homeworkuploadlist WHERE HomeworkID=? and UploadUID=?");
    $Temp->bind_param("ii", $_GET["HomeworkID"], $_SESSION["UID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows == 0) {
        if (strcmp($RowData[6], date("Y-m-d") . " " . date("h:i")) < 0 && !$RowData[8]) {
            echo "非法调用：已经超过提交时间且老师没有开启补交";
		    CreateNewLine();
			require_once "Footer.php";
			die();
        }
    }
    else {
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if ($RowData[6] != 2) {
            echo "非法调用：您已经通过或老师还没有批改您的作业";
		    CreateNewLine();
			require_once "Footer.php";
			die();
        }
        $InputDetail = $RowData[3];
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
            $Temp = $Connect->prepare("INSERT INTO Homeworkuploadfilelist(HomeworkID, UploadUID, UploadTime, FileName, FileType, FileSize) VALUES (?, ?, current_timestamp(), ?, ?, ?)");
            $Temp->bind_param("iisss", $_GET["HomeworkID"], $_SESSION["UID"], $_FILES["UploadFile"]["name"], $_FILES["UploadFile"]["type"], $_FILES["UploadFile"]["size"]);
            $Temp->execute();
            move_uploaded_file($_FILES["UploadFile"]["tmp_name"], "HomeworkUploadFile/" . $Temp->insert_id . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $_FILES["UploadFile"]["name"]);
        }
    }
    if (isset($_POST["Delete"]) && isset($_POST["HomeworkUploadFileID"])) {
        $Temp = $Connect->prepare("SELECT filename FROM Homeworkuploadfilelist WHERE HomeworkUploadFileID=?");
        $Temp->bind_param("i", $_POST["HomeworkUploadFileID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        $Temp = $Connect->prepare("DELETE FROM Homeworkuploadfilelist WHERE HomeworkUploadFileID=?");
        $Temp->bind_param("i", $_POST["HomeworkUploadFileID"]);
        $Temp->execute();
        if (file_exists("HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
            if (!unlink("HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
                $ErrorMessage = "删除失败";
            }
        }
        else {
            $ErrorMessage = "删除失败：没有该文件，已将该文件记录删除";
        }
    }
    if (isset($_POST["Submit"]) && isset($_POST["Data"])) {
        $Temp = $Connect->prepare("SELECT HomeworkUploadFileID FROM Homeworkuploadfilelist WHERE UploadUID=? AND HomeworkID=?");
        $Temp->bind_param("ii", $_SESSION["UID"], $_GET["HomeworkID"]);
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
            $Temp = $Connect->prepare("SELECT Status FROM Homeworkuploadlist WHERE HomeworkID=? and UploadUID=?");
            $Temp->bind_param("ii", $_GET["HomeworkID"], $_SESSION["UID"]);
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
                $Temp = $Connect->prepare("DELETE FROM Homeworkuploadlist WHERE HomeworkID=? and UploadUID=?");
                $Temp->bind_param("ii", $_GET["HomeworkID"], $_SESSION["UID"]);
                $Temp->execute();
            }
            $Temp = $Connect->prepare("INSERT INTO Homeworkuploadlist(HomeworkID, UploadUID, Data, FileList, UploadTime, Status) VALUES (?, ?, ?, ?, current_timestamp(), ?)");
            $Temp->bind_param("iissi", $_GET["HomeworkID"], $_SESSION["UID"], $InputDetail, $FileList, $ThisStatus);
            $Temp->execute();
            $Temp = $Connect->prepare("SELECT ClassID FROM Homeworklist WHERE HomeworkID=?");
            $Temp->bind_param("i", $_GET["HomeworkID"]);
            $Temp->execute();
            $Result = $Temp->get_result();
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            $ErrorMessage = "<script>window.location=\"Homeworks.php?ClassID=" . $RowData[0] . "\"</script>";
        }
    }
    if (isset($_POST["Rename"]) && isset($_POST["HomeworkUploadFileID"]) && isset($_POST["AfterFileName"])) {
        $Temp = $Connect->prepare("SELECT filename FROM Homeworkuploadfilelist WHERE HomeworkUploadFileID=?");
        $Temp->bind_param("s", $_POST["HomeworkUploadFileID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if (file_exists("HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
            rename("HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0], "HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $_POST["AfterFileName"]);
            $Temp = $Connect->prepare("UPDATE Homeworkuploadfilelist SET filename=? WHERE HomeworkUploadFileID=?");
            $Temp->bind_param("ss", $_POST["AfterFileName"], $_POST["HomeworkUploadFileID"]);
            $Temp->execute();
        }
        else {
            $ErrorMessage = "重命名失败：没有该文件，已将该文件记录删除";
            $Temp = $Connect->prepare("DELETE FROM Homeworkuploadfilelist WHERE HomeworkUploadFileID=?");
            $Temp->bind_param("i", $_POST["HomeworkUploadFileID"]);
            $Temp->execute();
        }
    }
    $Temp = $Connect->prepare("SELECT * FROM Homeworkuploadfilelist WHERE UploadUID=? AND HomeworkID=?");
    $Temp->bind_param("ii", $_SESSION["UID"], $_GET["HomeworkID"]);
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
    echo $ErrorMessage;
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
        echo "<input type=\"hidden\" name=\"HomeworkUploadFileID\" value=\"$RowData[0]\" />";
        echo "<input class=\"SecondButton\" name=\"Delete\" type=\"submit\" value=\"删除\" />";
        echo "<input class=\"Input\" type=\"text\" required=\"required\" name=\"AfterFileName\" value=\"$RowData[4]\" />";
        echo "<input class=\"SecondButton\" type=\"submit\" name=\"Rename\" value=\"重命名\" />";
		CreateDownload("HomeworkUploadFile/" . $RowData[0] . "_" . $RowData[1] . "_" . $RowData[2] . "_" . $RowData[4], $RowData[4]);
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "footer.php";
?>
