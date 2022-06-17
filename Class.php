<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if (!isset($_GET["ClassID"])) {
        echo "非法调用：没有传入ClassID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Temp = $Connect->prepare("SELECT * FROM classlist WHERE ClassID=?");
    $Temp->bind_param("i", $_GET["ClassID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    if ($Result->num_rows == 0) {
        echo "非法调用：没有此班级";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Result->data_seek(0);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    $Member = GetClassType($_GET["ClassID"]);
    if ($Member == "") {
        echo "非法调用：您不在当前班级中";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    CreateText("班级编号：");
    echo $RowData[0];
    CreateNewLine();
    CreateText("班级名称：");
    echo $RowData[1];
    CreateNewLine();
    CreateText("班级管理员：");
    echo GetUserName($RowData[2]);
    CreateNewLine();
    CreateText("班级教师：");
	$TempArray = StringToArray($RowData[3]);
	for ($i = 0; $i < count($TempArray); $i++) {
        echo GetUserName($TempArray[$i]) . " ";
	}
    CreateNewLine();
    CreateText("班级学生：");
	$TempArray = StringToArray($RowData[4]);
	for ($i = 0; $i < count($TempArray); $i++) {
        echo GetUserName($TempArray[$i]) . " ";
	}
    CreateNewLine();
    CreateNewLine();
    CreateLink("ClassFile.php?ClassID=" . $_GET["ClassID"], "班级文件");
    CreateNewLine();
    CreateLink("Homeworks.php?ClassID=" . $_GET["ClassID"], "班级作业");
    CreateNewLine();
    CreateLink("ClockIns.php?ClassID=" . $_GET["ClassID"], "班级打卡");
    CreateNewLine();
    require_once "Footer.php";
?>
