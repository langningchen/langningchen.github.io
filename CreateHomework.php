<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    $ErrorMessage = "";
    $NeedUpload = $CanUploadAfterEnd = "1";
    $Data = "";
    $EndDate = date("Y-m-d");
    $EndTime = date("H:i:s");
    $Title = $EndDate . "的作业";
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
	if ($Member == "学生") {
		echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
	}
    if (isset($_POST["Title"]) && isset($_POST["Data"]) && isset($_POST["NeedUpload"]) && isset($_POST["CanUploadAfterEnd"]) && isset($_POST["EndDate"]) && isset($_POST["EndTime"])) {
        $Title = SanitizeString($_POST["Title"]);
        $Data = SanitizeString($_POST["Data"]);
        $NeedUpload = $_POST["NeedUpload"];
        $CanUploadAfterEnd = $_POST["CanUploadAfterEnd"];
        $EndDate = $_POST["EndDate"];
        $EndTime = $_POST["EndTime"];
        if ($_POST["Title"] != "" && $_POST["Data"] != "" && $_POST["EndDate"] != "" && $_POST["EndTime"] != "") {
            $_POST["Data"] = SanitizeString($_POST["Data"]);
            $_POST["EndTime"] = $_POST["EndDate"] . " " . $_POST["EndTime"];
            $Temp = $Connect->prepare("INSERT INTO Homeworklist(ClassID, UploadUID, Title, Data, CreateTime, EndTime, NeedUpload, CanUploadAfterEnd) VALUES (?, ?, ?, ?, current_timestamp(), ?, ?, ?)");
            $Temp->bind_param("iisssii", $_GET["ClassID"], $_SESSION["UID"], $_POST["Title"], $_POST["Data"], $_POST["EndTime"], $_POST["NeedUpload"], $_POST["CanUploadAfterEnd"]);
            $Temp->execute();
            $ErrorMessage = "<script>window.location=\"Homeworks.php?ClassID=" . $_GET["ClassID"] . "\"</script>";
        }
        else {
            $ErrorMessage = "请填写完整";
        }
    }
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input class=\"Input\" type=\"text\" name=\"Title\" require=\"required\" placeholder=\"标题\" value=\"" . $Title . "\"/>";
    CreateNewLine();
    echo "<textarea class=\"Input\" name=\"Data\" require=\"required\" placeholder=\"内容\">" . $Data . "</textarea>";
    CreateNewLine();
    CreateText("是否需要提交：");
    echo "<select class=\"SecondButton\" name=\"NeedUpload\" value=\"" . $NeedUpload . "\">";
    echo "<option value=\"1\">需要</option>";
    echo "<option value=\"0\">不需要</option>";
    echo "</select>";
    CreateNewLine();
    CreateText("是否允许补交：");
    echo "<select class=\"SecondButton\" name=\"CanUploadAfterEnd\" value=\"" . $CanUploadAfterEnd . "\">";
    echo "<option value=\"1\">允许</option>";
    echo "<option value=\"0\">不允许</option>";
    echo "</select>";
    CreateText("（不需提交时此项随便填，系统忽略）");
    CreateNewLine();
    CreateText("截止时间：");
    echo "<input class=\"Input\" type=\"date\" require=\"required\" name=\"EndDate\" value=\"" . $EndDate . "\" />";
    echo "<input class=\"Input\" type=\"time\" require=\"required\" name=\"EndTime\" />";
    CreateText("（不需提交时此项随便填，系统忽略）");
    CreateNewLine();
    echo "<input class=\"MainButton\" type=\"submit\" name=\"Create\" value=\"发布\" />";
    echo "</form>";
    CreateNewLine();
    CreateText("提示：因为技术原因，如需添加附件请上传至班级文件并在正文中使用文字说明。");
    CreateNewLine();
    CreateNewLine();
    CreateText($ErrorMessage);
    CreateNewLine();
    require_once "footer.php";
?>
