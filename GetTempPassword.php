<?php
    require_once "function.php";
    require_once "Header.php";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("请输入您的学号：");
    echo "<input class=\"Input\" type=\"number\" require=\"required\" min=\"1\" max=\"50\" name=\"StudentNumber\" />";
    CreateNewLine();
    CreateText("请输入您的学籍号（仅作为身份验证）：");
    echo "<input class=\"Input\" type=\"number\" require=\"required\" name=\"Number\" />";
    CreateNewLine();
    echo "<input class=\"MainButton\" type=\"submit\" value=\"查询\" />";
    echo "</form>";
    CreateNewLine();
    $ErrorMessage = "";
    if (isset($_POST["StudentNumber"]) && isset($_POST["Number"])) {
        $UserName = "23" . $_POST["StudentNumber"];
        $Temp = $Connect->prepare("SELECT Password FROM temppassword WHERE UserName=? AND Number=?");
        $Temp->bind_param("ss", $UserName, $_POST["Number"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        if ($Result->num_rows == 0) {
            $ErrorMessage = "没有查找到符合条件的用户";
        }
        else if ($Result->num_rows > 1) {
            $ErrorMessage = "系统错误：有多个信息重复的用户";
        }
        else {
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            CreateText("您的用户名是：“" . $UserName . "”。");
            CreateNewLine();
            CreateText("您的密码是：“" . SanitizeString($RowData[0]) . "”。");
            CreateNewLine();
            CreateText("注意：用户名和密码均不包括最外层的双引号！");
            CreateNewLine();
            CreateNewLine();
            CreateText("请在登录后进入设置并修改密码。");
            CreateNewLine();
            CreateText("在此之后，服务器不再会储存您的明文初始密码，所有密码（包括初始密码）将会被加密处理。");
            CreateNewLine();
            CreateNewLine();
            CreateText("<a class=\"Link\" href=\"Login.php\">点我去登录</a>");
        }
    }
    CreateNewLine();
    require_once "Footer.php";
?>
