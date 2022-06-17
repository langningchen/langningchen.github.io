<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if ($_SESSION["UserType"] == 0) {
        echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $ErrorMessage = "";
    if (isset($_FILES["file"])) {
        if ($_FILES["file"]["error"] != 0)
        {
            $ErrorMessage = "上传失败，错误码：" . $_FILES["file"]["error"];
        }
        else
        {
            $Temp = $Connect->prepare("INSERT INTO filelist(uploaduid, filename, filetype, filesize) VALUES (?,?,?,?)");
            $Temp->bind_param("issi", $_SESSION["UID"], $_FILES["file"]["name"], $_FILES["file"]["type"], $_FILES["file"]["size"]);
            $Temp->execute();
            move_uploaded_file($_FILES["file"]["tmp_name"], "UploadFile/" . $Temp->insert_id . "_" . $_FILES["file"]["name"]);
        }
    }
    if (isset($_POST["Delete"]) && isset($_POST["ID"])) {
        $Temp = $Connect->prepare("SELECT filename FROM filelist WHERE ID=?");
        $Temp->bind_param("i", $_POST["ID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        $Temp = $Connect->prepare("DELETE FROM filelist WHERE ID=?");
        $Temp->bind_param("i", $_POST["ID"]);
        $Temp->execute();
        if (file_exists("UploadFile/" . $_POST["ID"] . "_" . $RowData[0])) {
            if (!unlink("UploadFile/" . $_POST["ID"] . "_" . $RowData[0])) {
                $ErrorMessage = "删除失败";
            }
        }
        else {
            $ErrorMessage = "删除失败：没有该文件，已将该文件记录删除";
        }
    }
    if (isset($_POST["Rename"]) && isset($_POST["ID"]) && isset($_POST["AfterFileName"])) {
        $Temp = $Connect->prepare("SELECT filename FROM filelist WHERE ID=?");
        $Temp->bind_param("s", $_POST["ID"]);
        $Temp->execute();
        $Result = $Temp->get_result();
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if (file_exists("UploadFile/" . $_POST["ID"] . "_" . $RowData[0])) {
            rename("UploadFile/" . $_POST["ID"] . "_" . $RowData[0], "UploadFile/" . $_POST["ID"] . "_" . $_POST["AfterFileName"]);
            $Temp = $Connect->prepare("UPDATE filelist SET filename=? WHERE ID=?");
            $Temp->bind_param("ss", $_POST["AfterFileName"], $_POST["ID"]);
            $Temp->execute();
        }
        else {
            $ErrorMessage = "重命名失败：没有该文件，已将该文件记录删除";
            $Temp = $Connect->prepare("DELETE FROM filelist WHERE ID=?");
            $Temp->bind_param("i", $_POST["ID"]);
            $Temp->execute();
        }
    }
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\" enctype=\"multipart/form-data\">";
    echo "<input class=\"Input\" type=\"file\" name=\"file\" id=\"file\" required=\"required\" />";
    echo "<input class=\"MainButton\" type=\"submit\" name=\"submit\" value=\"上传\" />";
    echo "</form>";
    CreateNewLine();
    CreateText($ErrorMessage);
    CreateNewLine();
    $Temp = $Connect->prepare("SELECT filename, filesize, ID FROM filelist WHERE uploaduid=?");
    $Temp->bind_param("i", $_SESSION["UID"]);
    $Temp->execute();
    $Result = $Temp->get_result();
    CreateText("<table border=\"1\" style=\"width: 100%\">");
    CreateText("<thead>");
    echo "<td style=\"width: 10%\">";
    CreateText("编号");
    echo "</td>";
    echo "<td style=\"width: 40%\">";
    CreateText("文件名");
    echo "</td>";
    echo "<td style=\"width: 20%\">";
    CreateText("文件大小");
    echo "</td>";
    echo "<td style=\"width: 30%\">";
    CreateText("操作");
    echo "</td>";
    CreateText("</thead>");
    CreateText("<tbody>");
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        echo "<tr>";
        echo "<td>";
        CreateText($RowData[2]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[0]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[1]);
        echo "</td>";
        echo "<td>";
        echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"ID\" value=\"$RowData[2]\" />";
        echo "<input class=\"DangerousButton\" name=\"Delete\" type=\"submit\" value=\"删除\" />";
        echo "<input class=\"Input\" type=\"text\" required=\"required\" name=\"AfterFileName\" value=\"$RowData[0]\" />";
        echo "<input class=\"SecondButton\" type=\"submit\" name=\"Rename\" value=\"重命名\" />";
		CreateDownload("UploadFile/" . $RowData[2] . "_" . $RowData[0], $RowData[0]);
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    require_once "footer.php";
?>
