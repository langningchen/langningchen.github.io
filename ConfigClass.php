<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if ($_SESSION["UserType"] != 2) {
        echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    if (isset($_POST["Delete"]) && isset($_POST["ClassID"])) {
        $Temp = $Connect->prepare("DELETE FROM classlist WHERE ClassID=?");
        $Temp->bind_param("i", $_POST["ClassID"]);
        $Temp->execute();
        
        $Temp = $Connect->prepare("DELETE FROM classfilelist WHERE ClassID=?");
        $Temp->bind_param("i", $_POST["ClassID"]);
        $Temp->execute();
        
        $Temp = $Connect->prepare("SELECT HomeworkID FROM Homeworklist WHERE ClassID=?");
        $Temp->bind_param("i", $_POST["ClassID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            DeleteHomework($RowData[0]);
        }
        $Temp = $Connect->prepare("DELETE FROM Homeworklist WHERE ClassID=?");
        $Temp->bind_param("i", $_POST["ClassID"]);
        $Temp->execute();
        
        $Temp = $Connect->prepare("SELECT ClockInID FROM clockinlist WHERE ClassID=?");
        $Temp->bind_param("i", $_POST["ClassID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            DeleteClockIn($RowData[0]);
        }
        $Temp = $Connect->prepare("DELETE FROM clockinlist WHERE ClassID=?");
        $Temp->bind_param("i", $_POST["ClassID"]);
        $Temp->execute();
    }
    $Temp = $Connect->prepare("SELECT * FROM classlist");
    $Temp->execute();
    $Result = $Temp->get_result();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td style=\"width: 5%\">";
    CreateText("编号");
    echo "</td>";
    echo "<td style=\"width: 5%\">";
    CreateText("名称");
    echo "</td>";
    echo "<td style=\"width: 5%\">";
    CreateText("管理员");
    echo "</td>";
    echo "<td style=\"width: 10%\">";
    CreateText("教师");
    echo "</td>";
    echo "<td style=\"width: 70%\">";
    CreateText("学生");
    echo "</td>";
    echo "<td style=\"width: 5%\">";
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
        echo GetUserName($RowData[2]);
        echo "</td>";
        echo "<td>";
		$TempArray = StringToArray($RowData[3]);
		for ($i = 0; $i < count($TempArray); $i++) {
			echo GetUserName($TempArray[$i]);
		}
        echo "</td>";
        echo "<td>";
		$TempArray = StringToArray($RowData[4]);
		for ($i = 0; $i < count($TempArray); $i++) {
			echo GetUserName($TempArray[$i]);
		}
        echo "</td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"ClassID\" value=\"" . $RowData[0] . "\" />";
        echo "<input class=\"DangerousButton\" type=\"submit\" name=\"Delete\" value=\"删除\" />";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "Footer.php";
?>
