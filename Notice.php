<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["NoticeID"])) {
        echo "非法调用：没有传入NoticeID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Temp = $Connect->prepare("SELECT * FROM notice WHERE NoticeID=?");
    $Temp->bind_param("s", $_GET["NoticeID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows == 0) {
        echo "非法调用：没有该公告。";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    else if ($Result->num_rows != 1) {
        echo "系统错误：有重复公告";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Result->data_seek(0);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    CreateText("编号：");
    CreateText($RowData[0]);
    CreateNewLine();
    CreateText("发布者：");
    echo GetUserName($RowData[1]);
    CreateNewLine();
    CreateText("发布时间：");
    CreateText($RowData[2]);
    CreateNewLine();
    CreateText("标题：");
    CreateText($RowData[3]);
    CreateNewLine();
    CreateText("内容：");
    CreateText($RowData[4]);
    CreateNewLine();
    require_once "Footer.php";
?>
