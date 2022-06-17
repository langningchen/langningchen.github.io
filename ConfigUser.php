<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if ($_SESSION["UserType"] != 2) {
        echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $RandPassword = "";
    $CalcedPassword = "";
    if (isset($_POST["Calc"])) {
        $RandPassword = $_POST["Password"];
        $CalcedPassword = EncodePassword($_POST["Password"]);
    }
    if (isset($_POST["New"])) {
        $Temp = $Connect->prepare("INSERT INTO userlist(UserName, Password, UserType) VALUES (?,?,?)");
        $Temp->bind_param("sss", $_POST["Username"], $_POST["Password"], $_POST["Type"]);
        $Temp->execute();
    }
    if (isset($_POST["Delete"])) {
        $Temp = $Connect->prepare("DELETE FROM userlist WHERE UID=?");
        $Temp->bind_param("s", $_POST["UID"]);
        $Temp->execute();
    }
    if (isset($_POST["ChangePassword"])) {
        $Temp = $Connect->prepare("UPDATE userlist SET Password=? WHERE UID=?");
        $Temp->bind_param("ss", $_POST["Password"], $_POST["UID"]);
        $Temp->execute();
    }
    if (isset($_POST["ChangeType"])) {
        $Temp = $Connect->prepare("UPDATE userlist SET UserType=? WHERE UID=?");
        $Temp->bind_param("ss", $_POST["Type"], $_POST["UID"]);
        $Temp->execute();
    }
    if (isset($_POST["RandPassword"])) {
        $RandPassword = CreateRandPassword();
    }
    $Temp = $Connect->prepare("SELECT * FROM userlist");
    $Temp->execute();
    $Result = $Temp->get_result();
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("密码计算：");
    echo "<input class=\"Input\" type=\"text\" name=\"Password\" placeholder=\"加密前的密码\" value=\"" . $RandPassword . "\" />";
    echo "<input class=\"MainButton\" type=\"submit\" name=\"Calc\" value=\"计算\" />";
    echo "<input class=\"SecondButton\" type=\"submit\" name=\"RandPassword\" value=\"生成\" />";
    CreateText($CalcedPassword);
    echo "</form>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("新增用户：");
    echo "<input class=\"Input\" type=\"text\" required=\"required\" name=\"Username\" placeholder=\"用户名\" />";
    echo "<input class=\"Input\" type=\"password\" required=\"required\" name=\"Password\" placeholder=\"加密后的密码\" />";
    echo "<input class=\"Input\" type=\"number\" required=\"required\" min=\"0\" max=\"3\" name=\"Type\" placeholder=\"用户类型\" />";
    echo "<input class=\"MainButton\" type=\"submit\" name=\"New\" value=\"增加\" />";
    echo "</form>";
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td style=\"width: 5%\">";
    CreateText("UID");
    echo "</td>";
    echo "<td style=\"width: 15%\">";
    CreateText("用户名");
    echo "</td>";
    echo "<td style=\"width: 20%\">";
    CreateText("密码");
    echo "</td>";
    echo "<td style=\"width: 5%\">";
    CreateText("用户类型");
    echo "</td>";
    echo "<td style=\"width: 15%\">";
    CreateText("最后登录时间");
    echo "</td>";
    echo "<td style=\"width: 40%\">";
    CreateText("操作");
    echo "</td>";
    echo "</thead>";
    echo "<tbody>";
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        echo "<tr>";
        echo "<td>";
        CreateText($RowData[0]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[1]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[2]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[3]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[4]);
        echo "</td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"UID\" value=\"$RowData[0]\" />";
        echo "<input class=\"Input\" type=\"text\" required=\"required\" name=\"Password\" value=\"$RowData[2]\" />";
        echo "<input class=\"DangerousButton\" type=\"submit\" name=\"ChangePassword\" value=\"更改密码\" />";
        echo "<input class=\"Input\" type=\"number\" required=\"required\" min=\"0\" max=\"3\" value=\"$RowData[3]\" name=\"Type\" />";
        echo "<input class=\"WarningButton\" type=\"submit\" name=\"ChangeType\" value=\"更改权限\" />";
        echo "<input class=\"DangerousButton\" type=\"submit\" name=\"Delete\" value=\"删除\" />";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "Footer.php";
?>
