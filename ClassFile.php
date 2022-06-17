<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    $ErrorMessage = "";
    if (!isset($_GET["ClassID"])) {
        echo "非法调用：没有传入ClassID参数";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Member = GetClassType($_GET["ClassID"]);
    if ($Member == "") {
        echo "非法调用：您不在当前班级中";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    if (isset($_POST["New"]) && isset($_POST["FileID"])) {
        if ($Member == "学生") {
            $ErrorMessage = "没有权限";
        }
        else {
            $Temp = $Connect->prepare("SELECT uploaduid FROM filelist WHERE ID=?");
            $Temp->bind_param("i", $_POST["FileID"]);
            $Temp->execute();
            $Result = $Temp->get_result();
            if ($Result->num_rows != 1) {
                $ErrorMessage = "没有该文件";
            }
            else {
                $Result->data_seek(0);
                $RowData = $Result->fetch_array(MYSQLI_NUM);
                if ($RowData[0] != $_SESSION["UID"]) {
                    $ErrorMessage = "必须是自己上传的文件才能设为班级文件";
                }
                else {
                    $Temp = $Connect->prepare("INSERT INTO classfilelist(ClassID, UID, FileID) VALUES (?,?,?)");
                    $Temp->bind_param("iii", $_GET["ClassID"], $_SESSION["UID"], $_POST["FileID"]);
                    $Temp->execute();
                }
            }
        }
    }
    if (isset($_POST["Delete"]) && isset($_POST["ClassFileID"])) {
        if ($Member == "学生") {
            $ErrorMessage = "没有权限";
        }
        else {
            $Temp = $Connect->prepare("DELETE FROM classfilelist WHERE ClassFileID=?");
            $Temp->bind_param("i", $_POST["ClassFileID"]);
            $Temp->execute();
        }
    }
    if ($Member != "学生") {
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        CreateText("请输入已上传文件的编号：");
        echo "<input type=\"number\" class=\"Input\" required=\"required\" name=\"FileID\" id=\"file\" />";
        echo "<input type=\"submit\" class=\"MainButton\" name=\"New\" value=\"添加\" />";
        echo "</form>";
    }
    CreateText($ErrorMessage);
    CreateNewLine();
    CreateNewLine();
    $Temp = $Connect->prepare("SELECT ClassFileID, UID, FileID FROM classfilelist");
    $Temp->execute();
    $Result = $Temp->get_result();
    CreateText("文件数量：");
    echo $Result->num_rows;
    CreateNewLine();
    echo "<table border=\"1\" style=\"width: 100%\">";
    echo "<thead>";
    echo "<td style=\"width: 20%\">";
    CreateText("上传者");
    echo "</td>";
    echo "<td style=\"width: 40%\">";
    CreateText("文件名");
    echo "</td>";
    echo "<td style=\"width: 20%\">";
    CreateText("文件大小");
    echo "</td>";
    echo "<td style=\"width: 20%\">";
    CreateText("操作");
    echo "</td>";
    echo "</thead>";
    echo "<tbody>";
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        echo "<tr>";
        echo "<td>";
        echo GetUserName($RowData[1]);
        echo "</td>";
        $Temp = $Connect->prepare("SELECT ID, filename, filesize FROM filelist WHERE ID=?");
        $Temp->bind_param("i", $RowData[2]);
        $Temp->execute();
        $TempResult = $Temp->get_result();
        if ($TempResult->num_rows == 0) {
            echo "<td>";
            CreateText("文件已被上传者删除");
            echo "</td>";
            echo "<td>";
            echo "</td>";
        }
        if ($TempResult->num_rows != 0) {
            $TempResult->data_seek($i);
            $TempRowData = $TempResult->fetch_array(MYSQLI_NUM);
            echo "<td>";
            CreateText($TempRowData[1]);
            echo "</td>";
            echo "<td>";
            CreateText($TempRowData[2]);
            echo "</td>";
        }
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        if ($Member != "学生") {
            echo "<input type=\"hidden\" name=\"ClassFileID\" value=\"" . $RowData[0] . "\" />";
            echo "<input class=\"DangerousButton\" name=\"Delete\" type=\"submit\" value=\"删除\" />";
        }
        if ($TempResult->num_rows != 0) {
			CreateDownload("UploadFile/" . $TempRowData[0] . "_" . $TempRowData[1], $TempRowData[1]);
        }
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "footer.php";
?>
