<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_POST["HomeworkID"])) {
        echo "非法调用：没有传入HomeworkID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Temp = $Connect->prepare("SELECT * FROM Homeworklist WHERE HomeworkID=?");
    $Temp->bind_param("i", $_POST["HomeworkID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows != 1) {
        echo "非法调用：不存在相应的作业";
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
    $Temp = $Connect->prepare("SELECT * FROM Homeworkuploadlist WHERE HomeworkID=?");
    $Temp->bind_param("i", $_POST["HomeworkID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    CreateText("正在打包数据……");
    CreateNewLine();
    $ZipFileName = $_POST["HomeworkID"] . "号作业数据包.zip";
    $UploadTimeFileName = $_POST["HomeworkID"] . "号作业上传时间.csv";
    if (file_exists("HomeworkDownloadFile/" . $ZipFileName)) {
        unlink("HomeworkDownloadFile/" . $ZipFileName);
    }
    if (file_exists("HomeworkDownloadFile/" . $UploadTimeFileName)) {
        unlink("HomeworkDownloadFile/" . $UploadTimeFileName);
    }
    $ZipFile = new \ZipArchive;
    $ZipFile->open("HomeworkDownloadFile/" . $ZipFileName, \ZipArchive::CREATE);
    $UploadTimeFile = fopen("HomeworkDownloadFile/" . $UploadTimeFileName, "w");
    fprintf($UploadTimeFile, "用户编号,用户名,提交时间\n");
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if ($RowData[3] != "") {
            $ZipFile->addFromString("用户" . $RowData[2] . "（" . GetNoLinkUserName($RowData[2]) . "）作业内容.txt", $RowData[3]);
        }
		
		$TempArray = StringToArray($RowData[4]);
		for ($i = 0; $i < count($TempArray); $i++) {
            $FileIndex = $TempArray[$i];
            $FileTemp = $Connect->prepare("SELECT * FROM Homeworkuploadfilelist WHERE HomeworkUploadFileID=?");
            $FileTemp->bind_param("i", $FileIndex);
            $FileTemp->execute();
            $FileResult = $FileTemp->get_result();
            if ($Result->num_rows == 1) {
                $FileResult->data_seek(0);
                $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
                $ZipFile->addFile(
                    "HomeworkUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[4], 
                    "用户" . $RowData[2] . "（" . GetNoLinkUserName($RowData[2]) . "）上传的文件：" . $FileRowData[4]
                );
            }
		}
        fprintf($UploadTimeFile, "%d,%s,%s\n", $RowData[2], GetNoLinkUserName($RowData), $RowData[5]);
    }
    fclose($UploadTimeFile);
    $ZipFile->addFile("HomeworkDownloadFile/" . $UploadTimeFileName, $UploadTimeFileName);
    $ZipFile->close();
    CreateNewLine();
    CreateText("打包完成！");
    CreateNewLine();
	CreateDownload("HomeworkDownloadFile/" . $ZipFileName, $ZipFileName);
    CreateNewLine();
    require_once "footer.php";
?>
