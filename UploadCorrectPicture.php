<?php
    require_once "NotLogin.php";
    require_once "Function.php";
    if (!isset($_POST["HomeworkUploadID"])) {
        echo "非法调用：没有传入HomeworkUploadID参数";
		CreateNewLine();
		die();
    }
    $Temp = $Connect->prepare("SELECT * FROM Homeworkuploadlist WHERE HomeworkUploadID=?");
    $Temp->bind_param("i", $_POST["HomeworkUploadID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows != 1) {
        echo "非法调用：不存在相应的作业上传";
		CreateNewLine();
		die();
    }
    $Result->data_seek(0);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    $Member = GetClassType($RowData[1]);
    if ($Member == "") {
        echo "非法调用：您不在当前班级中";
		CreateNewLine();
		die();
    }
    if ($Member == "学生") {
        echo "非法调用：没有权限";
		CreateNewLine();
		die();
    }
    $Image = $_POST["Image"];
    $Image = str_replace('data:image/png;base64,', '', $Image);
    $Image = str_replace(' ', '+', $Image);
    $ImageData = base64_decode($Image);
    $File = $_SESSION["UID"] . "_" . time() . "_Check.png";
    $Temp = $Connect->prepare("INSERT INTO Homeworkuploadchecklist(HomeworkUploadID, UploadUID, FileName) VALUES (?, ?, ?)");
    $Temp->bind_param("iis", $_POST["HomeworkUploadID"], $_SESSION["UID"], $File);
    $Temp->execute();
    if (!file_put_contents("HomeworkUploadCheckFile/" . $Temp->insert_id . "_" . $File, $ImageData)) {
        echo "系统错误：写入文件失败";
		CreateNewLine();
		die();
    }
    echo "OK";
?>
