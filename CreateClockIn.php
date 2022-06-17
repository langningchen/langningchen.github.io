<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    $ErrorMessage = "";
    $NeedUpload = $CanUploadAfterEnd = "1";
    $Data = "";
    $EndDate = date("Y-m-d");
    $Title = $EndDate . "的打卡";
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
    if (isset($_POST["Title"]) && isset($_POST["Data"]) && isset($_POST["CanUploadAfterEnd"]) && isset($_POST["EndDate"])) {
        $Title = SanitizeString($_POST["Title"]);
        $Data = SanitizeString($_POST["Data"]);
        $CanUploadAfterEnd = $_POST["CanUploadAfterEnd"];
        $EndDate = $_POST["EndDate"];
        if ($_POST["Title"] != "" && $_POST["Data"] != "" && $_POST["EndDate"] != "") {
            $_POST["Data"] = SanitizeString($_POST["Data"]);
            $Temp = $Connect->prepare("INSERT INTO clockinlist(ClassID, UploadUID, Title, Data, CreateTime, EndTime, CanUploadAfterEnd) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $Today = date("Y-m-d");
            $Temp->bind_param("iissssi", $_GET["ClassID"], $_SESSION["UID"], $_POST["Title"], $_POST["Data"], $Today, $_POST["EndDate"], $_POST["CanUploadAfterEnd"]);
            $Temp->execute();
            $ErrorMessage = "<script>window.location=\"ClockIns.php?ClassID=" . $_GET["ClassID"] . "\"</script>";
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
    CreateText("是否允许补打卡：");
    echo "<select class=\"SecondButton\" name=\"CanUploadAfterEnd\" value=\"" . $CanUploadAfterEnd . "\">";
    echo "<option value=\"1\">允许</option>";
    echo "<option value=\"0\">不允许</option>";
    echo "</select>";
    CreateNewLine();
    CreateText("截止日期：");
    echo "<input class=\"Input\" type=\"date\" require=\"required\" name=\"EndDate\" value=\"" . $EndDate . "\" />";
    CreateNewLine();
    echo "<input class=\"MainButton\" type=\"submit\" name=\"Create\" value=\"发布\" />";
    echo "</form>";
    CreateNewLine();
    if ($ErrorMessage != "") {
        CreateText($ErrorMessage);
        CreateNewLine();
    }
    require_once "footer.php";
?>
