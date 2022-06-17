<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["UID"])) {
        echo "非法调用：没有传入UID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Temp = $Connect->prepare("SELECT * FROM userlist WHERE UID=?");
    $Temp->bind_param("i", $_GET["UID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows == 0) {
        echo "非法调用：没有该用户";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    else if ($Result->num_rows == 1) {
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        CreateText("用户编号：");
        CreateText($RowData[0]);
        CreateNewLine();
        CreateText("用户名：");
        CreateText($RowData[1]);
        CreateNewLine();
        CreateText("用户类型：");
        switch ($RowData[3]) {
            case "0":
                CreateText("普通用户");
                break;
            case "1":
                CreateText("教师");
                break;
            case "2":
                CreateText("管理员");
                break;
            case "3":
                CreateText("封禁用户");
                break;
        }
    }
    CreateNewLine();
    require_once "footer.php";
?>
