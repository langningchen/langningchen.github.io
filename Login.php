<?php
    require_once "Function.php";
    session_start();
    $ErrorMessage = $UserName = $Password = "";
    if (isset($_POST["UserName"])) {
        $UserName = $_POST["UserName"];
    }
    if (isset($_POST["Password"])) {
        $Password = $_POST["Password"];
    }
    if (isset($_SESSION["UserName"]) && isset($_SESSION["UID"]) && isset($_SESSION["UserType"])) {
        $ErrorMessage = "<script>window.location = \"index.php\";</script>";
    }
    if (isset($_COOKIE["UserName"]) && isset($_COOKIE["Password"])) {
        $Temp = $Connect->prepare("SELECT UID,UserType FROM UserList WHERE UserName=? AND Password=?");
        $Temp->bind_param("ss", $_COOKIE["UserName"], $_COOKIE["Password"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        if ($Result->num_rows == 1) {
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            $_SESSION["UID"] = $RowData[0];
            $_SESSION["UserName"] = $_COOKIE["UserName"];
            $_SESSION["UserType"] = strval($RowData[1]);
            $Temp = $Connect->prepare("UPDATE userlist SET LastLoginTime=? WHERE UID=?");
            $NowTime = date("Y-m-d H:i:s");
            $Temp->bind_param("ss", $NowTime, $_SESSION["UID"]);
            $Temp->execute();
            $ErrorMessage = "<script>window.location = \"index.php\";</script>";
        }
        else {
            setcookie("UserName", "", time() - 1);
            setcookie("Password", "", time() - 1);
            setcookie("UID", "", time() - 1);
            setcookie("UserType", "", time() - 1);
        }
        $Result->close();
    }
    else if (isset($_SESSION["AuthCode"]) && isset($_POST["AuthCode"])) {
        if ($_SESSION["AuthCode"] != $_POST["AuthCode"]) {
            $ErrorMessage = "验证码错误";
        }
        else if ($UserName != "" && $Password != "") {
            $Temp = $Connect->prepare("SELECT UID,UserType FROM UserList WHERE UserName=? AND Password=?");
            $Password = EncodePassword($Password);
            $Temp->bind_param("ss", $UserName, $Password);
            $Temp->execute();
            $Result = $Temp->get_result();
            if ($Result->num_rows == 0) {
                $ErrorMessage = "用户名或密码错误！";
            }
            else if ($Result->num_rows == 1) {
                $Result->data_seek(0);
                $RowData = $Result->fetch_array(MYSQLI_NUM);

                $_SESSION["UID"] = $RowData[0];
                $_SESSION["UserName"] = $UserName;
                $_SESSION["UserType"] = $RowData[1];
                
                if (isset($_POST["AutoLogin"])) {
                    setcookie("UserName", $UserName, time() + 60 * 60 * 24 * 7);
                    setcookie("Password", $Password, time() + 60 * 60 * 24 * 7);
                    setcookie("UID", $RowData[0], time() + 60 * 60 * 24 * 7);
                    setcookie("UserType", $RowData[1], time() + 60 * 60 * 24 * 7);
                }
                $Temp = $Connect->prepare("UPDATE userlist SET LastLoginTime=current_timestamp() WHERE UID=?");
                $Temp->bind_param("s", $_SESSION["UID"]);
                $Temp->execute();
                $ErrorMessage = "<script>window.location = 'index.php';</script>";
            }
            else {
                $ErrorMessage = "发生系统错误：有重名用户";
            }
            $Result->close();
        }
        else {
            $ErrorMessage = "请填写完整";
        }
    }
    require_once "Header.php";
    echo "<div class=\"LoginForm\">";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<div class=\"LoginInput\">";
    CreateText("用户名");
    echo "<input class=\"Input\" type=\"text\" value=\"$UserName\" name=\"UserName\" />";
    echo "</div>";
    echo "<div class=\"LoginInput\">";
    CreateText("密&emsp;码");
    echo "<input class=\"Input\" type=\"password\" name=\"Password\" />";
    echo "</div>";
    echo "<div class=\"LoginInput\">";
    CreateText("验证码");
    echo "<input class=\"Input\" type=\"text\" name=\"AuthCode\" />";
    echo "<img src=\"GetPicture.php\" style=\"position: relative; top: 10px;\" class=\"AuthCodePic\" id=\"AuthCodePic\"></img>";
    echo "</div>";
    CreateNewLine();
    echo "<div>";
    echo "<input type=\"checkbox\" checked=\"checked\" name=\"AutoLogin\" />";
    CreateText("7天内自动登录");
    echo "</div>";
    CreateNewLine();
    echo "<input class=\"MainButton\" type=\"submit\" value=\"确定\" />";
    echo "</form>";
    CreateNewLine();
    CreateText($ErrorMessage);
    CreateNewLine();
    CreateNewLine();
    echo "<div style=\"font-size: 10px\">";
    CreateText("首次登录，不知道密码？");
    CreateLink("GetTempPassword.php", "点我获取初始密码");
    echo "</div>";
    echo "</div>";
    echo "<script>";
    echo "document.getElementById(\"AuthCodePic\").onclick = function () { this.src = \"GetPicture.php?r=\" + Math.random(); }";
    echo "</script>";
    require_once "footer.php";
?>
