<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    $ErrorMessage = "";
    if (isset($_POST["ChangePassword"]) && isset($_POST["Password"]) && isset($_POST["CopyPassword"])) {
        if ($_POST["Password"] != $_POST["CopyPassword"]) {
            $ErrorMessage = "两次输入的密码不同";
        }
        $Temp = $Connect->prepare("UPDATE userlist SET Password=? WHERE UID=?");
        $TempPassword = EncodePassword($_POST["Password"]);
        $Temp->bind_param("ss", $TempPassword, $_SESSION["UID"]);
        $Temp->execute();
        $ErrorMessage = "更改成功";
    }
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("更改密码：");
    CreateNewLine();
    echo "<input class=\"Input\" type=\"password\" required=\"required\" name=\"Password\" placeholder=\"新密码\" />";
    CreateNewLine();
    echo "<input class=\"Input\" type=\"password\" required=\"required\" name=\"CopyPassword\" placeholder=\"重复一遍新密码\" />";
    CreateNewLine();
    echo "<input class=\"DangerousButton\" type=\"submit\" name=\"ChangePassword\" value=\"更改\" />";
    echo "</form>";
    CreateText($ErrorMessage);
    require_once "Footer.php";
?>
