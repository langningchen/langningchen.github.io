<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if ($_SESSION["UserType"] != 2) {
        echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    CreateLink("ConfigUser.php", "管理用户");
    CreateNewLine();
    CreateLink("ConfigClass.php", "管理班级");
    CreateNewLine();
    CreateLink("ConfigChat.php", "管理聊天");
    CreateNewLine();
    CreateLink("ConfigNotice.php", "管理公告");
    CreateNewLine();
    CreateLink("ConfigDatabase.php", "管理数据");
    CreateNewLine();
    require_once "Footer.php";
?>
