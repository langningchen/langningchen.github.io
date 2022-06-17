<?php
    echo "<!DOCTYPE html>";
    echo "<html>";
    echo "<head>";
    if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") || strpos($_SERVER['HTTP_USER_AGENT'], "Triden")) {
        echo "<style>@import \"css/Ordinary.css\";</style>";
        echo "<style>@import \"Main.css\";</style>";
        echo "</head>";
        echo "<body>";
        CreateText("请不要使用IE浏览器，因为IE浏览器无法提供所需的支持。推荐使用的浏览器：");
        CreateLink("http://www.firefox.com.cn", "Mozilla火狐浏览器");
        CreateLink("http://www.microsoft.com/zh-cn/edge", "微软Edge浏览器");
        CreateLink("http://www.google.cn/intl/zh-CN/chrome", "谷歌Chrome浏览器");
        echo "</body>";
        echo "</html>";
        die();
    }
    if (isset($_SESSION["UID"]) && isset($_SESSION["UserName"]) && isset($_SESSION["UserType"])) {
        echo "<style>@import \"css/Ordinary.css\";</style>";
        echo "<style>@import \"Main.css\";</style>";
        echo "<script src=\"Count.js\"></script>";
        echo "</head>";
        echo "<body>";
        echo "<header>";
        echo "<div>";
        echo "</div>";
        echo "<ul>";
        echo "<li onclick=\"window.location='Index.php'\"><a>主页</a></li>";
        echo "<li onclick=\"window.location='Classes.php'\"><a>班级</a></li>";
        if ($_SESSION["UserType"] != "0") {
            echo "<li onclick=\"window.location='Upload.php'\"><a>文件</a></li>";
        }
        if ($_SESSION["UserType"] == "2") {
            echo "<li onclick=\"window.location='Config.php'\"><a>管理</a></li>";
        }
        echo "<li onclick=\"window.location='Chats.php'\"><a>私聊</a></li>";
        echo "<li onclick=\"window.location='Settings.php'\"><a>设置</a></li>";
        echo "<li onclick=\"window.location='Logout.php'\"><a>退出</a></li>";
        echo "</ul>";
        echo "</header>";
        echo "<br />";
    }
    else {
        echo "<style>@import \"css/Ordinary.css\";</style>";
        echo "<style>@import \"Main.css\";</style>";
        echo "</head>";
        echo "<body>";
    }
    require_once "Function.php";
?>
